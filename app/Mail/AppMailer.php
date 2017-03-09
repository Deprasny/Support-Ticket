<?php

namespace App\Mail;
use App\Ticket;



use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;

class AppMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $mailer;
    public $formAddress ='Deprasnur@gmail.com';
    public $fromName ='Support Ticket';
    public $to;
    public $subject;
    public $view;
    public $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
      $this->mailer = $mailer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function sendTicketInformation($user, Ticket $ticket)
    {
        $this->to = $user->email;
        $this->subject="[Ticket ID: $ticket->ticket_id] $ticket->title";
        $this->view = 'emails.ticket_info';
        $this->data = compact('user', 'ticket');
        return $this->deliver();
    }

    public function sendTicketComments($ticketOwner, $user, Ticket $ticket, $comment)
    {
        $this->to = $ticketOwner->email;
        $this->subject = "RE: $ticket->title (Ticket ID:   $ticket->ticket_id)";
        $this->view = 'emails.ticket_comments';
        $this->data = compact('ticketOwner', 'user', 'ticket', 'comment');
    }

    public function sendTicketStatusNotification($ticketOwner, Ticket $ticket)
    {
      $this->to = $ticketOwner->email;
      $this->subject = "RE: $ticket->title (Ticket ID: $ticket->ticket_id)";
      $this->view = 'emails.ticket_status';
      $this->data = compact('ticketOwner', 'ticket');

      return $this->deliver();
    }

    public function deliver()
    {
      $this->mailer->send($this->view, $this->data, function($message){
        $message->from($this->formAddress, $this->fromName)
                ->to($this->to)->subject($this->subject);
      });
    }
}
