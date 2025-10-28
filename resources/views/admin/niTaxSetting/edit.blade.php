@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FinanceController')
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
                <form class="relative form-horizontal" action="{{route('nitax-edit')}}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    <div class="form-group">
                        <input name="id" value="{{$tax->id}}" type="hidden">
                        <label class="required control-label col-lg-2" for="finance_year">Financial Year</label>
                        <div class="col-lg-10">
                            <input type="number" name="finance_year" class="form-control" id="finance_year"
                                value="{{ old('finance_year', $tax->finance_year ?? '') }}">
                            @error('finance_year')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_1">Class 4 amount One</label>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_1" class="form-control" placeholder="Min amount"
                                id="c4_min_ammount_1" value="{{ old('c4_min_ammount_1', $tax->c4_min_ammount_1 ?? '') }}">
                            @error('c4_min_ammount_1')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_tax_1" class="form-control" placeholder="Tax %"
                                id="c4_min_ammount_1_tax" value="{{ old('c4_min_ammount_tax_1', $tax->c4_min_ammount_tax_1 ?? '') }}">
                            @error('c4_min_ammount_tax_1')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_2">Class 4 amount Two</label>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_2" class="form-control" placeholder="Max amount"
                                id="c4_min_ammount_2" value="{{ old('c4_min_ammount_2', $tax->c4_min_ammount_2 ?? '') }}">
                            @error('c4_min_ammount_2')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_tax_2" class="form-control" placeholder="Tax %"
                                id="c4_min_ammount_2_tax" value="{{ old('c4_min_ammount_tax_2', $tax->c4_min_ammount_tax_2 ?? '') }}">
                            @error('c4_min_ammount_tax_2')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_3">Class 4 amount Three</label>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_3" class="form-control" placeholder="Max amount"
                                id="c4_min_ammount_3" value="{{ old('c4_min_ammount_3', $tax->c4_min_ammount_3 ?? '') }}">
                            @error('c4_min_ammount_3')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="c4_min_ammount_tax_3" class="form-control" placeholder="Tax %"
                                id="c4_min_ammount_3_tax" value="{{ old('c4_min_ammount_tax_3', $tax->c4_min_ammount_tax_3 ?? '') }}">
                            @error('c4_min_ammount_tax_3')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c2_min_amount">Class 2 amount</label>
                        <div class="col-lg-5">
                            <input type="number" name="c2_min_amount" class="form-control" placeholder="Min amount"
                                id="c2_min_amount" value="{{ old('c2_min_amount', $tax->c2_min_amount ?? '') }}">
                            @error('c2_min_amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="c2_tax" class="form-control" placeholder="Tax charge for whole year"
                                id="c2_tax" value="{{ old('c2_tax', $tax->c2_tax ?? '') }}">
                            @error('c2_tax')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
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
