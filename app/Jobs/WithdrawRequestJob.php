<?php

namespace App\Jobs;

use App\Mail\WithdrawRequestEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WithdrawRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    public $amount;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$amount)
    {
        $this->user =   $user;
        $this->amount =   $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send(new WithdrawRequestEmail($this->user,$this->amount));
    }
}
