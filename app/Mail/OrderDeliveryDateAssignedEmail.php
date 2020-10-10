<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDeliveryDateAssignedEmail extends Mailable
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
        $this->view =   'mail.date_assigned';
        $this->title =   'Order No.'. $order->order_no.' Delivery Date';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to('wy@softwarealliance.dk')
            ->subject($this->title)
            ->view($this->view);
    }
}
