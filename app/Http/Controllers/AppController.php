<?php

namespace App\Http\Controllers;

use App\Livewire\HakAkses\HakAksesLivewire;
use App\Livewire\Profile\ProfileLivewire;
use App\Livewire\Umum\BerandaLivewire;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function umumBeranda(Request $request)
    {
        return view('pages.app', [
            'auth' => $request->attributes->get('auth'),
            'title' => 'Beranda',
            'livewire' => BerandaLivewire::getComponentName(),
        ]);
    }

    public function hakAkses(Request $request)
    {
        return view('pages.app', [
            'auth' => $request->attributes->get('auth'),
            'title' => 'Hak Akses',
            'livewire' => HakAksesLivewire::getComponentName(),
        ]);
    }

    public function profile(Request $request)
    {
        return view('pages.app', [
            'auth' => $request->attributes->get('auth'),
            'title' => 'Profile',
            'livewire' => ProfileLivewire::getComponentName(),
        ]);
    }

    public function privacy(Request $request)
    {
        return view('features.legal.privacy-page', [
            'auth' => $request->attributes->get('auth'),
        ]);
    }

    public function terms(Request $request)
    {
        return view('features.legal.terms-page', [
            'auth' => $request->attributes->get('auth'),
        ]);
    }
}
