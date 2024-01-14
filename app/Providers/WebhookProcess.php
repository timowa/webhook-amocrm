<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class WebhookProcessProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool{
        return true;
    }
}
