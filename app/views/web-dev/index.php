@extends('layouts.app')

@section('content')
    @routeIs('web-dev.index')
        // ...existing code...
    @endrouteIs
    
    @routeIs('web-dev.services')
        // ...existing code...
    @endrouteIs
@endsection