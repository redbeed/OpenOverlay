<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Api\Connection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Redbeed\OpenOverlay\Exceptions\WebhookTwitchSignatureMissing;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function handleProviderCallback(Request $request)
    {
        $messageSignature = $request->headers->get('Twitch-Eventsub-Message-Signature', '');
        $messageId = $request->headers->get('Twitch-Eventsub-Message-Id', '');
        $messageTimestamp = $request->headers->get('Twitch-Eventsub-Message-Timestamp', '');
        $requestBody = $request->getContent();

        if(EventSubClient::verifySignature($messageSignature, $messageId, $messageTimestamp, $requestBody) === false) {
            return response('Not Valid', Response::HTTP_UNAUTHORIZED);
        }

        return $request->get('challenge');
    }
}
