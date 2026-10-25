<?php

namespace Fishinglog\Livewire\Components;

use Fishinglog\Models\Angler;
use Fishinglog\Models\AnglerBadge;
use Fishinglog\Models\Badge;
use Fishinglog\Models\Expedition;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MeritBadgeCarousel extends Component
{
    public ?string $anglerId = null;

    public ?string $expeditionId = null;

    public ?int $maxVisible = null;

    public string $size = 'md'; // sm, md, lg

    public ?string $title = null;

    public bool $showCount = true;

    public bool $showPoints = true;

    public bool $showSashLink = true;

    public bool $boxed = true;

    public bool $showHeader = true;

    public bool $showInspector = true;

    /**
     * Stored lightweight DTO array when badges are passed directly as a collection.
     *
     * @var list<array<string, mixed>>
     */
    public array $directBadges = [];

    /**
     * Mount the component with either a badge collection, an angler, or an expedition.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>|array<int, mixed>|null  $badges
     * @param  \Fishinglog\Models\Angler|string|null  $angler
     * @param  \Fishinglog\Models\Expedition|string|null  $expedition
     */
    public function mount(
        mixed $badges = null,
        mixed $angler = null,
        mixed $expedition = null,
        ?int $maxVisible = null,
        string $size = 'md',
        ?string $title = null,
        bool $showCount = true,
        bool $showPoints = true,
        bool $showSashLink = true,
        bool $boxed = true,
        bool $showHeader = true,
        bool $showInspector = true
    ): void {
        if ($angler instanceof Angler) {
            $this->anglerId = $angler->id;
        } elseif (is_string($angler)) {
            $this->anglerId = $angler;
        }

        if ($expedition instanceof Expedition) {
            $this->expeditionId = $expedition->id;
        } elseif (is_string($expedition)) {
            $this->expeditionId = $expedition;
        }

        $this->maxVisible = ($maxVisible !== null && $maxVisible > 0) ? $maxVisible : null;
        $this->size = in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'md';
        $this->title = $title;
        $this->showCount = $showCount;
        $this->showPoints = $showPoints;
        $this->showSashLink = $showSashLink;
        $this->boxed = $boxed;
        $this->showHeader = $showHeader;
        $this->showInspector = $showInspector;

        if ($badges !== null) {
            $this->directBadges = $this->transformBadgeCollection($badges);
        }
    }

    /**
     * Computed badge list DTOs to eliminate Eloquent serialization bloat.
     *
     * @return list<array<string, mixed>>
     */
    #[Computed]
    public function badgeList(): array
    {
        if (! empty($this->directBadges)) {
            return $this->directBadges;
        }

        if ($this->anglerId) {
            $angler = Angler::with(['earnedBadges.badge', 'earnedBadges.record'])->find($this->anglerId);
            if ($angler && $angler->earnedBadges) {
                return $this->transformBadgeCollection($angler->earnedBadges);
            }
        }

        if ($this->expeditionId) {
            $earned = AnglerBadge::where('expedition_id', $this->expeditionId)
                ->with(['badge', 'record', 'angler'])
                ->orderBy('awarded_at', 'desc')
                ->get();

            return $this->transformBadgeCollection($earned);
        }

        return [];
    }

    /**
     * Total count of all badges across the dataset.
     */
    #[Computed]
    public function totalBadgesCount(): int
    {
        return count($this->badgeList);
    }

    /**
     * Total score points summed from the badges.
     */
    #[Computed]
    public function totalPoints(): int
    {
        return array_sum(array_column($this->badgeList, 'points'));
    }

    /**
     * Slice of badges to display within carousel track limits.
     *
     * @return list<array<string, mixed>>
     */
    #[Computed]
    public function visibleBadges(): array
    {
        if ($this->maxVisible === null || $this->maxVisible <= 0) {
            return $this->badgeList;
        }

        return array_slice($this->badgeList, 0, $this->maxVisible);
    }

    /**
     * Count of badges exceeding the maximum carousel limit.
     */
    #[Computed]
    public function overflowCount(): int
    {
        if ($this->maxVisible === null || $this->maxVisible <= 0) {
            return 0;
        }

        return max(0, count($this->badgeList) - $this->maxVisible);
    }

    /**
     * Render the Livewire component view.
     */
    public function render(): View
    {
        return view('livewire.components.merit-badge-carousel', [
            'badges' => $this->visibleBadges,
            'totalCount' => $this->totalBadgesCount,
            'totalPoints' => $this->totalPoints,
            'overflowCount' => $this->overflowCount,
            'boxed' => $this->boxed,
            'showHeader' => $this->showHeader,
            'showInspector' => $this->showInspector,
            'size' => $this->size,
            'carouselId' => $this->getId(),
            'showCount' => $this->showCount,
            'showPoints' => $this->showPoints,
            'showSashLink' => $this->showSashLink,
        ]);
    }

    /**
     * Transform a heterogeneous badge collection into uniform presentation DTOs.
     *
     * @param  mixed  $items
     * @return list<array<string, mixed>>
     */
    protected function transformBadgeCollection(mixed $items): array
    {
        if ($items instanceof EloquentCollection || $items instanceof Collection) {
            $items = $items->all();
        }

        if (! is_iterable($items)) {
            return [];
        }

        $transformed = [];

        foreach ($items as $item) {
            $dto = $this->extractBadgeDto($item);
            if ($dto !== null) {
                $transformed[] = $dto;
            }
        }

        return $transformed;
    }

    /**
     * Extract uniform DTO attributes from an AnglerBadge or Badge record.
     *
     * @param  mixed  $item
     * @return array<string, mixed>|null
     */
    protected function extractBadgeDto(mixed $item): ?array
    {
        $badge = null;
        $pivot = null;

        if ($item instanceof AnglerBadge) {
            $pivot = $item;
            $badge = $item->badge;
        } elseif ($item instanceof Badge) {
            $badge = $item;
            $pivot = $item->pivot ?? null;
        } elseif (is_array($item)) {
            return $item;
        }

        if (! $badge) {
            return null;
        }

        $tier = strtolower($badge->tier ?? 'bronze');
        $imagePath = $badge->image_path;
        $hasImage = $imagePath && file_exists(public_path($imagePath));

        return [
            'id' => (string) $badge->id,
            'slug' => (string) ($badge->slug ?? ''),
            'name' => (string) $badge->name,
            'description' => (string) ($badge->description ?? ''),
            'category' => (string) ($badge->category ?? 'general'),
            'tier' => $tier,
            'points' => (int) ($badge->points ?? 0),
            'icon' => (string) ($badge->icon ?: 'award'),
            'image_url' => $hasImage ? asset($imagePath) : null,
            'has_image' => $hasImage,
            'awarded_at' => $pivot?->awarded_at ? $pivot->awarded_at->format('M j, Y') : null,
            'trigger_summary' => $pivot?->trigger_summary ?? null,
            'record_id' => $pivot?->record_id ?? null,
            'angler_name' => $pivot?->angler?->name ?? null,
            'tier_border' => $badge->getTierBorderColor(),
            'tier_badge' => $badge->getTierBadgeClass(),
            'tier_glow' => match ($tier) {
                'platinum' => 'shadow-cyan-400/40 ring-cyan-400/30 border-cyan-400',
                'gold' => 'shadow-amber-400/40 ring-amber-400/30 border-amber-400',
                'silver' => 'shadow-slate-300/40 ring-slate-300/30 border-slate-300',
                default => 'shadow-amber-800/40 ring-amber-700/30 border-amber-700',
            },
            'tier_pip' => match ($tier) {
                'platinum' => 'bg-cyan-400',
                'gold' => 'bg-amber-400',
                'silver' => 'bg-slate-300',
                default => 'bg-amber-700',
            },
        ];
    }
}
