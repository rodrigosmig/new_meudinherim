@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ $user->name }}</li>
        <li class="breadcrumb-item active">{{ __('global.profile') }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <form id="formEditPhoto" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <img src="{{ asset('images/user.png') }}" id="imgProfile" style="width: 100px; height: 100px" class="profile-user-img img-fluid img-circle" />
                           {{--  @if (! auth()->user()->hasPhoto())
                                <img src="{{ asset('img/user.png') }}" id="imgProfile" style="width: 150px; height: 150px" class="img-thumbnail" />
                            @else
                                <img src="/storage/{{auth()->user()->photo}}" id="imgProfile" style="width: 150px; height: 150px" class="img-thumbnail" />
                            @endif --}}
                            
                            {{-- <div class="middle">
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                <input type="button" class="btn btn-secondary" id="btnChangePicture" value="Change" />
                                <input type="file" style="display: none;" id="profilePicture" name="file" />
                            </div> --}}
                        </form>
                        {{-- <img class="profile-user-img img-fluid img-circle" src="../../dist/img/user4-128x128.jpg" alt="{{ $user->name }}"> --}}
                    </div>
        
                  <h3 class="profile-username text-center">{{ $user->name }}</h3>
        
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                        <b>{{ __('global.accounts') }}</b> <a class="float-right">{{ $user->accounts()->count() }}</a>
                        </li>
                        <li class="list-group-item">
                        <b>{{ __('global.cards') }}</b> <a class="float-right">{{ $user->cards()->count() }}</a>
                        </li>
                    </ul>
        
                    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ __('global.profile') }}</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">{{ __('global.password') }}</a>
                        </li>
                      </ul>
                      <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form class="form-horizontal" action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-2 col-form-label">{{ __('global.name') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('global.name') }}" value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">{{ __('global.email') }}</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('global.email') }}" value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                  <button type="submit" class="btn btn-primary"><b>{{ __('global.change') }}</b></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <form class="form-horizontal" action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-2 col-form-label">{{ __('global.current_password') }}</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="{{ __('global.current_password') }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-2 col-form-label">{{ __('global.new_password') }}</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="new_password" name="password" placeholder="{{ __('global.new_password') }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">{{ __('global.confirm_password') }}</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="confirm_password" name="password_confirmation" placeholder="{{ __('global.confirm_password') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                  <button type="submit" class="btn btn-primary"><b>{{ __('global.change') }}</b></button>
                                </div>
                            </form>
                        </div>
                      </div>
                </div>
            </div>
        </div>
    </div>   
@stop