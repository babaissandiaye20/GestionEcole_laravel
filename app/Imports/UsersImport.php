<?php
namespace App\Imports;

use App\Services\UserFirebaseServiceInterface;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
class UsersImport implements ToCollection
{
    protected $userService;

    public function __construct(UserFirebaseServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [
                'nom' => $row[0],
                'prenom' => $row[1],
                'email' => $row[2] ?? null,
                'password' => $row[3],
                'telephone' => $row[4],
                'fonction' => $row[5],
                'statut' => $row[6],
            ];

            if (!$data['email']) {
                Log::error('Missing email for user creation.');
                continue;
            }

            try {
                // Créer l'utilisateur
                $this->userService->createUser($data);
            } catch (\Exception $e) {
                Log::error("Failed to create user for email {$data['email']}: " . $e->getMessage());
                continue; // Skip this row and move on to the next
            }
        }
    }

}
