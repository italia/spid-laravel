@inject('SPIDAuth', 'SPIDAuth')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Private area</div>
                <div class="card-body">
                    Hi {{ $SPIDAuth->getSPIDUser()->name }} {{ $SPIDAuth->getSPIDUser()->familyName }}, this area is private!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
