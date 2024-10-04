<?php

namespace App\Http\Controllers;

use App\Models\Rd;
use App\Models\User;
use App\Notifications\RendezVousNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function checkRendezVous()
    {


        $tommorrow = now()->addDay()->format('Y-m-d');

        // Récupérer les rendez-vous d'aujourd'hui
        $rendezVous = Rd::whereDate('date_du_rdv', $tommorrow)->get();

        foreach ($rendezVous as $rendez) {
            // Envoyer l'email
            $destinataire = User::find($rendez->commercial_id); // Remplace par le nom réel
            $destinataire->notify(new RendezVousNotification($rendez));

           // Mail::to('chef@example.com')->send(new RendezVousNotification($clientName, $today->format('Y-m-d')));
        }


        
        $today = Carbon::today();

        // Récupérer les rendez-vous d'aujourd'hui
        $rendezVous = Rd::whereDate('date_du_rdv', $today)->get();
         
        foreach ($rendezVous as $rendez) {
            // Créer la notification dans la table
      //      Notification::create([
      //          'systeme_id' =>  $rendez->systeme_id,
      //          'prospect_id' =>  $rendez->prospect_id,
      //          'message' => "Vous avez un rendez-vous  aujourd'hui.",
    //      ]);
    $commercial = $rendez->commercial;
            // Envoyer l'email
            //$destinataire = User::find($rendezVous->commercial_id); // Remplace par le nom réel
            if($commercial){
                $commercial->notify(new RendezVousNotification($rendez));
            }
            else{
                return 'Aucun commercial trouvé pour ce rendez-vous';
            }
           

           // Mail::to('chef@example.com')->send(new RendezVousNotification($clientName, $today->format('Y-m-d')));
        }

        return response()->json(['message' => 'Notifications envoyées !']);
    }



    public function markAsRead($id)
{
    // Récupérer la notification par son ID
    $notification = Auth::user()->notifications->find($id);

    // Marquer la notification comme lue
    if ($notification) {
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    return redirect()->back()->with('error', 'Notification non trouvée.');
}













}