<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewBooking extends Notification
{
    use Queueable;

    protected $booking;
    protected $commonUtil;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($booking, $commonUtil)
    {
        $this->booking = $booking;
        $this->commonUtil = $commonUtil;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $customer_name = $this->booking->customer->name;
        $mail_message = (new MailMessage)
                    ->subject(__('restaurant.new_booking_subject', ['business_name' => $this->booking->business->name]))
                    ->greeting(__('restaurant.hello_name', ['name' => $customer_name]))
                    ->line(__('restaurant.booking_confirmed'));

        if (!empty($this->booking->table->name)) {
            $mail_message->line(__('restaurant.table_info', ['table' => $this->booking->table->name]));
        }
        $booking_start = $this->commonUtil->format_date($this->booking->booking_start, true);
        $booking_end = $this->commonUtil->format_date($this->booking->booking_end, true);

        $mail_message->line(__('restaurant.booking_time_info', ['from' => $booking_start, 'to' => $booking_end]));

        if (!empty($this->booking->booking_note)) {
            $mail_message->line($this->booking->booking_note);
        }

        return $mail_message;
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
