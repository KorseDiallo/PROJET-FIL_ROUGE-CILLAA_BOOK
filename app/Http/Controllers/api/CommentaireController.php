<?php

namespace App\Http\Controllers\api;

use App\Models\Commentaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(title="endPoind de commentaire", version="0.1")
 */

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/commentaires",
     *     summary="liste de tout les commentaires",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index(): JsonResponse
    {
        $commentaires  = Commentaire::all();

        return response()->json($commentaires);
    }


    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\post(
     *     path="/api/ajoutercommentaires",
     *     summary="ajouter commentaires",
     *     @OA\Response(response="201", description="enregistrer avec succes")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom' => 'required',
            'email' => 'required',
            'contenu' => 'required',
            'article_id' => 'required|exists:articles,id'
        ]);
        $commentaire = Commentaire::create($request->all());
        return response()->json(data: ['message' => 'Commentaire create successfully', 'commentaire' => $commentaire], status: 201);
    }

    /**
     * Display the specified resource.
     */


    /**
     * @OA\Get(
     *     path="/api/commentaires/{commentaire}",
     *     summary="Details d'un commentaires",
     *  @OA\Parameter(
     *         name="commentaire",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function show(Commentaire $commentaire): JsonResponse
    {
        return response()->json($commentaire);
    }


    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, Commentaire $commentaire): JsonResponse
    {
        //update avec validation
        $request->validate([
            'nom' => 'required',
            'email' => 'required',
            'contenu' => 'required',
            'article_id' => 'required|exists:articles,id',
        ]);

        // On met à jour un projet existant
        $commentaire->update($request->all());

        return response()->json(['message' => 'Projet update successfully', 'commentaire' => $commentaire], 201);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\delete(
     *     path="/api/commentaires/{commentaire}",
     *     summary="Supprimer un commentaires par l'admin",
     *  @OA\Parameter(
     *         name="commentaire",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Commentaire $commentaire): JsonResponse
    {
        $commentaire->delete();

        return response()->json(['message' => 'Commentaire delete successfully', 'commentaire' => $commentaire]);
    }
}
