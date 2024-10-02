<?php

namespace App\Http\Controllers;

use App\Http\Requests\RdsRequest;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Rd;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RendezVousController extends Controller
{


    public function index(){

      //  $commercial =  Auth::user();
      //  $aujourdhui = Carbon::today();

        //Récupérer les rendez-vous du jour pour le commercial connecté
      //  $rendezvous = Rd::where('systeme_id',$commercial->id)
                 //   ->whereDate('date_du_rdv',$aujourdhui)
               //     ->get();
       // dd($rendezvous);

       //Envoyer des notifications pour chaque rendez-vous


      // foreach($rendezvous as $rdv)
      // {
      //  $commercial->notify(new RendezVousNotification($rdv));
     //  }

     // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Récupérer le commercial associé à l'utilisateur
    $commercial = $user->commercial; // Le commercial associé à cet utilisateur

    // Vérifiez si le commercial existe
    if (!$commercial) {
        return redirect()->back()->withErrors(['error' => 'Aucun commercial associé à cet utilisateur.']);
    }

    // Récupérer les rendez-vous du commercial connecté
    $mesRendezVous = $commercial->rendezVous()->with('client')->get();

    // Récupérer les collaborateurs et leurs rendez-vous
    $rendezVousCollaborateurs = collect(); // Créer une collection vide

    // Vérifiez si le commercial a des collaborateurs
    if ($commercial->collaborateurs()->count() > 0) {
        // Si le commercial a des collaborateurs, on récupère leurs rendez-vous
        $collaborateurs = $commercial->collaborateurs()->with('rendezVous.client')->get();
        $rendezVousCollaborateurs = $collaborateurs->flatMap(function ($collaborateur) {
            return $collaborateur->rendezVous()->with('client')->get();
        });
    }

    // Fusionner les rendez-vous du commercial et ceux de ses collaborateurs
    $tousLesRendezVous = $mesRendezVous->merge($rendezVousCollaborateurs);

    // Passer les rendez-vous à la vue


    // $id = Auth::user()->id;
     //   $rds = Rd::with('client')->orderBy('created_at', 'desc')->where('user_id',$id)->get();
      //  $date = Carbon::parse($rds->date_du_rdv); // Convertir en objet Carbon
        return view('rds.index', compact('tousLesRendezVous'));
    }


  public function create(){
    $clients = Client::all();
    $produits = Produit::all();
    return view('rds.create', compact('clients','produits'));
  }



  public function store(RdsRequest $request,Rd $rd){

      // dd($request);

      $date = $request->date_du_rdv;
     // dd($date);
      $formattedDate = date('Y-m-d', strtotime($date));

        $user = User::where('id', Auth::user()->id)->first();
        $rd->date_du_rdv = $formattedDate;
        $rd->client_id =  $request->client_id;
        $rd->produit_id =  $request->produit_id;
        $rd->notes = $request->notes;
        $rd->prime = $request->prime;
        $rd->user_id = $user->id;
        $rd->save();

        return redirect()->route("rds.index")->with("success","Rendez-vous  enregistré avec succès");

  }


   public function edit(Rd $rd){

    $clients = Client::all();
    return view('rds.edit', compact('rd','clients'));

   }


   public function update(RdsRequest $request,Rd $rd){

    $date = $request->date_du_rdv;
    // dd($date);
     $formattedDate = date('Y-m-d', strtotime($date));

       $user = User::where('id', Auth::user()->id)->first();
       $rd->date_du_rdv = $formattedDate;
       $rd->produit_id =  $request->produit_id;
       $rd->client_id =  $request->client_id;
       $rd->notes = $request->notes;
       $rd->prime = $request->prime;
       $rd->user_id = $user->id;
       $rd->update();

       return redirect()->route("rds.index")->with("success","Rendez-vous modifié avec succès");

   }


   public function delete(Rd $rd ){

     $rd->delete();
    return redirect()->route("rds.index")->with("success","Rendez-vous supprimé avec succès");

   }




}