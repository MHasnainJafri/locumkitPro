@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FeedbackQuestionController')

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
                    <li>
                        <a href="/admin/config">Config</a>
                    </li>
                    <li class="active">
                        User </li>
                </ul>
            </div>
            <div class="page-content">
                <div id="tabs">
                    <div class="qus-tabs">
                        <ul>
                            <li class="{{ $controller->role == 'Locum' ? 'active' : '' }}">
                                <a href="{{ route('admin.feedbackquestion.index', ['q' => 'Locum']) }}">Locum</a>
                            </li>
                            <li class="{{ $controller->role == 'Employer' ? 'active' : '' }}">
                                <a href="{{ route('admin.feedbackquestion.index', ['q' => 'Employer']) }}">Employer</a>
                            </li>
                        </ul>
                    </div>
                    <div id="fre-tab">
                        <div class="cat-tabs">
                            <ul>
                                @foreach ($categories as $profession)
                                    <li style="margin-top: 25px;" {{ $controller->profession == $profession->id ? ' class=active' : '' }}>
                                        <a href="{{ route('admin.feedbackquestion.index', ['q' => $controller->role, 'c' => $profession->id]) }}">
                                            {{ $profession->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <table class="table clickable table-striped table-hover">
                            <colgroup>
                                <col width="60%">
                                <col width="10%">
                                <col width="10%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allfeedback as $feedback)
                                    <tr>
                                        <td>
                                            {{ $controller->role == 'Locum' ? $feedback->question_freelancer  : $feedback->question_employer }}

                                            </td>
                                        <td>
                                            <span style="{{$feedback->question_status == 0 ? 'color:red' : 'color:green'}}">{{$feedback->question_status == 0 ? 'Deactive' : 'Active'}}</span>
                                        </td>
                                        <td>{{$feedback->question_sort_order}}</td>
                                        @cando('question/edit')
                                        <td class="text-center">
                                            <a href="{{ route('admin.feedbackquestion.edit', $feedback->id) }}" class="edit-line">
                                                <img src="/backend/images/icones/edit.png" alt="Edit" />
                                            </a>
                                        </td>
                                        @endcando
                                        @cando('question/delete')
                                        <td class="text-center">
                                            <form action="{{ route('admin.feedbackquestion.destroy', $feedback->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="border: none;
                                                background: none;
                                                padding: 0;
                                                margin: 0;
                                                cursor: pointer;" onclick="return confirm('are you sure to delete this record!')">
                                                    <img src="/backend/images/icones/delete.png" alt="Delete">
                                                </button>
                                            </form>
                                        </td>
                                        @endcando
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $allfeedback->links() }}
                        <div class="pagination">
                            <link rel="stylesheet"
                                href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                            <p class="clearfix">
                            </p>
                            <ul class="paginator-div">
                            </ul>
                            <p></p>
                        </div>
                    </div>
                    <script type="text/javascript">
                        Gc.initTableList();
                    </script>
                </div>
            </div>
        </div>

    </div>
@endsection
