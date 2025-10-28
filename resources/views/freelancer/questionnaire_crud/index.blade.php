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
        .footer {
            padding: 0px !important;
            width: 0px !important;
        }
        
    </style>
@endpush

@extends('layouts.user_profile_app')

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
    <style>
        .active{
            background:#D3F4FF !important;
            color:black !important;
        }
    </style>

    <div id="primary-content" class="main-content register">
        <div class="container">
            <div class="" style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <a href="{{ route('freelancer.locumlogbook.follow-up-procedures.index') }}" class="grad_btn btn btn-info {{ request()->routeIs('freelancer.locumlogbook.follow-up-procedures.*') ? 'active' : '' }}">
                    FOLLOW UP PROCEDURES
                </a>
                
                <a href="{{ route('freelancer.locumlogbook.referral-pathways.index') }}" class="grad_btn btn btn-info {{ request()->routeIs('freelancer.locumlogbook.referral-pathways.*') ? 'active' : '' }}">
                    REFERRAL PATHWAYS
                </a>
                
                <a href="{{ route('freelancer.locumlogbook.practice-info.index') }}" class="grad_btn btn btn-info {{ request()->routeIs('freelancer.locumlogbook.practice-info.*') ? 'active' : '' }}">
                    PRACTICE INFO
                </a>
            </div>

            <div class="row pad0 finacedetable" style="padding-right: 0px !important;">
                <div class="col-md-12 col-sm-12 income">
                    <div class="col-md-12 pad0 head_box">
                        <span>
                            <h1 class="mar0 text-capitalize" id="register_head_blue" style="display: inline-block;padding-top: 15px;">{{ $page_title }}
                            </h1>
                            <h1 class="mar0 text-none" id="register_head_blue" style="display: inline-block;padding-top: 1px;">{{ $heading }}
                            </h1>
                        </span>
                        <div class="pad0" style="display: inline;">
                            <a href="{{ route($route . '.create') }}" class="read-common-btn grad_btn pull-right" style="height:38px;width:142px;">
                                ADD NEW
                            </a> 
                        </div>
                    </div>
                    <div class="col-md-12 pad0">
                        <table class="table-striped table-bordered table" style="width:100%" id="questionaire_list1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @foreach ($fields as $field)
                                        @if ((isset($field['index_hidden']) && $field['index_hidden']) == false)
                                            <th>{{ $field['title'] }}</th>
                                        @endif
                                    @endforeach
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @foreach ($fields as $field)
                                      
                                            @if ((isset($field['index_hidden']) && $field['index_hidden']) == false)
                                                @if (isset($field['type']) && $field['type'] == 'checkbox')
                                                    <td>{{ $record->{$field['name']} == 1 ? 'Yes' : 'No' }}</td>
                                                @else
                                                   @if($field['type']=='date')
                                                      <td> {{\Carbon\Carbon::parse($record->{$field['name']})->format('d/m/y')}}</td>
                                                 @elseif($field['type']=='datetime-local')
                                                    <td>{{ \Carbon\Carbon::parse($record->{$field['name']})->format('d/m/Y H:i') }}</td>

                                                      @else
                                                    <td>{{ $record->{$field['name']} }}</td>
                                                    @endif
                                                @endif
                                               
                                            @endif
                                        @endforeach


                                        <td>
                                            <a href="{{ route($route . '.edit', $record->id) }}" class="btn btn-xs btn-info">
                                                <i class="fa fa-fw fa-edit"></i>
                                            </a>
                                            <form id="del-form-{{ $record->id }}-record" action="{{ route($route . '.destroy', $record->id) }}" method="post" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteByFormId('del-form-{{ $record->id }}-record')">
                                                <i class="fa fa-fw fa-close"></i>
                                            </button>
                                        </td>
                                @endforeach

                                </tr>
                            </tbody>
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /*  $(document).ready(function() {
                        $('#questionaire_list1').DataTable();
                    }); */

        function deleteByFormId(id) {
            document.getElementById(id).submit();
        }
    </script>
@endpush
