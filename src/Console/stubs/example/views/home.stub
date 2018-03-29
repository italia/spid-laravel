@inject('SPIDAuth', 'SPIDAuth')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Home</div>
                <div class="card-body">
                  @if ($SPIDAuth->isAuthenticated())
                    Hi {{ $SPIDAuth->getSPIDUser()->name }} {{ $SPIDAuth->getSPIDUser()->familyName }}, you are logged in!
                  @else
                  @include('spid-auth::spid-button', ['size' => 'm'])
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
