<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Events\TicketCreated;
use App\Events\TicketUpdated;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['client.user', 'service', 'freelancer.user']);

        // Filtrar por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por cliente (somente tickets do próprio cliente)
        if ($request->user()->isClient()) {
            $query->where('client_id', $request->user()->client->id);
        }

        // Filtrar por freelancer (tickets atribuídos ao freelancer)
        if ($request->user()->isFreelancer()) {
            $query->where('freelancer_id', $request->user()->freelancer->id);
        }

        return TicketResource::collection($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
        ]);

        $ticket = Ticket::create([
            ...$validated,
            'client_id' => $request->user()->client->id,
            'status' => 'open',
        ]);

        event(new TicketCreated($ticket));

        return new TicketResource($ticket->load(['client.user', 'service', 'freelancer.user']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return new TicketResource($ticket->load(['client.user', 'service', 'freelancer.user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'budget' => 'sometimes|numeric|min:0',
            'deadline' => 'sometimes|date|after:today',
            'status' => 'sometimes|in:open,in_progress,completed,cancelled',
            'freelancer_id' => 'sometimes|exists:freelancers,id',
        ]);

        $ticket->update($validated);

        event(new TicketUpdated($ticket));

        return new TicketResource($ticket->load(['client.user', 'service', 'freelancer.user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully'], 200);
    }
}
