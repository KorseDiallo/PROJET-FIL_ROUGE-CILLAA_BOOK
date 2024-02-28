<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Promise\Create;
use App\Http\Controllers\Controller;
use App\Models\Invertissement;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="endPoind de user", version="0.1")
 */

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="liste de tout les users",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function index()
    {
        $users = User::all();
        return response()->json([
            "statut" => 1,
            "message Liste des users",
            "Users" => $users
        ]);
    }


    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/listePorteurs",
     *     summary="liste des porteurs et leur nombre de projets par l'admin",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function listePorteur()
    {
        $porteurs = User::where('role', 'Porteur')->get();
        $nombre = User::where('role', 'Porteur')->count();
        return response()->json([
            "statut" => 1,
            "nombre" => $nombre,
            "Porteurs" => $porteurs
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/listeBailleurs",
     *     summary="liste des bailleurs et leur nombre de projets par l'admin",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function listeBailleur()
    {
        $bailleurs = User::where('role', 'Bailleur')->get();
        $nombre = User::where('role', 'Bailleur')->count();
        return response()->json([
            "statut" => 1,
            "nombre" => $nombre,
            "Porteurs" => $bailleurs
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboardBailleur",
     *     summary="Dashoard du bailleur avec ces information et les investissements qu'il a proposé",
     *     @OA\Response(response="200", description="succes")
     * )
     */


    public function dashBordBailleur()
    {
        $user = auth()->user();
        $investissements = Invertissement::where('user_id', $user->id)->get();
        return response()->json([
            "message" => "Bienvenue sur ton Dashboard",
            "data" => $user,
            "Investissements" => $investissements
        ]);
    }



    /**
     * @OA\Get(
     *     path="/api/dashboardPorteur",
     *     summary="Dashoard du porteur avec ces information et ses projets",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function dashBordPorteur()
    {
        $user = auth()->user();
        $projet = Projet::where('user_id', $user->id)->get();
        return response()->json([
            "message" => "Bienvenue sur ton Dashboard",
            "data" => $user,
            "projets" => $projet
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboardAdmin",
     *     summary="Dashoard de l'admin avec ces information",
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function dashBordAdmin()
    {
        $user = auth()->user();
        return response()->json([
            "message" => "Bienvenue sur ton Dashboard",
            "data" => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user =  User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->role === 'Bailleur') {

                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        "statut" => 1,
                        "massage" => "Vous êtes connecté en tant que Bailleur",
                        "token" => $token,
                        "datas" => $user
                    ]);
                } elseif ($user->role === 'Porteur') {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        "statut" => 1,
                        "massage" => "Vous êtes connecté en tant que Porteur de projet",
                        "token" => $token
                    ]);
                } else {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        "statut" => 1,
                        "massage" => "Vous êtes connecté en tant que Admin",
                        "token" => $token
                    ]);
                }
            } else {
                return response()->json([
                    "statut" => 0,
                    "massage" => "Mot de passe Incorrect"
                ]);
            }
        } else {
            return response()->json([
                "massage" => "Email Introuvable"
            ]);
        }
    }

    /**
     * @OA\post(
     *     path="/api/login",
     *     summary="connexion d'un user",
     *     @OA\Response(response="200", description="succes")
     * )
     */


    public function Connexion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 401);
        }

        if (Auth::attempt($request->all())) {

            $user = Auth::user();

            if ($user->role === "Bailleur" && $user->est_bloque == 0) {
                $success =  $user->createToken('MyApp')->plainTextToken;
                return response([
                    'message' => 'Vous êtes connecté en tant que bailleur',
                    'token' => $success,
                    'datas' => $user
                ], 200);
            } elseif ($user->role === "Porteur" && $user->est_bloque == 0) {
                $success =  $user->createToken('MyApp')->plainTextToken;
                return response([
                    'message' => 'Vous êtes connecté en tant que Porteur de projet',
                    'token' => $success,
                    'datas' => $user
                ], 200);
            } elseif ($user->role === "Admin") {
                $success =  $user->createToken('MyApp')->plainTextToken;
                return response([
                    'message' => 'Vous êtes connecté en tant que Admin',
                    'token' => $success,
                    'datas' => $user
                ], 200);
            } else {
                return response()->json([
                    "message" => "Votre compte est bloqué"
                ]);
            }
        }


        return Response(['message' => 'email or password wrong'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\post(
     *     path="/api/inscription",
     *     summary="inscription d'un user",
     *     @OA\Response(response="200", description="enregistrer succes")
     * )
     */


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'image' => 'required',
            'description' => 'nullable|string',
            'telephone' => 'required|string|max:255',
            'role' => 'required|in:Porteur,Bailleur,Admin',
            'organisme' => 'nullable|in:ONG,Entreprise,Particulier',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->description = $request->description;
        $user->telephone = $request->telephone;
        $user->role = $request->role;
        $user->organisme = $request->organisme;
        $imageData = $request->image;
        $imageName = time() . '.jpeg';
        file_put_contents(public_path('image/' . $imageName), $imageData);
        $user->image = "image/" . $imageName;

        if ($user->save()) {
            return response()->json([
                "status" => "ok",
                "message" => "c'est bon",
                "data" => $user
            ]);
        }
    }



    /**
     * @OA\post(
     *     path="/api/logout",
     *     summary="deconnexion d'un user",
     *     @OA\Response(response="200", description="decconnexion succes")
     * )
     */

    public function logout(): Response
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return Response(['message' => 'Deconnexion effectuée'], 200);
    }

    /**
     * Display the specified resource.
     */


    /**
     * @OA\get(
     *     path="/api/info/{id}",
     *     summary="information de profil d'un user",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */


    public function show(string $id)
    {
        $user = User::findorFail($id);
        if ($user->role === "Bailleur") {
            return response()->json([
                "statut" => 1,
                "message" => "information du bailleur",
                "datas" => $user
            ]);
        } elseif ($user->role === "Porteur") {
            return response()->json([
                "statut" => 1,
                "message" => "information du Porteur",
                "datas" => $user
            ]);
        } elseif ($user->role === "Admin") {
            return response()->json([
                "statut" => 1,
                "message" => "information du Admin",
                "datas" => $user
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * @OA\post(
     *     path="/api/bloquerUser{id}",
     *     summary="Bloquer un user par l'admin",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function bloquerUser(string $id)
    {
        $user = User::findorFail($id);
        if ($user->role == "Bailleur" || $user->role == "Porteur") {

            $user->est_bloque = 1;
            $user->save();

            return response()->json([
                "statut" => 1,
                "Message" => "user bloqué"
            ]);
        }
    }

    /**
     * @OA\post(
     *     path="/api/debloquerUser{id}",
     *     summary="Débloquer un user par l'admin",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function debloquerUser(string $id)
    {
        $user = User::findorFail($id);
        if ($user->role == "Bailleir" || $user->role == "Porteur") {

            $user->est_bloque = 0;
            $user->save();

            return response()->json([
                "statut" => 1,
                "Message" => "user debloqué"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * @OA\put(
     *     path="/api/modifierProfil/{id}",
     *     summary="Modifier le profil d'un user",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */


    public function update(Request $request, string $id)
    {
        $user =  User::findorFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'image' => 'required|string',
            'description' => 'nullable|string',
            'telephone' => 'required|string|max:255',
            'role' => 'required|in:Porteur,Bailleur,Admin',
            'organisme' => 'nullable|in:ONG,Entreprise,Particulier',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->description = $request->description;
        $user->telephone = $request->telephone;
        $user->role = $request->role;
        $user->organisme = $request->organisme;

        if ($request->hasFile('image')) {
            $imageData = $request->image;
            $imageName = time() . '.jpeg';
            file_put_contents(public_path('image/' . $imageName), $imageData);
            $user->image = "image/" . $imageName;
        }

        $userAuth = Auth()->user();
        if ($userAuth->id == $user->id) {
            $user->save();
            return response()->json([
                "statut" => 1,
                "message" => "Modification effectuée",
                "datas" => $user
            ]);
        } else {
            return response()->json([
                "message" => "c'est pas pour toi"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
