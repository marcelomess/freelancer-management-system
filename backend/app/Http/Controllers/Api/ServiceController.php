<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Listar serviços",
     *     description="Lista todos os serviços com filtros avançados",
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="ID da categoria",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Preço mínimo",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Preço máximo",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="min_rating",
     *         in="query",
     *         description="Avaliação mínima",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do serviço",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de serviços",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Service::with(['freelancer.user', 'category']);

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->byPriceRange($request->min_price, $request->max_price);
        }

        if ($request->has('min_rating')) {
            $query->byRating($request->min_rating);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        $services = $query->paginate($request->get('per_page', 15));

        return ServiceResource::collection($services);
    }

    /**
     * @OA\Post(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Criar serviço",
     *     description="Freelancer cria novo serviço/portfólio com imagens",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category_id","title","description","price","delivery_time"},
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Desenvolvimento Web"),
     *                 @OA\Property(property="description", type="string", example="Crio sites profissionais"),
     *                 @OA\Property(property="price", type="number", example=500.00),
     *                 @OA\Property(property="delivery_time", type="integer", example=7),
     *                 @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Serviço criado com sucesso",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=403, description="Não autorizado")
     * )
     */
    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|integer|min:1',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $service = $request->user()->freelancer->services()->create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $service->addMedia($image)->toMediaCollection('portfolio');
            }
        }

        return new ServiceResource($service->load(['freelancer.user', 'category']));
    }

    /**
     * @OA\Get(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Detalhes do serviço",
     *     description="Obtém detalhes de um serviço específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do serviço",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do serviço",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Serviço não encontrado")
     * )
     */
    public function show(Service $service)
    {
        return new ServiceResource($service->load(['freelancer.user', 'category']));
    }

    /**
     * @OA\Put(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Atualizar serviço",
     *     description="Freelancer atualiza seu próprio serviço",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do serviço",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="delivery_time", type="integer"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Serviço atualizado com sucesso",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=403, description="Não autorizado")
     * )
     */
    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'delivery_time' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $service->update($validated);

        return new ServiceResource($service->load(['freelancer.user', 'category']));
    }

    /**
     * @OA\Delete(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Deletar serviço",
     *     description="Freelancer deleta seu próprio serviço",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do serviço",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Serviço deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Não autorizado")
     * )
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }
}
