<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class AuthController extends SocialiteController
{

    protected function callbackUrl(): string
    {
        return route('open_overlay.connection.callback');
    }

    public function handleProviderCallback()
    {
        $socialiteUser = $this->socialite()->user();

        if (empty($socialiteUser->token)) {
            return redirect()->route('dashboard');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $service = Connection::firstOrCreate(
            [
                'user_id' => $user->id,
                'service' => 'twitch',
            ],
            [
                'service_token' => $socialiteUser->token,
            ]);

        $service->service_token = $socialiteUser->token;
        $service->service_refresh_token = $socialiteUser->refreshToken;

        $service->service_user_id = $socialiteUser->getId();
        $service->service_username = $socialiteUser->getName();

        $service->expires_at = Carbon::now()->addSeconds($socialiteUser->expiresIn);
        $service->save();

        $user = Auth::user();
        event(new UserConnectionChanged($user, 'twitch'));

        return redirect()->route('dashboard');
    }
}


https://local-movrs.openoverlay.dev/connection/callback?code=qqys2acl61kguoldnd1whcaxlkjcue&scope=user%3Aread%3Aemail+user%3Aread%3Abroadcast+channel%3Aread%3Asubscriptions+channel%3Aread%3Aredemptions+bits%3Aread+chat%3Aedit+chat%3Aread&state=G0g27kgPtcXxN1nmgrUguldhoFBXryYwMWRIl98d
