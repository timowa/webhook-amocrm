<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\LeadService;
use App\Services\PrintNodeService;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as ProcessWebhookJob;

class LeadsJob extends ProcessWebhookJob
{

    public function handle(LeadService $leadService, PrintNodeService $printNodeService): void
    {
        logger('Пришел вебхук сделки');
        $leadCall = $this->webhookCall;
        $data = $leadCall->payload['leads'];
        if (array_key_exists('add', $data))
            $lead = $leadService->add($data['add'][0]);
        elseif(array_key_exists('update', $data))
            $lead = $leadService->update($data['update'][0]);
        if($lead)
            $printNodeService->printNode($lead);
    }
}
