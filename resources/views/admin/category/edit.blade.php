@extends('admin.layout.app')
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

                    <form class="relative form-horizontal" action="{{route('categories.update', $category->id)}}" method="post"


                        enctype="application/x-www-form-urlencoded">
                        @csrf
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="name">Profession
                                Name</label>
                            <div class="col-lg-10">
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{$category->name}}"
                                     required 
                                       minlength="3" 
                                       maxlength="50" 
                                       pattern="[A-Za-z\s]+" 
                                       title="Please enter only alphabets and spaces." 
                                       oninput="validateInput(this)">
                            </div>
                        </div>
                        <script>
                            function validateInput(input) {
                                input.value = input.value.replace(/[^A-Za-z\s]/g, '');
                            }
                        </script>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="name">Status</label>
                            <div class="col-lg-10">
                                <select name="status" class="form-control" id="">
                                    <option {{$category->is_active === 1 ? 'selected' : ''}} value="1">Active</option>
                                    <option {{$category->is_active === 0 ? 'selected' : ''}} value="1">In-Active</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="name">Profession
                                Status</label>
                            <div class="col-lg-10">
                                <select class="form-control" name="status">
                                    <option selected>Select Status</option>
                                    <option {{$category->status == 1 ? 'selected':''}} value='1'>Active</option>
                                    <option {{$category->status == 0 ? 'selected':''}} value='0'>In-Active</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <label class="optional&#x20;control-label&#x20;col-lg-2" for="description">Description</label>
                            <div class="col-lg-10">
                                <div id="qus_field">
                                    <p><input type="text" name="description" class="form-control" id="description"
                                            value="{{$category->description}}"></p>
                                </div>

                            </div>
                        </div>
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                        <!--<input id="input-save" type="submit" class="btn btn-warning" value="Save & add new"-->
                        <!--    name="submit">-->
                        <a href="{{route('admin.category.index')}}" class="btn btn-danger">Cancel</a>
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
