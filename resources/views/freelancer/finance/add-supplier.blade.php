@extends('layouts.user_profile_app')
@php
$action = '/freelancer/save-supplier';
if (isset($supplier) && $supplier) {
$action = "/freelancer/update-supplier/{$supplier->id}";
}
@endphp
@section('content')
<section id="breadcrum" class="breadcrum">
   <div class="breadcrum-sitemap">
      <div class="container">
         <div class="row">
            <ul>
               <li><a href="/">Home</a></li>
               <li><a href="/freelancer/dashboard">My Dashboard</a></li>
               <li><a href="/freelancer/finance">Finance</a></li>
               <li><a href="/freelancer/manage-supplier">Manage Supplier</a></li>
               <li><a href="#">Add Supplier</a></li>
            </ul>
         </div>
      </div>
   </div>
   <div class="breadcrum-title">
      <div class="container">
         <div class="row">
            <div class="set-icon registration-icon">
               <i class="glyphicon glyphicon-user" aria-hidden="true"></i>
            </div>
            <div class="set-title">
               <h3>Add Supplier </h3>
            </div>
         </div>
      </div>
   </div>
</section>
<div id="primary-content" class="main-content about">
   <div class="container">
      <div class="row">
         <div class="white-bg contents">
            <section class="add_item text-left">
               <div class="col-md-12 pad0">
                  <div class="finance-page-head marb20 text-center">Add supplier </div>
               </div>
               <div class="col-md-12 pad0"></div>
               <form class="add_item_form form-inline" action="{{ $action }}" method="post" id="supplier-form">
                    @csrf
                    @if (isset($supplier) && $supplier)
                        @method('PUT')
                    @endif
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3"></div>
                            <div class="col-md-7">
                                <h1 class="mar0 text-left" id="register_head_blue"></h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cname">Contact Name</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    id="cname"
                                    name="cname"
                                    type="text"
                                    value="{{ old('cname', $supplier->name ?? '') }}"
                                    placeholder="Contact Name"
                                    class="form-control @error('cname') is-invalid @enderror"
                                    required
                                    maxlength="40"
                                    onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">
                                @error('cname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="sname">Store Name</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    id="sname"
                                    name="sname"
                                    type="text"
                                    value="{{ old('sname', $supplier->store_name ?? '') }}"
                                    placeholder="Store Name"
                                    class="form-control @error('sname') is-invalid @enderror"
                                    required
                                    maxlength="255">
                                @error('sname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="address">Address 1</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <textarea
                                    class="form-control @error('address') is-invalid @enderror"
                                    id="address"
                                    name="address"
                                    rows="2"
                                    placeholder="Address"
                                    required
                                    minlength="10"
                                    maxlength="255">{{ old('address', $supplier->address ?? '') }}</textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="addresssec">Address 2</label>
                            </div>
                            <div class="col-md-7">
                                <textarea
                                    class="form-control @error('addresssec') is-invalid @enderror"
                                    id="addresssec"
                                    name="addresssec"
                                    rows="1"
                                    placeholder="Address"
                                    minlength="10"
                                    maxlength="255">{{ old('addresssec', $supplier->second_address ?? '') }}</textarea>
                                @error('addresssec')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="town">Town</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    type="text"
                                    class="form-control @error('town') is-invalid @enderror"
                                    id="town"
                                    name="town"
                                    minlength="4"
                                    maxlength="50"
                                    placeholder="Town"
                                    value="{{ old('town', $supplier->town ?? '') }}"
                                    pattern="[A-Za-z\s]+"
                                    title="Please enter letters only"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                                @error('town')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="country">Country</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    type="text"
                                    class="form-control @error('country') is-invalid @enderror"
                                    required
                                    minlength="4"
                                    maxlength="50"
                                    id="country"
                                    name="country"
                                    placeholder="Country"
                                    value="{{ old('country', $supplier->country ?? '') }}"
                                    pattern="[A-Za-z\s]+"
                                    title="Please enter letters only"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                                @error('country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="postcode">Postcode</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    type="text"
                                    class="form-control @error('postcode') is-invalid @enderror"
                                    id="postcode"
                                    name="postcode"
                                    placeholder="Postcode"
                                    minlength="5"
                                    maxlength="8"
                                    value="{{ old('postcode', $supplier->postcode ?? '') }}">
                                @error('postcode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <div id="cpost_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cnumber">Contact No</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    id="cnumber"
                                    name="cnumber"
                                    type="text"
                                    value="{{ old('cnumber', $supplier->contact_no ?? '') }}"
                                    placeholder="Contact Number"
                                    class="form-control @error('cnumber') is-invalid @enderror"
                                    minlength="10"
                                    maxlength="11"
                                    required>
                                @error('cnumber')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <div id="cnumber_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="email">E-mail Address</label>
                                <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $supplier->email ?? '') }}"
                                    placeholder="E-mail Address"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <button type="submit" id="supplier-btn" class="read-common-btn grad_btn hide-btn">Save</button>
                            <!--<button type="button" id="supplier_submit_loding" class="read-common-btn grad_btn" disabled>Saving...</button>-->
                        </div>
                    </div>
                </form>

            </section>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script>
//   $("#supplier-form").submit(function() {
//       $('.hide-btn').hide();
//       $('#supplier_submit_loding').show();
//   });
   $("#cnumber").keyup(function() {
       var cnumber = $("#cnumber").val();
       if (isNaN(cnumber)) {
           $("#cnumber").val('');
       }
   
   });
   
   $("#cnumber").keyup(function() {
       var cnumber = $("#cnumber").val();
       if (cnumber.length < 10 || cnumber.length > 11) {
           $('#cnumber_error').html('<div id="user_error" class="css_error">Please Enter Valid Contact No .</div>');
           $('#supplier-btn').prop('disabled', true);
       } else {
           $('#cnumber_error').html('');
           $('#supplier-btn').prop('disabled', false);
       }
       if (cnumber.length === 11) {
           return false;
       }
   
   });
</script>
@endpush