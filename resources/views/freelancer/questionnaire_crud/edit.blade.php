@extends('layouts.user_profile_app')

@push('styles')
    <style>
        .grad_btn {
            width: 250px;
            text-align: center;
            padding-left: 0px !important;
            padding-right: 0px !important;
            margin-top: 20px !important;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            user-select: none;
            padding-block: 20px;
        }

        .input-group-addon {
            padding: 5px !important;
            padding-right: 12px !important;
        }

        .dataTables_filter label {
            float: right;
        }
    </style>
@endpush

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="#">Locum Diary</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-sm-6 col-xs-12 pad0">
                        <div class="set-icon registration-icon">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </div>
                        <div class="set-title">
                            <h3>Locum Diary</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content register">
        <div class="container">
            <div class="col-md-3" style="padding-top: 70px;padding-left: 0px; padding-bottom: 60px;">
                <a href="{{ route('freelancer.locumlogbook.follow-up-procedures.index') }}" class="btn btn-info grad_btn">
                    FOLLOW UP PROCEDURES
                </a>

                <a href="{{ route('freelancer.locumlogbook.referral-pathways.index') }}" class="btn btn-info grad_btn">
                    Referral Pathways
                </a>
                <a href="{{ route('freelancer.locumlogbook.practice-info.index') }}" class="btn btn-info grad_btn">
                    Practice Info
                </a>
            </div>

            <div class="col-md-9" style="padding-right: 15px;padding-bottom: 25px;">
                <div class="row">
                    <div class="white-bg contents">
                        <section class="add_item pb30 text-left">
                            <div class="col-md-12 pad0">
                                <div class="finance-page-head text-center" style="font-size:20pt">{{ $add_heading }}
                                </div>
                            </div>
                            <div class="col-md-12 pad0">
                                <form action="{{ route($route . '.update', $record->id) }}" method="POST" class="add_item_form form-inline">
                                    @csrf
                                    @method('PUT')

                                    @foreach ($fields as $field)
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <label for="{{ $field['name'] }}" style="padding-top: 6px;">{{ $field['title'] }}</label>
                                                    @if (isset($field['validation_rules']) && str_contains($field['validation_rules'], 'required'))
                                                        <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                                                    @endif
                                                </div>
                                                <div class="col-md-7">
                                                    @if (isset($field['type']) && $field['type'] == 'checkbox')
                                                        <input @if ($record->{$field['name']}) checked @endif type="checkbox" name="{{ $field['name'] }}" id="{{ $field['name'] }}" @if (isset($field['validation_rules']) && str_contains($field['validation_rules'], 'required')) required @endif>
                                                    @else
                                                        <input @if (!in_array($field['type'], ['checkbox'])) class="form-control" @endif @if ($record->{$field['name']}) value="{{ $record->{$field['name']} }}" @endif
                                                               type="{{ isset($field['type']) ? $field['type'] : 'text' }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                                               @if (isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif @if (isset($field['validation_rules']) && str_contains($field['validation_rules'], 'required')) required @endif>
                                                    @endif
                                                </div>
                                            </div>
                                            @error($field['name'])
                                                <div class="has-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach


                                    <div class="col-md-12" style="padding-top: 20px;">
                                        <div class="form-group text-center">
                                            <button type="submit" class="read-common-btn grad_btn" style="display: inline">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
