<?php

namespace App\Livewire\Umum;

use App\Helper\WithComponentNameHelper;
use Livewire\Component;

class BerandaLivewire extends Component
{
    use WithComponentNameHelper;

    public $auth;

    public function mount()
    {
        $this->auth = request()->attributes->get('auth');
    }

    public function render()
    {
        return view('features.umum.beranda-livewire');
    }
}
