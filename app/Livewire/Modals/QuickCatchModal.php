<?php

namespace Fishinglog\Livewire\Modals;

use Fishinglog\Actions\Records\CreateCatchRecordAction;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Notifications\TrophyCatchLogged;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickCatchModal extends Component
{
    public bool $isOpen = false;

    public ?string $anglers_id = null;

    public ?string $lakes_id = null;

    public ?string $fish_breeds_id = null;

    public ?string $lures_id = null;

    public ?string $expeditions_id = null;

    public ?float $length = null;

    public ?float $weight = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $caught = '';

    public bool $released = true;

    public ?string $statusMessage = null;

    public ?string $statusType = null;

    public ?string $createdRecordId = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $trophyMilestone = null;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'anglers_id' => 'required|string|exists:anglers,id',
            'lakes_id' => 'required|string|exists:lakes,id',
            'fish_breeds_id' => 'required|string|exists:fish_breeds,id',
            'lures_id' => 'nullable|string|exists:lures,id',
            'expeditions_id' => 'nullable|string|exists:expeditions,id',
            'length' => 'required|numeric|min:0.1|max:200',
            'weight' => 'nullable|numeric|min:0.1|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'caught' => 'required|date',
            'released' => 'boolean',
        ];
    }

    public function mount(): void
    {
        $this->caught = date('Y-m-d');
        /** @var \Fishinglog\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->angler) {
            $this->anglers_id = (string) $user->angler->id;
        }
    }

    /**
     * Open the quick catch slide-over modal with optional pre-filled parameters.
     *
     * @param array<string, mixed> $params
     */
    #[On('open-quick-catch')]
    public function openQuickCatch(array $params = []): void
    {
        $this->isOpen = true;
        $this->statusMessage = null;
        $this->statusType = null;
        $this->trophyMilestone = null;
        $this->createdRecordId = null;

        if (!empty($params['lake_id'])) {
            $this->lakes_id = (string) $params['lake_id'];
        } elseif (!empty($params['lakes_id'])) {
            $this->lakes_id = (string) $params['lakes_id'];
        }

        if (!empty($params['species_id'])) {
            $this->fish_breeds_id = (string) $params['species_id'];
        } elseif (!empty($params['fish_breeds_id'])) {
            $this->fish_breeds_id = (string) $params['fish_breeds_id'];
        }

        if (!empty($params['expedition_id'])) {
            $this->expeditions_id = (string) $params['expedition_id'];
        } elseif (!empty($params['expeditions_id'])) {
            $this->expeditions_id = (string) $params['expeditions_id'];
        }

        if (!empty($params['angler_id'])) {
            $this->anglers_id = (string) $params['angler_id'];
        } elseif (!empty($params['anglers_id'])) {
            $this->anglers_id = (string) $params['anglers_id'];
        }

        if (!empty($params['lure_id'])) {
            $this->lures_id = (string) $params['lure_id'];
        }

        if (!empty($params['latitude']) && is_numeric($params['latitude'])) {
            $this->latitude = (float) $params['latitude'];
        }

        if (!empty($params['longitude']) && is_numeric($params['longitude'])) {
            $this->longitude = (float) $params['longitude'];
        }
    }

    #[On('close-quick-catch')]
    public function closeQuickCatch(): void
    {
        $this->isOpen = false;
        $this->statusMessage = null;
        $this->statusType = null;
    }

    #[On('lure-selected')]
    public function handleLureSelected(?string $lureId = null): void
    {
        $this->lures_id = $lureId;
    }

    public function save(CreateCatchRecordAction $createRecordAction): void
    {
        $validated = $this->validate();

        /** @var Record $record */
        $record = $createRecordAction->execute($validated);

        // Check if this catch achieves a Personal Best or Trophy milestone
        try {
            $milestone = $record->checkTrophyMilestone();
            if ($milestone) {
                /** @var \Fishinglog\Models\User|null $user */
                $user = Auth::user() ?? $record->angler?->user;
                if ($user) {
                    $user->notify(new TrophyCatchLogged($record, $milestone));
                }
                $this->trophyMilestone = array_merge($milestone, [
                    'record_id' => $record->id,
                    'species_name' => $record->fishBreed ? $record->fishBreed->name : 'Fish',
                    'length' => $record->length,
                    'lake_name' => $record->lake ? $record->lake->name : 'Waterbody',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to evaluate trophy milestone in quick catch modal: ' . $e->getMessage());
        }

        $this->createdRecordId = (string) $record->id;
        $this->statusMessage = 'Catch registered successfully!';
        $this->statusType = 'success';

        // Reset numeric dimensions for rapid successive logging while retaining angler & lake
        $this->length = null;
        $this->weight = null;

        // Dispatch reactive events for parent pages
        $this->dispatch('catch-saved', recordId: $record->id, lakeId: $record->lakes_id);
        $this->dispatch('refresh-records');
        $this->dispatch('refresh-map');
    }

    public function render(): View
    {
        $anglers = $this->isOpen ? Angler::orderBy('firstName')->get() : collect();
        $lakes = $this->isOpen ? Lake::orderBy('name')->get() : collect();
        $fishBreeds = $this->isOpen ? FishBreed::orderBy('name')->get() : collect();

        return view('livewire.modals.quick-catch-modal', [
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fishBreeds' => $fishBreeds,
        ]);
    }
}
