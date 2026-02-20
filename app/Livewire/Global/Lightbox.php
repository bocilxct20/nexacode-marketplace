<?php

namespace App\Livewire\Global;

use Livewire\Component;
use Livewire\Attributes\On;

class Lightbox extends Component
{
    public $isOpen = false;
    public $images = [];
    public $activeIndex = 0;

    #[On('open-lightbox')]
    public function open($images, $activeIndex = 0)
    {
        $this->images = $images;
        $this->activeIndex = $activeIndex;
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->images = [];
        $this->activeIndex = 0;
    }

    public function next()
    {
        if (empty($this->images)) return;
        $this->activeIndex = ($this->activeIndex + 1) % count($this->images);
    }

    public function prev()
    {
        if (empty($this->images)) return;
        $this->activeIndex = ($this->activeIndex - 1 + count($this->images)) % count($this->images);
    }

    public function render()
    {
        return view('livewire.global.lightbox');
    }
}
