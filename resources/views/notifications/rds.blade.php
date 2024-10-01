@extends('notifications.layouts.template')
@section('section')
    <div>

        <p>

           Vôtre rendez avec le prospect  :

           {{ $nom . ' ' . $prenom }} <br>

           est pour aujourd'hui : {{ $date }}


        </p> <br>
    </div>
@endsection
