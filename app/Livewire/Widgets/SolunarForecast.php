<?php

namespace Fishinglog\Livewire\Widgets;

use Fishinglog\Models\Lake;
use Fishinglog\Services\SolunarService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class SolunarForecast extends Component
{
    public ?string $lakeId = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $lakeName = 'Location';

    public string $date = '';

    public bool $compact = false;

    public bool $collapsed = false;

    public function mount(
        ?string $lakeId = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $date = null,
        bool $compact = false,
        bool $collapsed = false
    ): void {
        $this->lakeId = $lakeId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->compact = $compact;
        $this->collapsed = $collapsed;
        $this->date = $date ?: Carbon::today()->format('Y-m-d');

        $this->resolveLakeCoordinates();
    }

    protected function resolveLakeCoordinates(): void
    {
        if ($this->lakeId) {
            $lake = Lake::find($this->lakeId);
            if ($lake) {
                $this->lakeName = $lake->name;
                $this->latitude = (float) $lake->latitude;
                $this->longitude = (float) $lake->longitude;
            }
        }

        // Default to central Ontario angling waters (e.g. Wawa Lake ~47.99, -84.76) if unspecified
        if (is_null($this->latitude) || is_null($this->longitude)) {
            $this->latitude = 47.9944;
            $this->longitude = -84.7619;
            $this->lakeName = 'Ontario Waters';
        }
    }

    #[On('lake-selected')]
    public function handleLakeSelected(string $lakeId): void
    {
        $this->lakeId = $lakeId;
        $this->resolveLakeCoordinates();
    }

    public function previousDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
    }

    public function today(): void
    {
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function setDate(string $targetDate): void
    {
        $this->date = Carbon::parse($targetDate)->format('Y-m-d');
    }

    public function toggleCollapsed(): void
    {
        $this->collapsed = !$this->collapsed;
    }

    public function render(): View
    {
        $solunarService = app(SolunarService::class);
        $solunarData = $solunarService->getSolunarData(
            (float) $this->latitude,
            (float) $this->longitude,
            $this->date
        );

        $isToday = Carbon::parse($this->date)->isToday();

        return view('livewire.widgets.solunar-forecast', [
            'solunar' => $solunarData,
            'isToday' => $isToday,
        ]);
    }
}
