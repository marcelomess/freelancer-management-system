<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket->load(['client.user', 'service', 'freelancer.user']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tickets'),
            new PrivateChannel('user.' . $this->ticket->service->freelancer->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => $this->ticket,
            'message' => 'Novo ticket criado: ' . $this->ticket->title,
        ];
    }
}
