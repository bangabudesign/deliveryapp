<?php

namespace App\Notifications;

use App\Channels\Messages\WhatsAppTemplate;
use App\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSuccess extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
        // return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return WhatsAppMessage
     */
    public function toWhatsApp($notifiable)
    {
        if ($this->order->type == "FOOD") {
            return (new WhatsAppTemplate)
                ->templateName('customer_order_success')
                ->default($this->order->invoice)
                ->default($this->order->driver->name)
                ->default(strval('https://wa.me/62'.$this->order->driver->phone))
                ->default($this->order->restaurant->name)
                ->default($this->order->restaurant->address)
                ->default(strval('Rp'.number_format($this->order->total)));
        } else {
            return (new WhatsAppTemplate)
                ->templateName('no_template')
                ->default(strval('Rp'.number_format($this->order->total)));
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
