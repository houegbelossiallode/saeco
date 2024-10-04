<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rd extends Model
{
    use HasFactory;
    protected $fillable = ['prime','produit_id','client_id','commercial_id','notes','date_du_rdv'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function commercial()
    {
        return $this->belongsTo(User::class,'commercial_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

}