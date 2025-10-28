
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
                            Create </li>
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
                     <form class="relative form-horizontal" action="{{route('admin.feedbackquestion.store')}}" method="post"
                         enctype="application/x-www-form-urlencoded">
                         @csrf
                         <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="category">Category</label>
                            <div class="col-lg-10">
                                <select name="category" class="form-control" id="category">
                                    @foreach ($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="fre_question">Question For Freelancer</label>
                            <div class="col-lg-10">
                                <input type="text" name="fre_question" class="form-control" id="fre_question" value required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="question">Question For Employee</label>
                            <div class="col-lg-10">
                                <input type="text" name="emp_question" class="form-control" id="emp_question" value required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="status">Question Status</label>
                            <div class="col-lg-10">
                                <select name="status" class="form-control" id="status">
                                    <option value="0">Deactive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="sort_order">Sort Order</label>
                            <div class="col-lg-10">
                                <input type="number" name="sort_order" class="form-control"
                                    id="order" value="0">
                            </div>
                        </div>
                         <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                         <input id="input-save" type="submit" class="btn btn-warning" value="Save &amp; add new"
                             name="submit">
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
