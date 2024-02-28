<?php

namespace App\Http\Controllers\api;

use Exception;
use App\Models\User;
use App\Models\Projet;
use Illuminate\Http\Request;
use App\Models\Invertissement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\EditeProjetRequest;
use App\Http\Requests\CreateProjetRequest;
use App\Mail\InvestissementMail;
use App\Mail\PropositionAcceptee;
use App\Notifications\InvestissementNotification;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="EndPoint de Invertissement ", version="0.1")
 */
class InvertissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/investissement/liste",
     *     summary= "Cette route permet de lister les investissement d'un bailleur donné",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {

        $user = auth()->user();
        $investissements = Invertissement::where('user_id', $user->id)->get();

        return response()->json([
            "message" => "Vos proposition d'investissement",
            "datas" => $investissements
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/investissement/{projet}",
     *     summary= "Cette route permet d'ajouter un investissement",
     *     @OA\Response(response="201", description="Investissement Créer avec succès")
     * )
     */
    public function store(EditeProjetRequest $request, string $id)

    {
        $user = auth()->user();
        $projet = Projet::findOrFail($id);
        // $porteur = $projet->user_id;
        if ($projet->etat == 'Disponible') {

            $projet_id = $projet->id;
            $user_id = $user->id;

            // try {
            $investissement = new Invertissement();
            $investissement->montant = $request->montant;
            $investissement->description = $request->description;
            $investissement->projet_id = $projet_id;
            $investissement->user_id = $user_id;
            if ($investissement->save()) {
                $porteur = User::find($projet['user_id']);
                $envoi = Mail::to($porteur->email)->send(new InvestissementMail());
                if ($envoi) {
                    return response()->json([
                        "message" => "c'est bon email envoyé"
                    ]);
                } else {
                    return response()->json([
                        "message" => "C'est pas bon"
                    ]);
                }
            }
        } else {
            return response()->json([
                "message" => "Ce projet n'est pas disponible"
            ]);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/investissement/{id}",
     *     summary= "Cette route permet de consulter le detail d'un investissement",
     *  @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'incestissement",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function show(string $id)
    {

        $investissement = Invertissement::findorFail($id);
        $projet = $investissement->projet->nom;
        $user = auth()->user();
        if ($investissement->user_id == $user->id) {

            return response()->json([
                "message" => "Details de la proposition",
                "datas" => [
                    "montant" => $investissement->montant,
                    "description" => $investissement->description,
                    "projet" => $projet,
                    'date' => $investissement->created_at,
                    'statut' => $investissement->status
                ]
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * @OA\Put(
     *     path="/api/refuserProposition/{id}",
     *     summary= "Cette route permet à un porteur de projet de refuser un investissement",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'investissement",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function refuser(string $id)
    {

        $investissement = Invertissement::findOrFail($id);
        if ($investissement->status !== "Refuer") {
            $investissement->status = "Refuser";
            if ($investissement->save()) {

                $bailler = User::find($investissement['user_id']);
                $envoi = Mail::to($bailler->email)->send(new PropositionAcceptee());
                if ($envoi) {
                    return response()->json([
                        "message" => "Proposition refuser"
                    ]);
                }
            }
        }
    }

    /**
     * @OA\Put(
     *     path="/api/accepterProposition/{id}",
     *     summary= "Cette route permet à un porteur de projet d'accepter un investissement",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'incestissement",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function valider(string $id)
    {
        $investissement = Invertissement::findOrFail($id);
        if ($investissement->status !== "Accepter") {
            $investissement->status = "Accepter";
            if ($investissement->save()) {
                $projet = $investissement->projet;
                $projet->etat = 'Finance';
                $projet->save();
                $bailler = User::find($investissement['user_id']);
                $envoi = Mail::to($bailler->email)->send(new PropositionAcceptee());
                if ($envoi) {
                    return response()->json([
                        "message" => "Proposition acceptée"
                    ]);
                }
            }
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invertissement $invertissement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * /supprimer/investissement/{id}
     */
    /**
     * @OA\Delete(
     *     path="/api/supprimer/investissement/{id}",
     *     summary= "Cette route permet de supprimer un investissement specifique",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'incestissement",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $investissement = Invertissement::findorFail($id);
        if ($investissement->user_id == $user->id) {
            if ($investissement->delete()) {
                return response()->json([
                    "message" => "Suppression effectuer"
                ]);
            }
        }
    }
}
