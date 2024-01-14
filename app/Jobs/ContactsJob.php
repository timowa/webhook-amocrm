<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ContactService;
use App\Services\PrintNodeService;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as ProcessWebhookJob;

class ContactsJob extends ProcessWebhookJob
{
  public function handle(ContactService $contactService, PrintNodeService $printNodeService){
      logger('Пришел вебхук контакта');
      $contactCall = $this->webhookCall;
      $data = $contactCall->payload['contacts'];
      if(array_key_exists('add',$data))
        $contact = $contactService->add($data['add'][0]);
      elseif(array_key_exists('update',$data))
        $contact = $contactService->update($data['update'][0]);
      if($contact)
        $printNodeService->printNode($contact);
  }
}
