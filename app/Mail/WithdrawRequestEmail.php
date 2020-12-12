<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $amount;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$amount)
    {
        $this->user =   $user;
        $this->amount =   $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->user->email)
            ->subject('Withdraw Request')
            ->view('mail.withdraw_request');
    }
}
