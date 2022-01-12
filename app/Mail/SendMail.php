<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $senderEmail, $data, $title, $type)
    {
        $this->email = $email;
        $this->senderEmail = $senderEmail;
        $this->data = $data;
        $this->title = $title;
        $this->type = $type;
        // dd($type);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->senderEmail , $this->senderEmail)
            ->subject($this->title)
            ->view('send_mail')
            ->with([
                'data' => $this->data,
                'email' => $this->email,
                'title' => $this->title,
                'type' => $this->type,
            ]);
    }
}
