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

     $id = Auth::user()->id;

     //Récuperer les rendez-vous du commercial connecté
     
     $mesRendezVous = Rd::with('client')->orderBy('created_at', 'desc')->where('user_id',$id)->get();

// Si le commercial est un chef, récupérer les rendez-vous des collaborateurs
$rendezVousCollaborateurs = $id->collaborateurs->flatMap(function ($collaborateur) {
    return $collaborateur->rds()->with('client')->get();
});

// Fusionner les deux collections
$tousLesRendezVous = $mesRendezVous->merge($rendezVousCollaborateurs);



        
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