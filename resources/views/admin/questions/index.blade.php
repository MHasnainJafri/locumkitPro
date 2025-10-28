@extends('admin.layout.app')
@section('content')
@inject('controller', 'App\Http\Controllers\admin\UserController')
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
                    </ul>
                </div>
                <div class="page-content">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                    <div class="question">
                        <div class="qus-tabs">
                            <ul>
                                <li class="{{ request()->get('q') == 'Locum' ? 'active' : '' }}">
                                    <a href="{{ route('admin.question.index', ['q' => 'Locum', 'c' => $controller->profession]) }}">Locum</a>
                                </li>
                                <li class="{{ request()->get('q') == 'Employer' ? 'active' : '' }}">
                                    <a href="{{ route('admin.question.index', ['q' => 'Employer', 'c' => $controller->profession]) }}">Employer</a>
                                </li>
                            </ul>

                        </div>
                        <div id="fre-tab">
                            <div class="cat-tabs">
                                <ul>
                                    @php
                                        use App\Models\UserAclProfession;
                                        $get_category = UserAclProfession::where('is_active', 1)->get();
                                    @endphp
                                    @foreach ($get_category as $profession)
                                         <li class="active" style="margin-top: 25px;"><a
                                            href="{{route('admin.question.index',['q'=>$controller->role,'c'=>$profession->id])}}">{{$profession->name}}</a>
                                    </li>
                                    @endforeach


                                </ul>
                            </div>
                            <table class="table table-striped table-hover">
                                <colgroup>

                                    <col width="50%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="1%">
                                    <col width="1%">
                                </colgroup>
                                <thead>
                                    @foreach ($UserQuestion as $question)

                                    @endforeach
                                    <tr>

                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Option Value</th>
                                        <th>Sort Order</th>
                                        @cando('question/edit')
                                        <th class="text-center">Edit</th>
                                        @endcando
                                        @cando('question/delete')
                                        <th class="text-center">Delete</th>
                                        @endcando

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($UserQuestion as $q)
                                        <tr>
                                            {{-- @dd($UserQuestion) --}}
                                            <td>{{ $role === 'Employer' ? $q->employer_question : $q->freelancer_question }}</td>
                                            <td>Select Option</td>
                                            <td>
                                                @php
                                                    $options = json_decode($q->values, true);
                                                @endphp

                                                @if ($options)
                                                    @foreach ($options as $index => $option)
                                                        <p>{{ $index + 1 }}. {{ $option }}</p>
                                                    @endforeach
                                                @endif
                                            </td>
                                        <td>{{ $q->sort_order }}</td>
                                        @cando('question/edit')
                                        <td class="text-center">
                                            <a href="{{ route('admin.question.edit',$q) }}" class="edit-line">
                                                <img src="/backend/images/icones/edit.png"
                                                    alt="Edit">
                                            </a>
                                        </td>
                                        @endcando
                                        @cando('question/delete')
                                        @method('destroy')
                                            <!--<form action="{{ route('admin.question.destroy',$q) }}" method="post">-->
                                            <form id="delete_form_{{$q->id}}" action="{{ route('admin.question.destroy',$q) }}" method="POST">
                                                @csrf
                                                @method('delete')                                            
                                                <td class="text-center">
                                                    <!--<button class="delete-line" type="submit">-->
                                                    <!--    <img src="/backend/images/icones/delete.png"-->
                                                    <!--        alt="Delete">-->
                                                    <!--</button>-->
                                                    <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$q->id}}">
                                                        <img src="/backend/images/icones/delete.png"
                                                            alt="Delete">
                                                    </button>
                                                        
                                                </td>
                                            </form>
                                        @endcando
                                    </tr>
                                    
                                    <div class="modal fade" id="exampleModalCenter_{{$q->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Delete Question</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                            Are you sure to delete this Question?
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="button" onClick="deleteRole({{$q->id}})" class="btn btn-danger">Confirm</button>
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
                        <style>
                            table tr td:nth-child(3) {
                                height: 135px;
                                overflow: auto;
                                display: block;
                            }
                        </style>
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
    </body>

    </html>
@endsection
