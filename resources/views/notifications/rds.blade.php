@extends('notifications.layouts.template')
@section('section')
    <div>

        <p>

           {{ $message }} avec le prospect  :

           {{ $nom . ' ' . $prenom }} 


        </p> <br>
    </div>
@endsection
