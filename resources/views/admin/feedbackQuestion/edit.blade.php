@extends('admin.layout.app')
@inject('controller', 'App\Http\Controllers\admin\FeedbackQuestionController')

@section('content')
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
                        <a href="/admin/config">Feedback Question</a>
                    </li>
                    <li class="active">
                        Edit </li>
                </ul>
            </div>
            <div class="page-content">
                <form class="relative form-horizontal" action="{{route('admin.feedbackquestion.update', $feedback['id'])}}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="required control-label col-lg-2" id="question_cat_id"
                            for="question_cat_id">Category</label>
                        <div class="col-lg-10">
                            <select name="category" class="form-control" id="category">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category->id == $feedback['question_cat_id'] ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="question_freelancer">Question For
                            Freelancer</label>
                        <div class="col-lg-10">
                            <input type="text" name="question_freelancer" class="form-control" id="question_freelancer"
                                value="{{ $feedback['question_freelancer'] }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="question_employer">Question For Employer</label>
                        <div class="col-lg-10">
                            <input type="text" name="question_employer" class="form-control" id="question_employer"
                                value="{{ $feedback['question_employer'] }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2" for="question_status">Question Status</label>
                        <div class="col-lg-10">
                            <select name="question_status" class="form-control">
                                @if ($feedback['question_status'] == 0)
                                    <option value="0" selected="selected">Deactive</option>
                                    <option value="1">Active</option>
                                @else
                                    <option value="1" selected="selected">Active</option>
                                    <option value="0">Deactive</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="question_sort_order">Sort Order</label>
                        <div class="col-lg-10">
                            <input type="number" name="sort_order" class="form-control" id="sort_order"
                                value="{{ $feedback['question_sort_order'] }}">
                        </div>
                    </div>
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save &amp; add new" name="submit">
                </form>
                <script type="text/javascript">
                    $(function() {
                        Gc.saveCommand();
                        Gc.checkDataChanged();
                        Gc.initRoles();
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
