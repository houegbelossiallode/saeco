<?php

namespace App\Http\Controllers;

use App\Exports\ProduitExport;
use App\Imports\TarifImport;
use App\Models\Compagnie;
use App\Models\Condition;
use App\Models\Conditiongroupe;
use App\Models\Conditionvaleur;
use App\Models\Liaison;
use App\Models\Personnel;
use App\Models\Produit;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ConditionController extends Controller
{
    public function newcondition($id)
    {
        return view('conditions.newcondition', ['idproduit' => $id]);
    }

    public function addcondition(Request $request, $id)
    {
        $rules = [
            'libelle' => 'required',
            'niveau' => 'required',
        ];


        $messages = [
            'libelle.required' => 'Veuillez renseigner le nom de la condition.',
            'niveau.required' => 'Veuillez renseigner le niveau de la hierachie.',
        ];

        $validatedData = $request->validate($rules, $messages);

        Condition::create([
            'libelle' => $request->libelle,
            'niveau' => $request->niveau,
            'produit_id' => $id,
        ]);

        return redirect()->route('condition.liste', $id)->with('success', 'Condition enregistrée avec succès !!!');
    }

    public function editcondition($idproduit, $idcondition)
    {
        $condition = Condition::find($idcondition);
        return view('conditions.editcondition', ['idproduit' => $idproduit, 'condition' => $condition]);
    }

    public function updatecondition(Request $request, $idproduit, $idcondition)
    {
        $rules = [
            'libelle' => 'required',
            'niveau' => 'required',
        ];


        $messages = [
            'libelle.required' => 'Veuillez renseigner le nom de la condition.',
            'niveau.required' => 'Veuillez renseigner le niveau de la hierachie.',
        ];

        $validatedData = $request->validate($rules, $messages);


        $condition = Condition::find($idcondition);
        $query = $condition->forceFill([
            'libelle' => $request->libelle,
            'niveau' => $request->niveau,
        ])->save();

        if (!$query) {
            return redirect()->route('condition.liste', $idproduit)->with('error', 'Erreur de modification !!!');
        }
        return redirect()->route('condition.liste', $idproduit)->with('success', 'Condition modifiée avec succès !!!');
    }

    public function listecondition($id)
    {
        $produit = Produit::find($id);
        $conditions = $produit->conditions;
        return view('conditions.listecondition', ['idproduit' => $id, 'conditions' => $conditions]);
    }

    public function deletecondition($idproduit, $idcondition)
    {
        $condition = Condition::find($idcondition);
        $condition->delete();

        return redirect()->route('condition.liste', $idproduit)->with('success', 'Condition supprimée avec succès !!!');
    }

    public function newhierachie($id)
    {
        $condition = Condition::find($id);
        $superieures = Condition::where('niveau', '<', $condition->niveau)->where('produit_id', $condition->produit_id)->get();


        return view('conditions.hierachie.newhierachie', ['superieures' => $superieures, 'id' => $id]);
    }

    public function addhierachie(Request $request, $id)
    {
        $rules = [
            'superieure' => 'required',
        ];


        $messages = [
            'superieure.required' => 'Veuillez sélectionner la condition supérieure.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $condition = Condition::find($id);
        $query = $condition->forceFill([
            'parent_id' => $request->superieure,
        ])->save();

        if (!$query) {
            return redirect()->route('condition.liste', $condition->produit_id)->with('error', 'Echec de l\'attribution de la condition supérieure !!!');
        }
        return redirect()->route('condition.liste', $condition->produit_id)->with('success', 'Attribution de la condition supérieure effectuée avec succès !!!');
    }

    public function listehierachie($id)
    {
        $condition = Condition::find($id);

        $superieures = $condition->getSuperieureHierachie();
        $inferieures = $condition->getInferieureHierachie();


        //dd($inferieures);

        return view('conditions.hierachie.listehierachie', ['superieures' => $superieures, 'inferieures' => $inferieures]);
    }


    public function newvaleur($id)
    {
        return view('conditions.valeurs.newvaleur', ['idcondition' => $id]);
    }

    public function addvaleur(Request $request, $id)
    {
        $rules = [
            'valeur' => 'required',
        ];


        $messages = [
            'valeur.required' => 'Veuillez renseigner la valeur de la condition.',
        ];

        $validatedData = $request->validate($rules, $messages);

        Conditionvaleur::create([
            'libelle' => $request->valeur,
            'condition_id' => $id,
        ]);

        return redirect()->route('valeur.liste', $id)->with('success', 'Valeur enregistrée avec succès !!!');
    }
    public function editvaleur($idcondition, $idvaleur)
    {
        $valeur = Conditionvaleur::find($idvaleur);
        return view('conditions.valeurs.editvaleur', ['idcondition' => $idcondition, 'valeur' => $valeur]);
    }
    public function updatevaleur(Request $request, $idcondition, $idvaleur)
    {
        $rules = [
            'valeur' => 'required',
        ];


        $messages = [
            'valeur.required' => 'Veuillez renseigner la valeur de la condition.',
        ];

        $validatedData = $request->validate($rules, $messages);


        $valeur = Conditionvaleur::find($idvaleur);
        $query = $valeur->forceFill([
            'libelle' => $request->valeur,
        ])->save();

        if (!$query) {
            return redirect()->route('valeur.liste', $idcondition)->with('error', 'Erreur de modification !!!');
        }
        return redirect()->route('valeur.liste', $idcondition)->with('success', 'Valeur modifiée avec succès !!!');
    }

    public function listevaleur($id)
    {
        $condition = Condition::find($id);
        $valeurs = $condition->valeurs;
        return view('conditions.valeurs.listevaleur', ['idcondition' => $id, 'valeurs' => $valeurs]);
    }

    public function deletevaleur($idcondition, $idvaleur)
    {
        $valeur = Conditionvaleur::find($idvaleur);
        $valeur->delete();

        return redirect()->route('valeur.liste', $idcondition)->with('success', 'Valeur supprimée avec succès !!!');
    }

    public function listegroupe($id)
    {
        $produit = Produit::find($id);
        $groupes = $produit->conditiongroupes;

        return view('conditions.groupe.listegroupe', ['groupes' => $groupes, 'idproduit' => $id]);
    }

    public function newgroupe($id)
    {
        $produit = Produit::find($id);
        $conditions = $produit->conditions;
        return view('conditions.groupe.newgroupe', ['conditions' => $conditions, 'idproduit' => $id]);
    }

    public function addgroupe(Request $request, $id)
    {
        $rules = [
            'condition' => 'required',
        ];
        $messages = [
            'condition.required' => 'Veuillez sélectionner les conditions',
        ];

        $validated = $request->validate($rules, $messages);

        $conditions = $validated['condition'];

        include public_path('dist/include/helpers.php');
        $ref = generateRandomCode(5);
        $groupe = "Group_" . $ref;

        while (Conditiongroupe::where('libelle', $groupe)->exists()) {
            $ref = generateRandomCode(5);
            $groupe = "Group_" . $ref;
        }
        $groupe = Conditiongroupe::create([
            "libelle" => $groupe,
            'produit_id' => $id,
        ]);
        $groupe_id = $groupe->id;

        foreach ($conditions as $conditionId => $valeurId) {

            Liaison::create([
                'conditiongroupe_id' => $groupe_id,
                'conditionvaleur_id' => $valeurId,
            ]);
        }

        return redirect()->route('groupe.liste', $id)->with('success', 'Nouveau groupe enregistré avec succès');
    }

    public function deletegroupe($idproduit, $idgroupe)
    {
        $groupe = Conditiongroupe::find($idgroupe);
        $groupe->delete();

        return redirect()->route('groupe.liste', $idproduit)->with('success', 'Groupe supprimé avec succès !!!');
    }

    public function exportExcel($produitId)
    {
        return Excel::download(new ProduitExport($produitId), 'produit_' . $produitId . '.xlsx');
    }

    public function newtarif($id)
    {
        return view('conditions.tarif.newtarif', ['id' => $id]);
    }

    public function addtarif(Request $request, $id)
    {
        if (Auth::user()->role != "Personnel") {
            return redirect()->back();
        }
        $user = Personnel::where('user_id', Auth::user()->id)->first();
        $compagnie_id = $user->compagnie_id;

        $request->validate([
            'fichier' => 'required|mimes:xlsx',
        ], [
            'fichier.required' => 'Veuillez choisir le fichier',
            'fichier.mimes' => 'Choississez un fichier Excel .xlsv SVP!!!',
        ]);

        Excel::import(new TarifImport($compagnie_id), $request->file('fichier'));

        return redirect()->route('tarif.liste', $id)->with('success', 'Les tarifs ont été importés avec succès.');
    }

    // public function edittarif($idrealisation, $idobjectif)
    // {
    //     $realisation = Realisation::find($idrealisation);
    //     return view('reseau.realisation.editrealisation', ['idrealisation' => $idrealisation, "idobjectif" => $idobjectif, "realisation" => $realisation]);
    // }

    // public function updatetarif(Request $request, $idrealisation, $idobjectif)
    // {
    //     $rules = [
    //         'nombre' => 'required',
    //         'ca' => 'required',
    //         'date' => 'required',
    //     ];

    //     $messages = [
    //         'nombre.required' => 'Veuillez rensigner le nombre de client.',
    //         'ca.required' => 'Veuillez rensigner le chiffre d\'affaire.',
    //         'date.required' => 'Veuillez rensigner la date de réalisation.',
    //     ];
    //     $validatedData = $request->validate($rules, $messages);

    //     $realisation = Realisation::find($idrealisation);

    //     $query = $realisation->forcefill([
    //         'nombre' => $request->nombre,
    //         'ca' => $request->ca,
    //         'date' => $request->date,
    //     ])->save();

    //     if (!$query) {
    //         return redirect()->route('realisation.liste', $idobjectif)->with('error', 'Echec de la modification de la realisation!!!');
    //     }

    //     return redirect()->route('realisation.liste', $idobjectif)->with('success', 'Modification de la realisation effectuée avec succès !!!');
    // }

    // public function deletetarif($idrealisation, $idobjectif)
    // {
    //     $realisation = Realisation::find($idrealisation);
    //     $realisation->delete();

    //     return redirect()->route('realisation.liste', $idobjectif)->with('success', 'Suppression de la réalisation effectuée avec succès !!!');
    // }


    public function listetarif($id)
    {
        if (Auth::user()->role != "Personnel") {
            return redirect()->back();
        }

        // Récupérer l'utilisateur personnel actuel
        $user = Personnel::where('user_id', Auth::user()->id)->first();
        $compagnie_id = $user->compagnie_id;
        $compagnie = Compagnie::find($compagnie_id);

        $tarifs = $compagnie->tarifs;
        //dd($tarifs);

        $groupes = [];

        foreach ($tarifs as $tarif) {
            //$groupe = $tarif->groupe;
            $groupe = Conditiongroupe::find($tarif->conditiongroupe_id);
            //dd($groupe);
            if ($groupe->produit_id == $id) {
                $groupe['id'] = $groupe->id;
                $groupe['produit'] = $groupe->produit->nomProduit;
                $groupe['tarif'] = $tarif->tarif;
                $groupe['reduction'] = $tarif->reduction;
                $groupe['liaisons'] = $groupe->liaisons;
            }

            $groupes[] = $groupe;
        }

        //dd($groupes);

        // // Charger le produit et les tarifs associés à la compagnie
        // $produit = Produit::find($id);

        // $ensembles = $produit->conditiongroupes;

        // $groupes = [];

        // foreach ($ensembles as $ensemble) {
        //     if ($ensemble->tarif->compagnie_id == $compagnie_id) {
        //         $groupe[] = $ensemble;
        //     }
        // }

        // $conditiongroupeIds = $compagnie->tarifs()->pluck('conditiongroupe_id');

        // // Récupérer les conditiongroupes liés au produit et aux conditiongroupeIds de la compagnie
        // $groupes = Produit::find($id)
        //     ->conditiongroupes()
        //     ->whereIn('id', $conditiongroupeIds)
        //     ->get();

        // $groupes = Produit::where('id', $id)->with(['conditiongroupes' => function ($query) use ($compagnie_id) {
        //     $query->whereHas('tarif', function ($query) use ($compagnie_id) {
        //         $query->where('compagnie_id', $compagnie_id);
        //     })
        //         ->with('tarif');
        // }])
        //     ->first();

        return view('conditions.tarif.listetarif', ['groupes' => $groupes, 'id' => $id, 'compagnie' => $compagnie_id]);
    }



    // public function getTarif(Request $request) {
    //     $conditionvaleurs = array_filter($request->except('_token'));

    //     if (empty($conditionvaleurs)) {
    //         return response()->json(['tarif' => 'Aucune condition sélectionnée.']);
    //     }

    //     // Trouver le groupe de conditions correspondant
    //     $groupes = Conditiongroupe::whereHas('liaisons', function ($query) use ($conditionvaleurs) {
    //         $query->whereIn('conditionvaleur_id', $conditionvaleurs);
    //     })
    //     ->with('tarif')
    //     ->havingRaw('COUNT(DISTINCT liaisons.conditionvaleur_id) = ?', [count($conditionvaleurs)])
    //     ->first();

    //     if ($groupes && $groupes->tarif) {
    //         return response()->json(['tarif' => $groupes->tarif->prix]);
    //     } else {
    //         return response()->json(['tarif' => 'Pas de tarif trouvé pour cette combinaison.']);
    //     }
    // }


}
