<?php

namespace App\Http\Controllers\api;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCtegorieRequest;
use App\Models\Projet;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="EndPoint de Categorie ", version="0.1")
 */
class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/listeCtagorie",
     *     summary= "Lister les categories et afficher leur nombre",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {
        $categorie = Categorie::all();
        $nombre = Categorie::all()->count();
        return response()->json([
            "statut" => 1,
            "nombre" => $nombre,
            "categorie" => $categorie

        ]);
    }

    public function ProjetParCategorie(Request $request)
    {
        $categorieId = $request->input('categorie_id');

        if ($categorieId) {
            $projets = Projet::where('categorie_id', $categorieId)->get();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Projets récupérés avec succès',
                'projets' => $projets
            ]);
        } else {
            return response()->json([
                'status_code' => 400,
                'status_message' => 'ID de catégorie manquant'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/ajouterCategorie",
     *     summary= "Cette route permet d'ajouter une categorie",
     *     @OA\Response(response="201", description="Enregistrement Effectué avec succès")
     * )
     */

    public function store(CreateCtegorieRequest $request)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);

        $categorie = new Categorie();
        $categorie->libelle = $request->libelle;
        if ($categorie->save()) {
            return response()->json([
                "statut" => 1,
                "message" => "Categorie ajoutée"
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/ModifierCategorie/{id}",
     *     summary= "Cette route permet de modifier une categorie",
     *  @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categorie",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="Succes")
     * )
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);
        $categorie = Categorie::findorFail($id);
        $categorie->libelle = $request->libelle;
        if ($categorie->save()) {
            return response()->json([
                "message" => "Modification effectuée"
            ]);
        } else {
            return response()->json([
                "message" => "Modification non effectuée"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/SupprimerCategorie/{id}",
     *     summary= "Cette route permet de supprimer une categorie",
     *  @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categorie",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="Succes")
     * )
     */
    public function destroy(Request $request, string $id)
    {
        $categorie = Categorie::findorFail($id);
        if ($categorie->delete()) {
            return response()->json([
                "Statut" => 1,
                "massage" => "Suppression effectuer"
            ]);
        }
    }
}
