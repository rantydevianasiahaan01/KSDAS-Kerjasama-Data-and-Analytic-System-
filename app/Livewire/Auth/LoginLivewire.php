<?php

namespace App\Livewire\Auth;

use App\Helper\ConstHelper;
use App\Helper\ToolsHelper;
use App\Helper\WithComponentNameHelper;
use App\Http\Api\UserApi;
use Livewire\Component;

class LoginLivewire extends Component
{
    use WithComponentNameHelper;

    public $globalErrorMessage = '';

    public function rendered()
    {
        ToolsHelper::setErrorMessage('');
    }

    public function mount()
    {
        $this->globalErrorMessage = ToolsHelper::getErrorMessage();

        $authToken = ToolsHelper::getAuthToken();
        if (!empty($authToken)) {
            $this->setup($authToken);
        }
    }

    public function render()
    {
        $urlLoginSSO = config('sdi.sso_authorize_url') . '?' . http_build_query([
            'client_id' => config('sdi.sso_client_id'),
        ]);

        $data = [
            'urlLoginSSO' => $urlLoginSSO,
        ];

        return view('features.auth.login-livewire', $data);
    }

    public function setup($authToken)
    {
        $resLoginInfo = UserApi::getLoginInfo($authToken);
        if ($resLoginInfo->status != 'success') {
            ToolsHelper::setAuthToken('');
            $this->dispatch('invalidAuthToken');
            return;
        }

        ToolsHelper::setAuthToken($authToken);

        $response = UserApi::getMe($authToken);
        if ($response->status != 'success') {
            return redirect()->route('auth.totp');
        }

        return redirect()->route('app.beranda');
    }

    // Data Submit
    public $systemId;
    public $info;
    public $username;
    public $password;

    public function submit()
    {
        // Reset error bag sebelum validasi
        $this->resetErrorBag();

        // Validasi input
        $this->validate([
            'systemId' => 'required|string|max:50',
            'info' => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        $response = UserApi::postLogin(
            $this->systemId,
            config('app.name'),
            $this->info,
            $this->username,
            $this->password
        );

        if (!isset($response->data->token)) {
            $this->addError('username', $response->message ?? 'Gagal login, silakan coba lagi.');
            return;
        }

        ToolsHelper::setAuthToken($response->data->token);
        $this->dispatch('saveAuthToken', authToken: $response->data->token);
    }
}
