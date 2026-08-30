# Fish Breed Avatar Art Guide & Generation Prompts

This document defines the standard art style, composition rules, color specifications, and image generation prompt catalog for the **Fishing Logbook** fish avatar asset library located in `public/images/fish/avatars/`.

---

## 🎨 Master Aesthetic Standard

All species avatars must strictly follow these aesthetic invariants to ensure visual unity across data tables, catch cards, dossiers, and brag boards:

1. **Composition & Orientation**:
   - **Full Head Portrait**: Dramatic 3/4 close-up headshot filling **85%–90%** of the circle.
   - **Facing Direction**: Left-facing (head in bottom-left/center, gills and dorsal fin tapering up-right).
   - **Expression**: Dynamic predatory/alert posture (open or semi-open jaw showcasing authentic mouth/teeth anatomy).
2. **Background & Framing**:
   - **Canvas**: Solid square aspect ratio (`1:1`).
   - **Circle Background**: Solid muted slate-blue (`#5B6B7C` / `#475569`).
   - **Border**: Double concentric circular border ring in neutral slate-grey (`#334155` / `#64748B`).
   - **Outer Border Margin**: Clean white outer square canvas surrounding the circular emblem.
3. **Rendering & Line Style**:
   - **Art Style**: Crisp vector illustration / esports-grade wildlife emblem art.
   - **Line Weight**: Bold, dark contour outlines with clean secondary anatomical details.
   - **Shading**: High-contrast cel-shading with specular highlights, scale cross-hatching, and glassy eye reflection catchlights.
   - **No Text**: No lettering, watermarks, or typography inside the badge.

---

## 📐 Master Prompt Template

```text
A close-up head portrait circular avatar of a trophy [SPECIES_COMMON_NAME] ([SPECIES_SCIENTIFIC_NAME]), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. [ANATOMICAL_COLORING_AND_PATTERNS], [MOUTH_AND_TEETH_FEATURES], [CHEEK_GILL_AND_DORSAL_DETAILS], and a vivid [EYE_COLOR_AND_SHINE] with a sharp white highlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

## 🐟 Species Generation Prompts

### 1. Smallmouth Bass (*Micropterus dolomieu*) - [ACTIVE]
**File**: `smallmouth_bass.jpg`  
**Reference Assets**: `largemouth_bass.jpg`, `walleye.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Smallmouth Bass (Micropterus dolomieu), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Rich golden-bronze and dark olive-brown scales, distinct dark vertical tiger-stripe bars along the body, 3 dark radiating cheek stripes behind a vivid reddish-amber eye with a sharp white highlight reflection. Aggressive open mouth showing the authentic smallmouth jawline (maxilla terminating beneath the eye), textured sandpaper lip pads, detailed flared operculum (gill cover), and spiny dorsal fin crest rising at the top right. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 2. Largemouth Bass (*Micropterus salmoides*) - [ACTIVE]
**File**: `largemouth_bass.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Largemouth Bass (Micropterus salmoides), styled in clean vector line art. The fish head faces to the left, filling the circular emblem. Deep olive-green and pale belly scales with dark irregular lateral blotches, prominent massive open bucketmouth with upper jaw extending well past the rear of the eye, fine sandpaper tooth pads on the jaws and tongue, flared olive operculum, and a striking golden-olive eye with dark pupil and specular catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, on a white outer canvas. Bold contours, crisp cel-shading.
```

---

### 3. Walleye (*Sander vitreus*) - [ACTIVE]
**File**: `walleye.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Walleye (Sander vitreus), styled in clean vector line art. The fish head faces to the left, filling the circular emblem. Golden-olive and brassy-yellow scales with dark olive mottling, large opaque glassy tapetum lucidum eye with bright silver-white glow, sharp canine predatory teeth protruding from the upper and lower jaws, flared spiny dorsal fin crest with dark spots rising at the top right, and white opercular accents. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, on a white outer canvas. Bold contours, crisp cel-shading.
```

---

### 4. Northern Pike (*Esox lucius*) - [READY]
**Target File**: `northern_pike.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Northern Pike (Esox lucius), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Elongated duckbill-shaped predatory snout with menacing open jaws displaying rows of needle-sharp canine teeth, dark forest-green head and body adorned with pale creamy-yellow bean-shaped horizontal spots, dark horizontal cheek barring, and an intense yellow-gold eye with horizontal pupil and sharp catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 5. Muskellunge (*Esox masquinongy*) - [READY]
**Target File**: `muskellunge.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a monster Muskellunge (Esox masquinongy), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Massive elongated predatory duckbill snout with fearsome open jaws lined with large dagger-like teeth, light silver-green and olive-bronze scales marked with distinct dark vertical tiger bars and spots across the cheeks and gill covers (sensory pores on lower jaw), and a piercing golden-yellow eye with specular catchlight. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 6. Brook Trout (*Salvelinus fontinalis*) - [READY]
**Target File**: `brook_trout.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy native Brook Trout (Salvelinus fontinalis), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Deep olive-green head with distinct yellowish vermiculations (worm-like markings) across the back and head, vibrant crimson red and yellow spots surrounded by pale blue halos along the flank, hooked kype lower jaw on dark char lip, and a luminous dark eye with golden iris ring and catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 7. Brown Trout (*Salmo trutta*) - [READY]
**Target File**: `brown_trout.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy hook-jawed Brown Trout (Salmo trutta), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Rich golden-brown and buttery-yellow head and body covered in large black spots and bright red spots encircled by pale blue halos, masculine hooked kype lower jaw, prominent operculum with dark spots, and a sharp amber-gold eye with specular catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 8. Rainbow Trout / Steelhead (*Oncorhynchus mykiss*) - [READY]
**Target File**: `rainbow_trout_steelhead.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Rainbow Trout / Steelhead (Oncorhynchus mykiss), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Shimmering silver-green and iridescent head with a vivid broad crimson-pink lateral band running across the operculum, profusely peppered with fine black spots across the head, cheeks, and dorsal ridge, clean trout jawline with subtle kype, and an alert metallic silver-gold eye with crisp catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 9. Lake Trout (*Salvelinus namaycush*) - [READY]
**Target File**: `lake_trout.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a deep-water trophy Lake Trout (Salvelinus namaycush), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Dark slate-grey and olive-brown head profusely covered with pale cream-colored irregular spots, deeply cleft predatory mouth with sharp teeth and pale lip line, robust char gill plates, and a dark cool metallic eye with crisp white highlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 10. Yellow Perch (*Perca flavescens*) - [READY]
**Target File**: `yellow_perch.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a jumbo Yellow Perch (Perca flavescens), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Brilliant golden-yellow body with 6-8 bold dark olive-green vertical wedge bars, bright orange-tinted pelvic/pectoral fins and opercular edges, sharp spiny dorsal crest raised at the top right, and a bright alert amber eye with dark pupil and specular catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 11. Bluegill / Sunfish (*Lepomis macrochirus*) - [READY]
**Target File**: `bluegill.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a bull Bluegill / Sunfish (Lepomis macrochirus), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Deep round disk-shaped head with rich olive-green, copper, and royal blue iridescent streaks on the cheek and jaw, prominent deep black ear flap (opercular lobe), warm coppery-orange chest, and a bold dark eye with metallic rim and specular catchlight. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 12. Chinook Salmon (King) (*Oncorhynchus tshawytscha*) - [READY]
**Target File**: `chinook_king.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Chinook King Salmon (Oncorhynchus tshawytscha), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Massive powerful gunmetal-silver and dark slate head, distinctive solid black lower gums (black mouth line), hooked predatory kype jaw with sharp teeth, heavy black spots scattered across the upper head and back, and a fierce dark eye with silver rim and specular catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 13. Coho Salmon (Silver) (*Oncorhynchus kisutch*) - [READY]
**Target File**: `choho_silver.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Coho Silver Salmon (Oncorhynchus kisutch), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Brilliant bright chrome-silver and metallic blue-green head with characteristic white gums and black tongue, hooked predatory kype jaw lined with sharp teeth, fine black spotting on the upper back and dorsal ridge, and an intense silver-grey eye with specular highlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 14. Atlantic Salmon (*Salmo salar*) - [READY]
**Target File**: `atlantic.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Atlantic Salmon (Salmo salar), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Sleek metallic steel-blue and silver head with distinct black X-shaped and cross-shaped spots on the gill covers and upper flank, elegant kype jaw extending just to the rear edge of the eye, and a sharp luminous silver-blue eye with catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 15. Splake (*Salvelinus fontinalis x Salvelinus namaycush*) - [READY]
**Target File**: `splake.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Splake hybrid (Brook Trout x Lake Trout hybrid), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Dark olive-green and slate head combining subtle yellowish vermiculations and cream/pink spots, intermediate char jawline with sharp teeth, pale opercular borders, and a luminous metallic eye with crisp white highlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 16. Rock Bass (*Ambloplites rupestris*) - [READY]
**Target File**: `rock_bass.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a trophy Rock Bass (Ambloplites rupestris), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Compact brassy-bronze and olive head with dark black-brown mottled saddle blotches in broken horizontal rows, prominent blood-red "goggle eye" iris with a bright white catchlight reflection, large open bass mouth, and dark opercular spot. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```

---

### 17. Pink Salmon (*Oncorhynchus gorbuscha*) - [READY]
**Target File**: `pink_salmon.jpg`  
**Prompt**:
```text
A close-up head portrait circular avatar of a spawning Pink Salmon (Oncorhynchus gorbuscha), styled in the exact same art style, line weight, shading, and background as the reference images. The fish head faces to the left, filling the circular emblem. Distinctive exaggerated hooked predatory kype snout and humpback ridge, olive-green head with large dark oval spots across the back and dorsal fin, white lower jaw gums with dark tongue, and an alert dark eye with metallic rim and specular catchlight reflection. Rendered against a solid muted cool slate-blue circular background (#5b6b7c) enclosed by a circular slate-grey double border ring, set on a pure white outer canvas. Crisp vector line art, immaculate cel-shading highlights, bold contours.
```
