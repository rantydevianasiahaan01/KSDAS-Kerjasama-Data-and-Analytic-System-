<?php

namespace App\Http\Controllers;

use App\Helper\ApiHelper;
use App\Helper\ToolsHelper;
use App\Livewire\Auth\LoginLivewire;
use App\Livewire\Auth\LoginQrCodeLivewire;
use App\Livewire\Auth\RegisterLivewire;
use App\Livewire\Auth\TotpLivewire;
use Illuminate\Http\Request;
use App\Http\Api\UserApi;

class AuthController extends Controller
{
    public function loginRedirect($redirectToken)
    {
        $response = UserApi::postLoginRedirectVerify($redirectToken);
        if (! isset($response->data->token)) {
            return redirect()->route('auth.login');
        }

        ToolsHelper::setAuthToken($response->data->token);

        return redirect()->route('app.beranda');
    }


    public function register()
    {
         $data = [
            'title' => 'Register',
            'livewire' => RegisterLivewire::getComponentName()
        ];

        return view('pages.auth', $data);
    }

    public function login()
    {
        $data = [
            'title' => 'Login',
            'livewire' => LoginLivewire::getComponentName()
        ];

        return view('pages.auth', $data);
    }

    public function loginQRCode()
    {
        $data = [
            'title' => 'Login QRCode',
            'livewire' => LoginQrCodeLivewire::getComponentName()
        ];

        return view('pages.auth', $data);
    }

    public function totp()
    {
        $data = [
            'title' => 'TOTP',
            'livewire' => TotpLivewire::getComponentName()
        ];

        return view('pages.auth', $data);
    }

    public function logout()
    {
        $authToken = ToolsHelper::getAuthToken();
        if($authToken) {
            UserApi::postLogout($authToken);
        }
        ToolsHelper::setAuthToken('');
        return view('features.auth.logout-page');
    }

    // SSO Callback
    public function ssoCallback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('auth.login')->with('error', 'Kode otorisasi tidak ditemukan');
        }

        $urlToken = config('sdi.sso_token_url');
        $clientId = config('sdi.sso_client_id');
        $clientSecret = config('sdi.sso_client_secret');

        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ];

        $response = ApiHelper::sendRequest($urlToken, 'POST', $data);

        if (!isset($response->access_token)) {
            ToolsHelper::setErrorMessage(isset($response->message) ? $response->message : 'Gagal mendapatkan token akses dari SSO');
            return redirect()->route('auth.login');
        }

        // Simpan token akses di sesi atau cookie
        ToolsHelper::setAuthToken($response->access_token);
        return redirect()->route('app.beranda');
    }
}
