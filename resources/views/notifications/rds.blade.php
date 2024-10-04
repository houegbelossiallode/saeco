@extends('notifications.layouts.template')
@section('section')
    <div>

        <p>
            {{  $rendezVousMessage}}
            avec le prospect  :

           {{ $nom . ' ' . $prenom }} 
        </p> <br>
        voir plus {{ $action }}
    </div>
@endsection
