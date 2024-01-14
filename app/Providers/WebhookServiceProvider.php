<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class WebhookServiceProvider implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config):bool{
        return true;
    }
}
