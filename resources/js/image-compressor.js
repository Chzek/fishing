/**
 * Client-Side Offline Image Compressor
 * Resizes large camera photos to max 1600px width/height and compresses to quality JPEG/WebP.
 * Works 100% offline in browser without external dependencies.
 */

window.compressImageFile = async function (file, options = {}) {
    const maxWidth = options.maxWidth || 1600;
    const maxHeight = options.maxHeight || 1600;
    const quality = options.quality !== undefined ? options.quality : 0.82;
    const outputType = options.outputType || 'image/jpeg';

    // If file is not an image, return as-is
    if (!file || !file.type.startsWith('image/')) {
        return file;
    }

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        if (!blob || blob.size >= file.size) {
                            // If compression didn't save space or failed, keep original
                            resolve(file);
                            return;
                        }

                        const compressedFileName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                        const compressedFile = new File([blob], compressedFileName, {
                            type: outputType,
                            lastModified: Date.now(),
                        });
                        resolve(compressedFile);
                    },
                    outputType,
                    quality
                );
            };

            img.onerror = () => resolve(file);
            img.src = e.target.result;
        };

        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
    });
};

/**
 * Initializes interactive photo upload dropzone with auto-compression.
 */
window.initPhotoUploader = function (containerId, options = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const fileInput = container.querySelector('input[type="file"]');
    const previewGrid = container.querySelector('.photo-preview-grid');
    const statusText = container.querySelector('.photo-status-text');
    const multiple = fileInput.hasAttribute('multiple');

    let stagedFiles = [];
    const dataTransfer = new DataTransfer();

    async function handleFiles(files) {
        if (!files || files.length === 0) return;

        if (statusText) {
            statusText.textContent = `Optimizing ${files.length} photo(s)...`;
            statusText.classList.remove('hidden');
        }

        for (let i = 0; i < files.length; i++) {
            const rawFile = files[i];
            if (!rawFile.type.startsWith('image/')) continue;

            const compressed = await window.compressImageFile(rawFile, options);

            if (!multiple) {
                stagedFiles = [compressed];
            } else {
                stagedFiles.push(compressed);
            }
        }

        syncInputAndRender();

        if (statusText) {
            statusText.textContent = `${stagedFiles.length} photo(s) ready for upload`;
            setTimeout(() => {
                if (statusText) statusText.classList.add('hidden');
            }, 3000);
        }
    }

    function syncInputAndRender() {
        const newDt = new DataTransfer();
        stagedFiles.forEach((file) => newDt.items.add(file));
        fileInput.files = newDt.files;

        if (!previewGrid) return;
        previewGrid.innerHTML = '';

        if (stagedFiles.length === 0) {
            previewGrid.classList.add('hidden');
            return;
        }

        previewGrid.classList.remove('hidden');

        stagedFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'relative group aspect-square rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-full h-full object-cover';

            const badge = document.createElement('span');
            badge.className = 'absolute bottom-1 left-1 bg-slate-900/75 text-[10px] text-white font-mono px-1.5 py-0.5 rounded backdrop-blur-xs';
            badge.textContent = `${Math.round(file.size / 1024)} KB`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 w-6 h-6 rounded-full bg-rose-600/90 text-white flex items-center justify-center text-xs opacity-90 hover:opacity-100 hover:scale-110 transition shadow-sm';
            removeBtn.innerHTML = '✕';
            removeBtn.onclick = (e) => {
                e.stopPropagation();
                stagedFiles.splice(index, 1);
                syncInputAndRender();
            };

            card.appendChild(img);
            card.appendChild(badge);
            card.appendChild(removeBtn);
            previewGrid.appendChild(card);
        });
    }

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Drag and drop handlers
    const dropZone = container.querySelector('.photo-drop-zone') || container;
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-teal-500', 'bg-teal-50/50');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-teal-500', 'bg-teal-50/50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-teal-500', 'bg-teal-50/50');
        if (e.dataTransfer.files) {
            handleFiles(e.dataTransfer.files);
        }
    });
};
