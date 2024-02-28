<?php

namespace App\Http\Controllers\api;

use App\Models\NewsLetter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="endPoind de news letter", version="0.1")
 */
class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/voirNewsLetters",
     *     summary="liste des newsLetters par l'admin",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function index()
    {
        $newsletters = NewsLetter::all();
        $total = $newsletters->count();

        return response()->json([
            "status" => 1,
            "message" => "voici vos articles",
            "Total" => $total,
            "data" => $newsletters
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
     * @OA\post(
     *     path="/api/ajouterNewsLetter",
     *     summary="Incription d'un news letter",
     *     @OA\Response(response="201", description="enregistrer avec succes")
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:news_letters,email|max:255'
        ]);

        $newsletter = new NewsLetter();
        $newsletter->email = $request->email;
        if ($newsletter->save()) {
            return response()->json([
                "status" => 1,
                "message" => "Votre Email a ete Envoyer",
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */


    /**
     * @OA\delete(
     *     path="/api/supprimerNewsLetter/{id}",
     *     summary="Supprimer un news letter par l'admin",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du news letter",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     *   
     * )
     */


    public function supprimer($id)
    {
        $newsLetter = NewsLetter::findOrFail($id);

        if ($newsLetter->delete()) {
            return response()->json([
                "status" => 1,
                "message" => "Email supprimer avec succès"
            ]);
        }
    }
}
