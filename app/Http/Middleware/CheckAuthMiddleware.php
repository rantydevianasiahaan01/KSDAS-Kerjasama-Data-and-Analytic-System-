<?php

namespace App\Http\Middleware;

use App\Helper\ToolsHelper;
use App\Http\Api\UserApi;
use App\Models\HakAksesModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class CheckAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authToken = ToolsHelper::getAuthToken();
        if (empty($authToken)) {
            // Redirect to login page if auth token is not set
            return redirect()->route('auth.login');
        }

        $response = UserApi::getMe($authToken);

        if (!isset($response->data->user)) {
            // If the token is invalid, redirect to login page
            return redirect()->route('auth.login');
        }

        $auth = $response->data->user;
        $auth->photo = $auth->photo ?? '/assets/images/profile/' . ($request->auth->gender == 'F' ? 'F' : 'M') . '.jpg';

        $akses = HakAksesModel::where('user_id', $auth->id)->first();
        $auth->akses = isset($akses->akses) ? explode(',', $akses->akses) : [];

        $request->attributes->set('auth', $auth);
        return $next($request);
    }
}
