<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthControllerMySQL extends Controller
{
    /**
     * Login avec vraie base MySQL
     */
    public function login(Request $request)
    {
        // Headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ], 422);
            }

            // Recherche utilisateur dans MySQL
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé dans la base MySQL'
                ], 401);
            }

            // Vérification mot de passe
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 401);
            }

            // Connexion réussie avec données MySQL
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie avec MySQL',
                'data' => [
                    'id' => $user->id,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name,
                    'email' => $user->email,
                    'businessName' => $user->business_name,
                    'businessType' => $user->business_type
                ],
                'token' => 'mysql-token-' . $user->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur MySQL: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Register avec vraie base MySQL
     */
    public function register(Request $request)
    {
        // Headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'businessName' => 'required|string|max:255',
                'businessType' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Création utilisateur dans MySQL
            $user = User::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'business_name' => $request->businessName,
                'business_type' => $request->businessType,
                'accept_terms' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Compte créé dans MySQL',
                'data' => [
                    'id' => $user->id,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name,
                    'email' => $user->email,
                    'businessName' => $user->business_name,
                    'businessType' => $user->business_type
                ],
                'token' => 'mysql-token-' . $user->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur création MySQL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Me - Informations utilisateur MySQL
     */
    public function me(Request $request)
    {
        try {
            // Pour test, retourner info utilisateur ID 1
            $user = User::find(1);
            
            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'firstName' => $user->first_name,
                        'lastName' => $user->last_name,
                        'email' => $user->email,
                        'businessName' => $user->business_name,
                        'businessType' => $user->business_type
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur MySQL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
}
