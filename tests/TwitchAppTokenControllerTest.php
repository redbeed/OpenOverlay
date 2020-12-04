<?php

namespace Redbeed\OpenOverlay\Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class TwitchAppTokenControllerTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        config()->set('openoverlay.webhook.twitch.app_token.regenerate', true);

        Queue::fake();
        Event::fake();
    }

    /** @test */
    public function it_hide_app_token_generate_pages()
    {
        config()->set('openoverlay.webhook.twitch.app_token.regenerate', false);

        $this
            ->get(route('open_overlay.connection.app-token.redirect'))
            ->assertNotFound();

        $this
            ->get(route('open_overlay.connection.app-token.callback'))
            ->assertNotFound();
    }

    /** @test */
    public function it_redirect_without_auth()
    {
        $this->withoutExceptionHandling();

        $this
            ->get(route('open_overlay.connection.app-token.redirect'))
            ->assertNotFound();
    }
}
