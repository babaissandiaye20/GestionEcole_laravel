<?php
namespace App\Exports;


use App\Models\UserFireBase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Facades\FirebaseFacade;
class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Récupérer tous les utilisateurs sous forme de collection
        $users = app('App\Repositories\UserFirebaseRepositoryInterface')->getAllUsers();

        // Reformater les données si nécessaire (conversion en array)
        return collect($users)->map(function ($user) {
            return [
             // Assure-toi que les clés correspondent aux attributs
                'Nom' => $user['nom'],
                'Prénom' => $user['prenom'],
                'Email' => $user['email'],
               // 'statut' => $user['statut'],
            ];
        });
    }

    public function headings(): array
    {
        return [ 'Nom', 'Prénom', 'Email'];
    }
}
