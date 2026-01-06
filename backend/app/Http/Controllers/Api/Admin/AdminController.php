<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        $stats = [
            'users' => User::count(),
            'freelancers' => User::where('user_type', 'freelancer')->count(),
            'clients' => User::where('user_type', 'client')->count(),
            'services' => Service::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
            'totalTickets' => Ticket::count(),
            'reviews' => Review::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('user_type', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->user_type === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um administrador'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso'
        ]);
    }

    public function services(Request $request)
    {
        $services = Service::with(['freelancer.user', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    public function tickets(Request $request)
    {
        $tickets = Ticket::with(['service', 'client.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    public function reviews(Request $request)
    {
        $reviews = Review::with(['client.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }
}

