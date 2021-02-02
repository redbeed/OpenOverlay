<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Api\Connection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redbeed\OpenOverlay\Events\TwitchEventReceived;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;
use Redbeed\OpenOverlay\Service\Twitch\DateTime;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function handleProviderCallback(Request $request)
    {
        $messageSignature = $request->headers->get('Twitch-Eventsub-Message-Signature', '');
        $messageId = $request->headers->get('Twitch-Eventsub-Message-Id', '');
        $messageTimestamp = $request->headers->get('Twitch-Eventsub-Message-Timestamp', '');
        $messageType = $request->headers->get('Twitch-Eventsub-Subscription-Type', '');
        $requestBody = $request->getContent();

        if (EventSubClient::verifySignature($messageSignature, $messageId, $messageTimestamp, $requestBody) === false) {
            return response('Not Valid', Response::HTTP_UNAUTHORIZED);
        }

        $event = $request->get('event');
        if (!empty($event) && in_array($messageType, config('openoverlay.webhook.twitch.subscribe'), true)) {

            return $this->receiveNotification(
                $messageId,
                $messageType,
                $messageTimestamp,
                $event
            );

        }

        return $request->get('challenge');
    }

    private function receiveNotification(string $eventId, string $eventType, string $eventTimestamp, array $eventData)
    {
        if (empty($eventId) || empty($eventType)) {
            return \response('Event id or type not valid', Response::HTTP_BAD_REQUEST);
        }

        $newEvent = EventSubEvents::firstOrCreate(
            [
                'event_id' => $eventId,
            ],
            [
                'event_type' => $eventType,
                'event_user_id' => $eventData['broadcaster_user_id'] ?? $eventData['to_broadcaster_user_id'],
                'event_data' => $eventData,
                'event_sent' => DateTime::parse($eventTimestamp),
            ]
        );

        if ($newEvent->wasRecentlyCreated) {
            broadcast(new TwitchEventReceived($newEvent));
        }

        return \response('Event received', $newEvent->wasRecentlyCreated ? Response::HTTP_CREATED : Response::HTTP_OK);

    }
}
