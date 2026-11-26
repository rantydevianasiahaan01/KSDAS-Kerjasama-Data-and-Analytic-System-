<?php

namespace App\Livewire\Profile;

use App\Helper\ToolsHelper;
use App\Helper\WithComponentNameHelper;
use App\Http\Api\UserApi;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileLivewire extends Component
{
    use WithComponentNameHelper;
    use WithFileUploads;

    // Rendered
    public function rendered()
    {
        $this->resetErrorBag();  // Reset error bag setelah render selesai
    }

    // Mount
    public $auth;

    public function mount()
    {
        $this->auth = request()->attributes->get('auth');
        $this->editWhatsappKontak = $this->auth->whatsapp ?? '';
    }

    public function render()
    {
        return view('features.profile.profile-livewire');
    }

    // Edit Password
    public $editPasswordLama;
    public $editPasswordBaru;

    public function editPassword()
    {
        $this->validate([
            'editPasswordLama' => 'required',
            'editPasswordBaru' => 'required',
        ]);

        $authToken = ToolsHelper::getAuthToken();
        $response = UserApi::putMePassword($authToken, $this->editPasswordBaru, $this->editPasswordLama);
        if (!isset($response->status) || $response->status != 'success') {
            $message = isset($response->message) ? $response->message : 'Gagal mengirimkan data.';
            $this->addError('editPasswordLama', $message);
            return;
        }

        // Reload halaman
        $this->dispatch('reloadPage');
    }

    // Edit Whatsapp
    public $editWhatsappKontak;
    public $editWhatsappPassword;

    public function editWhatsapp()
    {
        $this->validate([
            'editWhatsappKontak' => 'required',
            'editWhatsappPassword' => 'required',
        ]);

        $authToken = ToolsHelper::getAuthToken();
        $response = UserApi::putMeWhatsapp($authToken, $this->editWhatsappKontak, $this->editWhatsappPassword);
        if (!isset($response->status) || $response->status != 'success') {
            $message = isset($response->message) ? $response->message : 'Gagal mengirimkan data.';
            $this->addError('editWhatsappKontak', $message);
            return;
        }

        // Reload halaman
        $this->dispatch('reloadPage');
    }

    public $photo;

    public function prepareEditPhoto()
    {
        $this->photo = null;

        $this->dispatch('showModal', id: 'editPhotoModal');
        $this->dispatch('clickSelectPhoto');
    }

    public function editPhoto()
    {
        // Cek apakah photo beruapa file gambar base64
        if (!$this->photo || !str_starts_with($this->photo, 'data:image/')) {
            $this->addError('photo', 'Foto profil tidak valid.');
            return;
        }

        // dd($this->photo);
        $response = UserApi::putMePhoto(ToolsHelper::getAuthToken(), $this->photo);
        // dd($response);
        if (!isset($response->status) || $response->status != 'success') {
            $message = isset($response->message) ? $response->message : 'Gagal mengubah foto profil.';
            $this->addError('photo', $message);
            return;
        }

        // Reload halaman
        $this->dispatch("showSuccessAlert", "Berhasil mengubah photo profile.");
        return redirect()->route('app.profile');
        // dd($response);
    }

    // Delete Account
    public $deleteAccountPassword;
    public $deleteAccountKode;

    public function deleteAccount()
    {
        $this->validate([
            'deleteAccountPassword' => 'required',
            'deleteAccountKode' => 'required',
        ], [
            'deleteAccountPassword.required' => 'Kata sandi wajib diisi.',
            'deleteAccountKode.required' => 'Kode OTP wajib diisi.',
        ]);

        $authToken = ToolsHelper::getAuthToken();
        $response = UserApi::deleteMe($authToken, $this->deleteAccountPassword, $this->deleteAccountKode);
        if (!isset($response->status) || $response->status != 'success') {
            $message = isset($response->message) ? $response->message : 'Gagal menghapus akun.';
            $this->addError('deleteAccountPassword', $message);
            return;
        }

        // Logout dan redirect ke halaman login
        return redirect()->route('auth.logout');
    }
}
