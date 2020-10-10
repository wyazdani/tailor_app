<?php

namespace App\Jobs;

use App\Mail\OrderDeliveryDateAssignedEmail;
use App\Mail\OrderdeliveryDateCustomer;
use App\Mail\OrderdeliveryDateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OrderDeliveryDateAssigned implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $order;
    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$order)
    {
        $this->user =   $user;
        $this->order =   $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send(new OrderDeliveryDateAssignedEmail($this->user,$this->order));
    }
}
