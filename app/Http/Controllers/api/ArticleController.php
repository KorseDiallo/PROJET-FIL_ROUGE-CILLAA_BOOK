<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\newslellerEmail;
use App\Models\Commentaire;
use App\Models\NewsLetter;
use App\Notifications\InfoNewsArticle;
use Illuminate\Support\Facades\Mail;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="endPoind de article", version="0.1")
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/voirArticle",
     *     summary="liste de tout les articles et afficher le nombre d'article",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function index()
    {
        $article = Article::all();
        $totalArticle = $article->count();

        return response()->json([
            "status" => 1,
            "message" => "voici vos articles",
            "Total" => $totalArticle,
            "data" => $article
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
     * @OA\Post(
     *     path="/api/ajouterArticle",
     *     summary="ajouter un article",
     *     @OA\Response(response="201", description="enregistrer avec succes")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'photo' => 'required',
            'description' => 'required|string'
        ]);

        $article = new Article();
        $article->titre = $request->titre;
        $imageData = $request->photo;
        $imageName = time() . '.jpeg';
        file_put_contents(public_path('image/' . $imageName), $imageData);
        $article->photo = "image/" . $imageName;
        $article->description = $request->description;

        if ($article->save()) {
            // Envoie Email aux abonnées du newsletters

            $newsletters = NewsLetter::all();
            foreach ($newsletters as $newsletter) {
                Mail::to($newsletter->email)->send(new newslellerEmail());
            }

            return response()->json([
                "status" => 1,
                "message" => "L'article a été ajouter avec succès",
                "data" => $article
            ]);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\get(
     *     path="/api/detailArticle/{article}",
     *     summary="details d'un article",
     *  @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function show(Article $article)
    {
        $commentaires = Commentaire::where('article_id', $article->id)->get();

        return response()->json([
            "message" => "Les Articles",
            "articles" => $article,
            "commentaire" => $commentaires
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\put(
     *     path="/api/modifierArticle/{article}",
     *     summary="modifier un article",
     *  @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'photo',
            'description' => 'required|string'
        ]);

        $article->titre = $request->titre;
        $imageData = $request->photo;
        $article->description = $request->description;
        if ($request->hasFile('image')) {
            $imageData = $request->photo;
            $imageName = time() . '.jpeg';
            file_put_contents(public_path('image/' . $imageName), $imageData);
            $article->photo = "image/" . $imageName;
        }


        if ($article->update()) {
            return response()->json([
                "status" => 1,
                "message" => "L'article a été modifier avec succès",
                "data" => $article
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\delete(
     *     path="/api/supprimerArticle/{article}",
     *     summary="supprimer un article",
     *  @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Article $article)
    {
        if ($article->delete()) {
            return response()->json([
                "status" => 1,
                "message" => "L'article a été supprimer avec succès",
                "data" => $article
            ]);
        }
    }
}
