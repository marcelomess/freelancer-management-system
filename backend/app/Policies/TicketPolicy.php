<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Cliente que criou ou freelancer atribuído
        return ($user->isClient() && $ticket->client_id === $user->client->id) ||
               ($user->isFreelancer() && $ticket->freelancer_id === $user->freelancer->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Cliente pode atualizar antes de ser atribuído, freelancer pode atualizar status
        return ($user->isClient() && $ticket->client_id === $user->client->id) ||
               ($user->isFreelancer() && $ticket->freelancer_id === $user->freelancer->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Apenas o cliente que criou pode deletar
        return $user->isClient() && $ticket->client_id === $user->client->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->isClient() && $ticket->client_id === $user->client->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->isClient() && $ticket->client_id === $user->client->id;
    }
}
