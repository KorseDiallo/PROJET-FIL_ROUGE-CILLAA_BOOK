<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Projet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="EndPoint de Projet ", version="0.1")
 */
class ProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/projets",
     *     summary= "Cette route permet de lister tous les projets",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        //return Projet::with('User')->get();
        $projets  = Projet::all();

        return response()->json($projets);
    }


    /**
     * @OA\Get(
     *     path="/api/projetsDisponibles",
     *     summary= "Cette route permet de lister tous les projets dispobiles",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function projetDispobile()
    {
        $projets = Projet::where('etat', 'Disponible')->get();
        return response()->json([
            "statut" => 1,
            "message" => "Les projets disponible",
            "Projets" => $projets
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */

    public function creer(Request $request): \Illuminate\Http\JsonResponse
    {


        $request->validate([
            'nom' => 'required',
            'image' => 'required',
            'objectif' => 'required',
            'description' => 'required',
            'echeance' => 'required',
            'budget' => 'required|numeric',
            'etat' => 'in:Disponible,Financé',
            'categorie_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $projet = Projet::create($request->all());
        return response()->json(['message' => 'Projet create successfully', 'projet' => $projet], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/projets",
     *     summary= "Cette route permet de créer des projet",
     *     @OA\Response(response="201", description="le projet a été creer avec succès")
     * )
     */

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $data = $request->validate([
            'nom' => 'required',
            'image' => 'required',
            'objectif' => 'required',
            'description' => 'required',
            'echeance' => 'required',
            'budget' => 'required|numeric',
            'etat' => 'in:Disponible,Financé',
            'categorie_id' => 'required|exists:categories,id',
            // 'user_id' => 'required|exists:users,id'
        ]);
        $data['user_id'] = $user->id;
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('images', 'public');
            $data['image'] = $image_path;
        }
        $projet = Projet::create($data);

        return response()->json(['message' => 'Projet create successfully', 'projet' => $projet], 201);
    }


    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/projets/{projet}",
     *     summary= "Cette route permet de voir le detail d'un projet",
     * @OA\Parameter(
     *         name="projet",
     *         in="path",
     *         required=true,
     *         description="ID du projet",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function show(Projet $projet): \Illuminate\Http\JsonResponse
    {
        return response()->json($projet);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/projets/{projet}",
     *     summary= "Cette route permet de modifier un projet specifique",
     * @OA\Parameter(
     *         name="projet",
     *         in="path",
     *         required=true,
     *         description="ID du projet",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function update(Request $request, Projet $projet): \Illuminate\Http\JsonResponse
    {
        //update avec validation
        $user = auth()->user();
        $datas =  $request->validate([
            'nom' => 'required',
            'image',
            'objectif' => 'required',
            'description' => 'required',
            'echeance' => 'required',
            'budget' => 'required|numeric',
            'etat' => 'in:Disponible,Financé',
            'categorie_id' => 'required|exists:categories,id',
            'user_id'
        ]);

        if ($projet->user_id == $user->id) {

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $validatedData['image'] = $imagePath;
            }
            $projet->update($datas);

            return response()->json([
                'message' => 'Projet mis à jour avec succès', 'projet' => $projet
            ]);
        } else {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à mettre à jour ce projet.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/projet/{projet}",
     *     summary= "Cette route permet de supprimer un projet donnée",
     * @OA\Parameter(
     *         name="projet",
     *         in="path",
     *         required=true,
     *         description="ID du projet",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Projet $projet): \Illuminate\Http\JsonResponse
    {

        $user = auth()->user();

        if ($projet->user_id == $user->id) {
            $projet->delete();
            return response()->json([
                "message" => "Projet supprimer avec succes"
            ]);
        } else {
            return response()->json([
                "message" => "Suppression impossible vous n'êtes pas proprietaire de ce projet"
            ]);
        }
    }
}
