@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="#">Blocked Locums</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Blocked Locums</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <div class="welcome-heading">
                        <h1><span>Blocked Locums</span></h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="block-fre-list cash_table-fiexd-scroll">
                        <table class="table-hover table-striped table table-fixed">
                            <thead>
                                <tr>
                                    <th class="col-xs-3 col-sm-3">User Id</th>
                                    <th class="col-xs-3 col-sm-3">Name</th>
                                    <th class="col-xs-3 col-sm-3">Block Date</th>
                                    <th class="col-xs-3 col-sm-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($block_locums as $block_locum)
                                    <tr>
                                        <td class="col-xs-3 col-sm-3"> {{ $block_locum->freelancer->id }} </td>
                                        <td class="col-xs-3 col-sm-3"> {{ $block_locum->freelancer->firstname . ' ' . $block_locum->freelancer->lastname }} </td>
                                        <td class="col-xs-3 col-sm-3"> {{ $block_locum->created_at->format('d-m-Y') }} </td>
                                        <td class="col-xs-3 col-sm-3"><a href='javascript:void(0)' onClick="un_block_user('{{ $block_locum->id }}')"> Unblock </a></td>
                                    </tr>
                                @empty
                                    <tr class="lblock-row">
                                        <td colspan="4">
                                            <h4 style="display:block; text-align:center; color:red">No record found.</h4>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function un_block_user(id) {
            $('div#alert-confirm-modal #alert-message').html('Please confirm if you want to un-block this locum from recieveing future job invitations');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#loader-div").show();
                $.ajax({
                    'url': '/ajax/manage-block-freelancer',
                    'type': 'DELETE',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'data': {
                        'un_block_id': id
                    },
                    'success': function(result) {
                        location.reload();
                    }
                });
                messageBoxClose();
            });

        }
    </script>
@endpush
