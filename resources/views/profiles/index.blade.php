@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ $user->name }}</li>
        <li class="breadcrumb-item active">{{ __('global.profile') }}</li>
    </ol>
@endsection

@push('js')
    <script src="{{ asset('js/profile/profile.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
@endpush

@section('js')
    <script>
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });

        var monthly = "{{ $user->enable_notification }}";

        if (monthly === '1') {
            $("#enable_notification").bootstrapSwitch('state', true)
        } else {
            $("#enable_notification").bootstrapSwitch('state', false)
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="image-container">
                            <form id="formEditPhoto" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <img src="{{ $user->adminlte_image() }}" id="imgProfile" style="width: 100px; height: 100px" class="profile-user-img img-fluid img-circle" />
                                <div class="middle">
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                    <input type="button" class="btn btn-secondary" id="btnChangePicture" value="Change" />
                                    <input type="file" style="display: none;" id="profilePicture" name="file" />
                                </div>
                            </form>
                        </div>
                    </div>
        
                    <h3 class="profile-username text-center">{{ $user->name }}</h3>
                    <h5 class="text-center">{{ $user->email }}</h4>
        
                    <div class="ml-auto">
                        <input type="button" class="btn btn-primary btn-block d-none" id="btnDiscard" value="{{ __('global.discard_changes') }}" />
                    </div>
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

                                    <div class="form-group row">
                                        <label for="enable_notification" class="col-sm-2 col-form-label">{{ __('global.receive_notifications') }}</label>
                                        <div class="col-sm-10">
                                            <input id="enable_notification" type="checkbox" name="enable_notification" data-bootstrap-switch data-off-color="danger" data-on-color="success">
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