<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $user;
    public $title;
    public $view;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$order)
    {
        $this->user =   $user;
        $this->order =   $order;
        if ($order->order_status=='pending'){
            $this->title  =   'Order placed';
            $this->view  =   'mail.order_placed';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->to('waqaryazdani2@gmail.com')
            ->subject($this->title)
            ->view($this->view);
    }
}
