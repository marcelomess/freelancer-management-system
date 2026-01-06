<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FreelancerResource;
use App\Models\Freelancer;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Freelancer::with(['user', 'services'])
            ->withCount('services');

        // Filtrar por skills
        if ($request->has('skills')) {
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            $query->where(function($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('skills', trim($skill));
                }
            });
        }

        // Filtrar por hourly_rate
        if ($request->has('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }

        if ($request->has('max_rate')) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }

        // Ordenar por avaliação
        if ($request->get('sort') === 'rating') {
            $query->orderBy('average_rating', 'desc');
        }

        return FreelancerResource::collection($query->paginate(15));
    }

    /**
     * Display the specified resource.
     */
    public function show(Freelancer $freelancer)
    {
        return new FreelancerResource($freelancer->load(['user', 'services'])->loadCount('services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Freelancer $freelancer)
    {
        // Apenas o próprio freelancer pode atualizar seu perfil
        if ($request->user()->freelancer?->id !== $freelancer->id) {
            return response()->json(['message' => 'You can only update your own profile'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'skills' => 'sometimes|array',
            'skills.*' => 'string',
        ]);

        $freelancer->update($validated);

        return new FreelancerResource($freelancer->load(['user', 'services']));
    }
}
