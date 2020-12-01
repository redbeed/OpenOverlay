<?php


namespace Redbeed\OpenOverlay\Http\Controllers\Api\Connection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleProviderCallback(Request $request)
    {
        Log::info($request->all());
        return $request->get('challenge');
    }
}
