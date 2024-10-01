<?php

namespace App\Http\Controllers;

use App\Models\Compagnie;
use App\Models\Condition;
use App\Models\Conditiongroupe;
use App\Models\Liaison;
use App\Models\Produit;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductionController extends Controller
{
    public function newproduction($id)
    {
        $produit = Produit::find($id);
        $conditions = $produit->conditions;

        $compagnies = Compagnie::all(); // Récupère toutes les compagnies disponibles
        return view('productions.newproduction', ['compagnies' => $compagnies, 'conditions' => $conditions]);
    }

    public function getTarif(Request $request)
    {
        $groupe = $request->input('groupe');
        $compagnies = $request->input('compagnie', []);

        if (empty($groupe)) {
            return response()->json(['infos' => 'Aucune condition sélectionnée.']);
        }
        if (empty($compagnies)) {
            return response()->json(['infos' => 'Aucune compagnie sélectionnée.']);
        }
        $infos = [];
        foreach ($compagnies as $compagnieId) {
            $tarifM = Tarif::where('conditiongroupe_id', $groupe)->where('compagnie_id', $compagnieId)->first();
            $tarifT = $tarifM->tarif;
            $reduction = $tarifM->reduction;

            $calcul = ($tarifT * $reduction) / 100;

            $tarifB = $tarifT - $calcul;
            $tarif = number_format($tarifB, 0, ',', '.') . ' FCFA';

            $infos[] = [
                'compagnie' => $tarifM->compagnie->nom,
                'prime' => $tarif,
                'reduction' => $tarifM->reduction . " %",
            ];
        }
        // $i = 0;
        // foreach ($infos as $info) {
        //     $i += 1;
        // }

        if ($infos) {
            return response()->json(['infos' => $infos]);
        } else {
            return response()->json(['infos' => 'Pas de tarif trouvé pour cette combinaison et compagnie.']);
        }
    }

    public function getGroup(Request $request)
    {
        // $conditionvaleurs = array_filter($request->except('_token', 'compagnie'));
        // $compagnieId = $request->input('compagnie'); // Récupère l'ID de la compagnie sélectionnée
        $rules = [
            'condition' => 'required',

        ];
        $messages = [
            'condition.required' => 'Veuillez sélectionner les conditions',
        ];

        $validated = $request->validate($rules, $messages);

        $conditionvaleurs = $validated['condition'];

        // $compagnieId = $validated['compagnie'];

        // $groupes = Conditiongroupe::whereHas('tarif', function ($query) use ($compagnieId) {
        //     $query->where('compagnie_id', $compagnieId);
        // });

        $groupes = Conditiongroupe::with('tarifs');

        foreach ($conditionvaleurs as $conditionId => $valeur) {
            $groupes->whereHas('liaisons', function ($query) use ($valeur) {
                $query->where('conditionvaleur_id', $valeur);
            });
        }

        $groupes = $groupes->get();
        $echantillon = $groupes->first();

        //dd($groupes);
        $compagnies = Compagnie::all();
        if ($echantillon) {
            foreach ($compagnies as $compagnie) {

                foreach ($echantillon->tarifs as $tarif) {
                    if ($tarif->compagnie == $compagnie) {
                        $compagnie['statut'] = true;
                    }
                }
            }
            $etat = 1;
        } else {
            $etat = 0;
        }

        // $compagnies = $compagnies->get();
        //dd($compagnies);


        //dd($groupe->tarif->compagnie_id);
        // dd($groupe->tarif->tarif);

        return view('productions.newproduction', ['groupes' => $groupes, "compagnies" => $compagnies, "etat" => $etat]);
    }
}
