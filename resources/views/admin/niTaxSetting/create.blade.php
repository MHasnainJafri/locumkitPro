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
                <form class="relative form-horizontal" action="{{route('nitaxCreate')}}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="finance_year">Financial Year</label>
                        <div class="col-lg-10">
                            <input 
                                type="number" 
                                name="finance_year" 
                                class="form-control" 
                                id="finance_year" 
                                value="" 
                                oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);" 
                                title="Please enter a number with up to 9 digits" 
                                required>
                        </div>  
                    </div>
                    @error('finance_year')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror



                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_1">Class 4 amount One</label>
                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_1" 
                                   class="form-control" 
                                   placeholder="Min amount"
                                   id="c4_min_ammount_1" 
                                   value="" 
                                   minlength="4"  
                                   maxlength="10"  
                                   pattern="^\d{4,10}$" 
                                   title="Please enter a number between 4 and 10 digits long"
                                   oninput="this.value = this.value.slice(0, 10);"> 
                        </div>
                        @error('c4_min_ammount_1')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror




                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_tax_1" 
                                   class="form-control" 
                                   placeholder="Tax %" 
                                   id="c4_min_ammount_1_tax" 
                                   value="" 
                                   min="0" 
                                   max="100"
                                   oninput="this.value = this.value > 100 ? 100 : this.value">
                        </div>
                    </div>
                    @error('c4_min_ammount_tax_1')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_2">Class 4 amount Two</label>
                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_2" 
                                   class="form-control" 
                                   placeholder="Max amount"
                                   id="c4_min_ammount_2" 
                                   value="" 
                                   minlength="4" 
                                   maxlength="10"  
                                   pattern="^\d{4,10}$" 
                                   title="Please enter a number between 4 and 10 digits long"
                                   oninput="this.value = this.value.slice(0, 10);">
                        </div>
                        @error('c4_min_ammount_2')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_tax_2" 
                                   class="form-control" 
                                   placeholder="Tax %" 
                                   id="c4_min_ammount_2_tax" 
                                   value="" 
                                   min="0" 
                                   max="100"
                                   oninput="this.value = this.value > 100 ? 100 : this.value"> 
                        </div>
                            @error('c4_min_ammount_tax_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c4_min_ammount_3">Class 4 amount Three</label>
                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_3" 
                                   class="form-control" 
                                   placeholder="Max amount"
                                   id="c4_min_ammount_3" 
                                   value=""
                                   minlength="4" 
                                   maxlength="10"  
                                   pattern="^\d{4,10}$" 
                                   title="Please enter a number between 4 and 10 digits long"
                                   oninput="this.value = this.value.slice(0, 10);">
                        </div>
                            @error('c4_min_ammount_3')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c4_min_ammount_tax_3" 
                                   class="form-control" 
                                   placeholder="Tax %" 
                                   id="c4_min_ammount_3_tax" 
                                   value="" 
                                   min="0" 
                                   max="100"
                                   oninput="this.value = this.value > 100 ? 100 : this.value">
                        </div>
                    </div>
                    @error('c4_min_ammount_tax_3')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="c2_min_amount">Class 2 amount</label>
                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c2_min_amount" 
                                   class="form-control" 
                                   placeholder="Min amount"
                                   id="c2_min_amount" 
                                   value="" 
                                   minlength="4" 
                                   maxlength="10"  
                                   pattern="^\d{4,10}$" 
                                   title="Please enter a number between 4 and 10 digits long"
                                   oninput="this.value = this.value.slice(0, 10);">
                        </div>
                        @error('c2_min_amount')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div class="col-lg-5">
                            <input type="number" 
                                   name="c2_tax" 
                                   class="form-control" 
                                   placeholder="Tax charge for whole year" 
                                   id="c2_tax" 
                                   value="" 
                                   min="0" 
                                   max="100"
                                   oninput="this.value = this.value > 100 ? 100 : this.value"> <!-- Limits tax to max 100% -->
                        </div>
                    </div>
                        @error('c2_tax') 
                        <span class="text-danger">{{ $message }}</span>
                        @enderror


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
