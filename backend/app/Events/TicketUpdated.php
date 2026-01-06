<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket->load(['client.user', 'service', 'freelancer.user']);
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->ticket->client->user_id),
        ];

        if ($this->ticket->freelancer_id) {
            $channels[] = new PrivateChannel('user.' . $this->ticket->freelancer->user_id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => $this->ticket,
            'message' => 'Ticket atualizado: ' . $this->ticket->title,
        ];
    }
}
