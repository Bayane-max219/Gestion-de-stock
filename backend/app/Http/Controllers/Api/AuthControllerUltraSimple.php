<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthControllerUltraSimple extends Controller
{
    /**
     * Login ultra simple sans base de données
     */
    public function login(Request $request)
    {
        // Headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        try {
            $input = $request->all();
            
            // Simulation login réussi pour franco@gmail.com
            if (isset($input['email']) && $input['email'] === 'franco@gmail.com') {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie (simulation)',
                    'data' => [
                        'id' => 1,
                        'firstName' => 'Franco',
                        'lastName' => 'Glory',
                        'email' => 'franco@gmail.com',
                        'businessName' => 'Franco Pharmacie',
                        'businessType' => 'pharmacie'
                    ],
                    'token' => 'token-franco-123'
                ]);
            }
            
            // Autres emails
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie (test)',
                'data' => [
                    'id' => 2,
                    'firstName' => 'Test',
                    'lastName' => 'User',
                    'email' => $input['email'] ?? 'test@example.com',
                    'businessName' => 'Test Business',
                    'businessType' => 'epicerie'
                ],
                'token' => 'token-test-456'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur login: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Register ultra simple
     */
    public function register(Request $request)
    {
        // Headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        try {
            $input = $request->all();
            
            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie (simulation)',
                'data' => [
                    'id' => 3,
                    'firstName' => $input['firstName'] ?? 'Nouveau',
                    'lastName' => $input['lastName'] ?? 'User',
                    'email' => $input['email'] ?? 'nouveau@example.com',
                    'businessName' => $input['businessName'] ?? 'Nouveau Business',
                    'businessType' => $input['businessType'] ?? 'epicerie'
                ],
                'token' => 'token-nouveau-789'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur register: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Me - Informations utilisateur
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => 1,
                'firstName' => 'Franco',
                'lastName' => 'Glory',
                'email' => 'franco@gmail.com'
            ]
        ]);
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
