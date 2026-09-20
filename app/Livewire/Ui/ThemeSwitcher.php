<?php

namespace Fishinglog\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ThemeSwitcher extends Component
{
    public string $theme = 'system';

    public bool $compact = false;

    public function mount(bool $compact = false): void
    {
        $this->compact = $compact;
        $this->theme = Auth::check() ? (Auth::user()->theme_preference ?? 'system') : 'system';
    }

    public function setTheme(string $theme): void
    {
        if (!in_array($theme, ['system', 'light', 'dark'], true)) {
            return;
        }

        $this->theme = $theme;

        if (Auth::check()) {
            Auth::user()->update([
                'theme_preference' => $theme,
            ]);
        }

        $this->dispatch('theme-changed', theme: $theme);
    }

    #[On('theme-changed')]
    public function handleThemeChanged(string $theme): void
    {
        if (in_array($theme, ['system', 'light', 'dark'], true)) {
            $this->theme = $theme;
        }
    }

    public function render(): View
    {
        return view('livewire.ui.theme-switcher');
    }
}
