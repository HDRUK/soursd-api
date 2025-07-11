<?php

namespace Tests\Feature;

use App\Jobs\SendEmailJob;
use Hdruk\LaravelMjml\Models\EmailTemplate;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailServiceTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake();
    }

    public function test_dispatch_email_job()
    {
        Mail::fake();

        Http::fake([
            config('mjml.default.access.mjmlRenderUrl') => Http::response(
                ['html' => '<html>content</html>'],
                201,
                ['application/json']
            ),
        ]);

        $to = [
            'to' => [
                'email' => 'loki.sinclair@hdruk.ac.uk',
                'name' => 'Loki Sinclair',
            ],
        ];

        $template = EmailTemplate::where('identifier', '=', 'example_template')->first();

        $replacements = [
            '[[header_text]]' => 'SOURSD',
            '[[button_text]]' => 'Click me!',
            '[[subheading_text]]' => 'Sub Heading Something or other',
        ];

        Bus::assertNothingDispatched();

        SendEmailJob::dispatch($to, $template, $replacements, null);

        Bus::assertDispatched(SendEmailJob::class);
    }
}
