<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['ticket', 'reviewer', 'reviewee']);

        // Filtrar por freelancer avaliado
        if ($request->has('freelancer_id')) {
            $query->where('reviewee_id', $request->freelancer_id);
        }

        return ReviewResource::collection($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Verificar se o ticket está completo
        $ticket = Ticket::findOrFail($validated['ticket_id']);
        
        if ($ticket->status !== 'completed') {
            return response()->json(['message' => 'Only completed tickets can be reviewed'], 422);
        }

        // Verificar se o usuário faz parte do ticket
        $user = $request->user();
        $isParticipant = $ticket->client_id === $user->client?->id || 
                        $ticket->freelancer_id === $user->freelancer?->id;

        if (!$isParticipant) {
            return response()->json(['message' => 'You can only review tickets you participated in'], 403);
        }

        // Verificar se já existe avaliação
        $existingReview = Review::where('ticket_id', $ticket->id)
            ->where('reviewer_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this ticket'], 422);
        }

        $review = Review::create([
            ...$validated,
            'reviewer_id' => $user->id,
        ]);

        // Atualizar média de avaliação do freelancer
        if ($ticket->freelancer) {
            $ticket->freelancer->updateAverageRating();
        }

        return new ReviewResource($review->load(['ticket', 'reviewer', 'reviewee']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return new ReviewResource($review->load(['ticket', 'reviewer', 'reviewee']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Reviews não podem ser editados após criação
        return response()->json(['message' => 'Reviews cannot be updated'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Apenas o autor pode deletar sua avaliação
        if ($review->reviewer_id !== $request()->user()->id) {
            return response()->json(['message' => 'You can only delete your own reviews'], 403);
        }

        $review->delete();

        // Recalcular média do freelancer
        if ($review->ticket->freelancer) {
            $review->ticket->freelancer->updateAverageRating();
        }

        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
}
