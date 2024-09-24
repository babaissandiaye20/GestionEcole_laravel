<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Firebase\Auth\Token\Exception\InvalidToken;
use App\Services\UserFirebaseServiceInterface;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
class UserFirebaseController extends Controller
{
    protected $userService;
      protected $firebaseAuth;

    public function __construct(UserFirebaseServiceInterface $userService,Auth $firebaseAuth)
    {
        $this->userService = $userService;
          $this->firebaseAuth = $firebaseAuth;

    }

   public function createUser(Request $request)
   {
       // Validation des données
       $validatedData = $request->validate([
           'nom' => 'required|string',
           'prenom' => 'required|string',
           'email' => 'required|email',
           'password' => 'required|string|min:6',
           'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',// Ajout de la validation de la photo
           'telephone'=>'required|string',
           'role' => 'required|string',
           'fonction'=>'nullable|string',

       ]);

       // Gestion de la photo uploadée
       if ($request->hasFile('photo')) {
           $validatedData['photo'] = $request->file('photo');
       }

       // Transmission des données au service pour créer l'utilisateur
       $user = $this->userService->createUser($validatedData,$validatedData['role']);
       return response()->json($user, 201);
   }

    public function getUserById($id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json($user);
    }

   public function updateUser(Request $request, $id)
   {
       $data = $request->all();

       // Valider les champs modifiables
       $validatedData = $request->validate([
           'nom' => 'nullable|string',
           'prenom' => 'nullable|string',
           'email' => 'nullable|email',
           'telephone' => 'nullable|string',
           'fonction' => 'nullable|string',
           'statut' => 'nullable|string',
       ]);

       // Mise à jour des informations de l'utilisateur
       $user = $this->userService->updateUser($id, $validatedData);
       return response()->json($user);
   }


    public function deleteUser($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function findUserByField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $user = $this->userService->findUserByField($field, $value);
        return response()->json($user);
    }

 public function getAllUsers(Request $request)
 {
     $role = $request->query('role'); // Récupérer le rôle depuis les paramètres de requête
     $users = $this->userService->getAllUsers($role);

     return response()->json($users);
 }



public function exportUsers()
{
    return Excel::download(new UsersExport, 'users.xlsx');
}
public function importUsers(Request $request)
{
    $validatedData = $request->validate([
        'file' => 'required|mimes:xlsx,csv',
    ]);

    // Récupération du fichier uploadé
    $file = $request->file('file');

    // Utilisation de Maatwebsite Excel pour lire le fichier
    Excel::import(new UsersImport($this->userService), $file);

    return response()->json(['message' => 'Users imported successfully'], 200);
}
 public function authenticateWithCredentials(Request $request)
    {
        // Validation des entrées
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }

        try {
            Log::info('Tentative de connexion avec l\'email: ' . $request->input('email'));

            // Authentification via Firebase avec email et mot de passe
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
                $request->input('email'),
                $request->input('password')
            );

            // Récupérer l'UID de l'utilisateur Firebase
            $uid = $signInResult->firebaseUserId();
            Log::info('Utilisateur connecté, Firebase UID: ' . $uid);

            // Informations supplémentaires à inclure dans le token
            $payload = [
                'iss' => 'your-app', // Identifiant de l'émetteur
                'sub' => $uid, // Identifiant de l'utilisateur (UID Firebase)
                'iat' => time(), // Timestamp actuel
                'exp' => time() + (60 * 60), // Expiration dans 1 heure
            ];

            // Clé secrète pour signer le JWT (assurez-vous qu'elle est sécurisée)
            $secretKey = env('JWT_SECRET', 'your-secret-key');

            // Générer le token JWT
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            return response()->json([
                'message' => 'Authenticated successfully',
                'token' => $jwt
            ]);

        } catch (UserNotFound $e) {
            Log::error('Utilisateur non trouvé pour l\'email: ' . $request->input('email'));
            return response()->json(['error' => 'User not found'], 404);
        } catch (InvalidPassword $e) {
            Log::error('Mot de passe invalide pour l\'email: ' . $request->input('email'));
            return response()->json(['error' => 'Invalid password'], 401);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'authentification: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
public function updateUserRoleToApprenant(Request $request, $id)
{
    // Valider uniquement l'entrée du rôle
    $validatedData = $request->validate([
        'role' => 'required|string|in:apprenant',
    ]);

    // Récupérer l'utilisateur par son ID
    $user = $this->userService->getUserById($id);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        return response()->json([
            'message' => "L'utilisateur avec l'ID {$id} n'existe pas."
        ], 404);
    }

    // Mettre à jour le rôle de l'utilisateur
    $updatedData = ['role' => 'apprenant'];
    $this->userService->updateUser($id, $updatedData);

    // Générer un fichier PDF avec un QR code contenant les informations de l'utilisateur
    $pdfPath = $this->generateUserPdfWithQrCode($user);

    return response()->json([
        'message' => "Le rôle de l'utilisateur a été mis à jour en apprenant.",
        'user' => $user,
        'pdf_path' => $pdfPath
    ], 200);
}
protected function generateUserPdfWithQrCode($user)
{
    // Générer les informations à inclure dans le QR code
    $qrData = "Nom: {$user['nom']}, Prénom: {$user['prenom']}, Email: {$user['email']}, Téléphone: {$user['telephone']}";

    // Générer le QR code en tant qu'image base64
    $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($qrData));

    // Créer un objet DOMPDF pour générer le fichier PDF
    $dompdf = new Dompdf();
    $html = "
        <h1>Informations de l'utilisateur</h1>
        <p>Nom: {$user['nom']}</p>
        <p>Prénom: {$user['prenom']}</p>
        <p>Email: {$user['email']}</p>
        <p>Téléphone: {$user['telephone']}</p>
        <p>Rôle: Apprenant</p>
        <img src='data:image/png;base64,{$qrCode}' alt='QR Code' />
    ";

    // Charger le HTML dans DOMPDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Sauvegarder le fichier PDF dans le stockage Laravel
    $fileName = 'user_' . $user['email'] . '_details.pdf';
    $filePath = 'pdfs/' . $fileName;

    // Utiliser le système de stockage Laravel pour sauvegarder le PDF
    Storage::put($filePath, $dompdf->output());

    return Storage::url($filePath); // Retourner le chemin d'accès au fichier PDF
}


}
