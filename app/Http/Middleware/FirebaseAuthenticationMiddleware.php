<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Support\Facades\Auth;

class FirebaseAuthenticationMiddleware 
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json(['error' => 'Token manquant.'], 401);
        }

        $idToken = str_replace('Bearer ', '', $authHeader);

        try {
            // Vérifier et décoder le token
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $firebaseUid = $verifiedIdToken->claims()->get('sub'); // UID Firebase

            // Vous pouvez également récupérer d'autres informations à partir du token
            $email = $verifiedIdToken->claims()->get('email');
            $role = $verifiedIdToken->claims()->get('role'); // Si vous avez stocké le rôle dans le token

            // Ajouter l'utilisateur et son rôle à la requête
            $request->attributes->set('firebaseUid', $firebaseUid);
            $request->attributes->set('email', $email);
            $request->attributes->set('role', $role);

        } catch (InvalidToken $e) {
            return response()->json(['error' => 'Token Firebase invalide.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la validation du token.'], 500);
        }

        return $next($request);
    }
}
