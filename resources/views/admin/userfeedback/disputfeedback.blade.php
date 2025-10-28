@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\UserFeedbackController')

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
                        <a href="">Feedback Management</a>
                    </li>
                    <li class="active">
                        Dispute Feedback </li>
                </ul>
            </div>
        </div>
        <div class="page-content" style="margin-top: -10px;">
            <div class="question">
                <div class="qus-tabs">
                    <ul>
                        <li class="{{ $controller->role == 'freelancer' ? 'active' : '' }}">
                            <a href="{{ route('disputefeedback.list', ['q' => 'freelancer']) }}">Locum</a>
                        </li>
                        <li class="{{ $controller->role == 'Employer' ? 'active' : '' }}">
                            <a href="{{ route('disputefeedback.list', ['q' => 'Employer']) }}">Employer</a>
                        </li>
                    </ul>
                </div>
                <div id="fre-tab">
                    <div class="cat-tabs">
                        <ul>
                            @foreach ($controller->professionslist as $profession)
                                <li style="margin-top: 25px;" {{ $controller->profession == $profession->id ? ' class=active' : '' }}>
                                    <a
                                        href="{{ route('disputefeedback.list', ['q' => $controller->role, 'c' => $profession->id]) }}">{{ $profession->name }}</a>

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
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobFeedbackDispute as $key => $data)
                                <tr>
                                    <td style="text-transform: capitalize;">{{ $key + 1 }}</td>
                                    <td style="text-transform: capitalize;">#{{ $data->jobfeedback->job_id }}</td>
                                    <td style="text-transform: capitalize;">
                                        {{ $controller?->role == 'freelancer' ? $data->jobfeedback?->freelancer?->firstname : $data->jobfeedback?->employer?->firstname }}
                                    </td>
                                    <td style="text-transform: capitalize;">`
                                        {{ $controller?->role == 'freelancer' ? $data->jobfeedback?->employer?->firstname : $data->jobfeedback?->freelancer?->firstname }}
                                    </td>
                                    <td>
                                        <div id="stars-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $data->rating)
                                                    <span class="glyphicon glyphicon-star"></span>
                                                @else
                                                    <span class="glyphicon glyphicon-star-empty"></span>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            style="{{ $data->status == 0 ? 'color:red' : 'color:green' }}">{{ $data->status == 0 ? 'Deactive' : 'Active' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.userfeedback.edit', $data->id) }}" class="edit-line">
                                            <img src="https://admin.locumkit.com/public/backend/images/icones/edit.png"
                                                alt="Edit">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.userfeedback.destroy', $data->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="border: none;
                                            background: none;
                                            padding: 0;
                                            margin: 0;
                                            cursor: pointer;"
                                                onclick="return confirm('are you sure to delete this record!')">
                                                <img src="/backend/images/icones/delete.png" alt="Delete">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                <style type="text/css">
                    div#stars-rating {
                        font-size: 16px;
                        color: #df7900;
                    }
                </style>
            </div>
        </div>
    </div>
@endsection
