@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FeedbackController')


    <div class="main-container container">
        @include('admin.layout.sidebar')
        <div class="col-lg-12 main-content">
                <div id="breadcrumbs" class="breadcrumbs">
                    <div id="menu-toggler-container" class="hidden-lg">
                        <span id="menu-toggler">
                            <i class="glyphicon glyphicon-new-window"></i>
                            <span class="menu-toggler-text">Menu</span>
                        </span>
                    </div>
                    <ul class="breadcrumb">
                        <li>
                            <i class="glyphicon glyphicon-home home-icon"></i>
                            <a href="/admin/dashboard">Dashboard</a>
                        </li>
                        <li class="active">
                            Feedback 
                        </li>
                    </ul>
                </div>


        <div class="page-content">
            <div class="question">
                <div class="qus-tabs">
                    <ul>
                        <li class="{{ $controller->role == 'Locum' ? 'active' : '' }}">
                            <a href="{{ route('admin.feedback.index', ['q' => 'Locum']) }}">Locum</a>
                        </li>
                        <li class="{{ $controller->role == 'Employer' ? 'active' : '' }}">
                            <a href="{{ route('admin.feedback.index', ['q' => 'Employer']) }}">Employer</a>
                        </li>
                    </ul>
                </div>
                <div id="fre-tab">
                    <div class="cat-tabs">
                        <ul>
                            
                            @foreach ($controller->professionslist as $profession)
                                <li style="margin-top: 25px;" {{ $controller->profession == $profession->id ? ' class=active' : '' }}>
                                    <a href="{{ route('admin.feedback.index', ['q' => $controller->role, 'c' => $profession->id]) }}" class="my-5">{{ $profession->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <table class="table clickable table-striped table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="5%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                            <col width="10%">
                            <col width="5%">
                            <col width="5%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Job ID</th>
                                <th>Feedback From</th>
                                <th>Feedback To</th>
                                <th>Average Rate</th>
                                <th>Feedback Status</th>
                                @cando('feedback/edit')
                                <th class="text-center">Edit</th>
                                @endcando
                                @cando('feedback/delete')
                                <th class="text-center">Delete</th>
                                @endcando
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($allfeedback as $allfeedback)
                                <tr>
                                    <td style="text-transform: capitalize;">{{ $allfeedback->id }}</td>
                                    <td style="text-transform: capitalize;">{{ $allfeedback->job_id }}</td>
                                    <td style="text-transform: capitalize;">{{ $allfeedback->employer->firstname }}</td>
                                    <td style="text-transform: capitalize;">{{ $allfeedback->freelancer->firstname }}</td>
                                    <td>
                                        <div id="stars-rating">
                                            <span class="glyphicon glyphicon-star"></span>
                                            <span class="glyphicon glyphicon-star"></span>
                                            <span class="glyphicon glyphicon-star"></span>
                                            <span class="glyphicon glyphicon-star"></span>
                                            <span class="glyphicon glyphicon-star-empty"></span>
                                        </div>
                                    </td>
                                    @php
                                        $statusOptions = [
                                            1 => 'Approved',
                                            2 => 'Dispute Pending',
                                            3 => 'Dispute Approved',
                                        ];
                                    @endphp
                                    <td>

                                        <span style="color:green">
                                            {{ isset($statusOptions[$allfeedback->status]) ? $statusOptions[$allfeedback->status] : '' }}
                                        </span>
                                    @cando('feedback/edit')
                                    <td class="text-center">
                                        <a href="{{ route('feedback.Edit', $allfeedback->id) }}" class="edit-line">
                                            <img src="/public/backend/images/icones/edit.png" alt="Edit">
                                        </a>
                                    </td>
                                    @endcando
                                    @cando('feedback/delete')
                                    <td class="text-center">
                                        <form action="{{ route('feedback.del', $allfeedback->id) }}" id="delete_form_{{$allfeedback->id}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <!--<button type="submit">-->
                                            <!--    <img src="/public/backend/images/icones/delete.png" alt="Delete">-->
                                            <!--</button>-->
                                        <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$allfeedback->id}}">
                                            <img src="/backend/images/icones/delete.png"
                                                alt="Delete">
                                        </button>

                                        </form>
                                    </td>
                                    @endcando
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModalCenter_{{$allfeedback->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Delete Feedback</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <div class="modal-body">
                                        Are you sure to delete this Feedback?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" onClick="deleteRole({{$allfeedback->id}})" class="btn btn-danger">Confirm</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        <link rel="stylesheet"
                            href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                        <p class="clearfix">
                        <ul class="paginator-div">
                        </ul>
                        </p>
                    </div>
                </div>
                <script type="text/javascript">
                    Gc.initTableList();
                </script>
                <style type="text/css">
                    div#stars-rating {
                        font-size: 16px;
                        color: #df7900;
                    } 
                </style>

            </div>
        </div>
        </div>
    </div>
    <script type="text/javascript">
        Gc.keepAlive('/admin/keep-alive');
    </script>
    <script>
        function deleteRole(id) {
            $("#delete_form_"+id).submit()
        }
    </script>
    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
@endsection
