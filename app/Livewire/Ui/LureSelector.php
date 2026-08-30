<?php

namespace Fishinglog\Livewire\Ui;

use Fishinglog\Models\Lure;
use Livewire\Component;

class LureSelector extends Component
{
    public string $name = 'lures_id';
    public int|string|null $selectedId = null;
    public string $search = '';
    public string $selectedCategory = 'all';
    public string $placeholder = 'Search lures by brand, model, color, category...';
    public bool $required = false;
    public bool $allowClear = true;
    public bool $disabled = false;
    public bool $isOpen = false;

    public function mount(
        ?string $name = 'lures_id',
        $selectedId = null,
        ?string $placeholder = null,
        bool $required = false,
        bool $allowClear = true,
        bool $disabled = false
    ): void {
        $this->name = $name ?? 'lures_id';
        $this->selectedId = !empty($selectedId) ? (string)$selectedId : null;
        if ($placeholder !== null) {
            $this->placeholder = $placeholder;
        }
        $this->required = $required;
        $this->allowClear = $allowClear;
        $this->disabled = $disabled;
    }

    public function updatedSearch(): void
    {
        $this->isOpen = true;
    }

    public function selectLure($lureId): void
    {
        $this->selectedId = !empty($lureId) ? (string)$lureId : null;
        $this->search = '';
        $this->isOpen = false;
        $this->dispatch('lure-selected', id: $this->selectedId);
    }

    public function clearSelection(): void
    {
        $this->selectedId = null;
        $this->search = '';
        $this->isOpen = false;
        $this->dispatch('lure-cleared');
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = $category;
        $this->isOpen = true;
    }

    public function toggleOpen(): void
    {
        if ($this->disabled) {
            return;
        }
        $this->isOpen = !$this->isOpen;
    }

    public function openDropdown(): void
    {
        if ($this->disabled) {
            return;
        }
        $this->isOpen = true;
    }

    public function closeDropdown(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $selectedLure = $this->selectedId
            ? Lure::withCount('records')->find($this->selectedId)
            : null;

        $categories = Lure::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        $query = Lure::query()->withCount('records');

        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        $searchTrimmed = trim($this->search);
        if ($searchTrimmed !== '') {
            $query->where(function ($q) use ($searchTrimmed) {
                $q->where('name', 'like', "%{$searchTrimmed}%")
                    ->orWhere('brand', 'like', "%{$searchTrimmed}%")
                    ->orWhere('color', 'like', "%{$searchTrimmed}%")
                    ->orWhere('category', 'like', "%{$searchTrimmed}%");
            });
        }

        $lures = $query->orderBy('category', 'asc')
            ->orderBy('brand', 'asc')
            ->orderBy('name', 'asc')
            ->orderBy('color', 'asc')
            ->get();

        $groupedLures = $lures->groupBy(fn ($item) => $item->category ?: 'Other');

        return view('livewire.ui.lure-selector', [
            'selectedLure' => $selectedLure,
            'categories' => $categories,
            'groupedLures' => $groupedLures,
            'totalResults' => $lures->count(),
        ]);
    }
}
