<?php

namespace Corals\Modules\Payment\SecurionPay\Job;


use Corals\Modules\Payment\Common\Models\WebhookCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleCustomerTrialWillEnd implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Corals\Modules\Payment\Common\Models\WebhookCall
     */
    public $webhookCall;

    /**
     * HandleInvoiceCreated constructor.
     * @param WebhookCall $webhookCall
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        logger('customer.subscription.trial_will_end');
        $this->webhookCall->markAsProcessed();
    }
}
