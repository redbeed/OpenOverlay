<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Redbeed\OpenOverlay\Models\User\Connections;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController extends Controller
{
    private function socialite(): Provider
    {
        return Socialite::driver('twitch');
        //            ->stateless()
        //            ->scopes(['user:edit:follows', 'channel:read:subscriptions', 'channel:read:redemptions', 'bits:read', 'user:read:broadcast'])
        //            ->with(['grant_type' => 'client_credentials']);
    }

    public function redirect(): RedirectResponse
    {
        return $this->socialite()->redirect();
    }

    public function handleProviderCallback()
    {
        $auth = $this->socialite()->getAccessTokenResponse(request()->get('code'));

        if (empty($auth['access_token'])) {
            return redirect()->route('dashboard');
        }

        /** @var User $user */
        $user = Auth::user();

        $service = Connections::firstOrCreate(
            [
                'user_id' => $user->id,
                'service' => 'twitch',
            ],
            [
                'service_token' => $auth['access_token'],
            ]);

        $service->service_token = $auth['access_token'];
        $service->expires_at = Carbon::now()->addSeconds($auth['expires_in']);
        $service->save();

        return redirect()->route('dashboard');
    }
}
