@extends('layouts.dashboard')
@section('content')
<style>
    .container{
        margin-left: 15px;
        margin-right: 15px;
    }
    .row{
        display: flex;
    }
    .justify-content-center{
        justify-content: center;
    }
    .col-md-8{
        width: 75%;
    }
    </style>

<div class="container">
     <div class="row justify-content-center">
         <div class="col-md-8">
             <div class="card">
                 <div class="card-header">Verify Your Email Address</div>
                   <div class="card-body">
                    @if (session('resent'))
                         <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                    <a href="http://customlaravelauth.co/{{$token}}/reset-password">Click Here</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection