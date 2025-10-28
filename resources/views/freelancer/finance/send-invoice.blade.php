@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/finance">Finance</a></li>
                        <li><a href="#">Send Invoice</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Send invoice</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content register">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    @if ($income_record->invoice_id)
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> Alert!</h4>Invoice already generated!
                        </div>
                    @else
                        <section class="add_item send_invoice pb30 text-left">
                            <div class="col-md-12 pad0">
                                <div class="text-capitalize finance-page-head text-center">Send invoice</div>
                            </div>
                            <div class="col-md-12 pad0"></div>
                            <div class="col-md-12 pad0">
                                <form id="invoice_form" action="/freelancer/save-send-invoice" method="POST">
    <input type="hidden" name="income_id" value="{{ $income_record->id }}" style="display: none;" hidden>
    <input type="hidden" name="job_id" value="{{ $income_record->job_id }}" style="display: none;" hidden>
    <input type="hidden" name="job_date" value="{{ $income_record->job_date }}" style="display: none;" hidden>
    <input type="hidden" name="job_rate" value="{{ $income_record->job_rate }}" style="display: none;" hidden>
    @csrf
    @method('POST')
    <section class="add_item_form form-inline">
        <div class="col-md-12" id="form-div">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4 information-tile text-right">
                        <a href="javascript:void(0);" title="Click here for your information" data-toggle="collapse" data-target="#yourinfo">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>Your information
                        </a>
                    </div>
                    <div class="col-md-7"></div>
                </div>
            </div>
            <div id="yourinfo" class="collapse">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-4"><label for="your_name">Your name</label></div>
                        <div class="col-md-7">
                            <input type="hidden" class="form-control" name="your_email" id="your_email" value="{{ $user_invoice_data['email'] }}" readonly />
                            <input type="text" class="form-control" name="your_name" id="your_name" value="{{ $user_invoice_data['name'] }}" required />
                        </div>
                    </div>
                    @error('your_name')
                        <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-4"><label for="your_address">Your address</label></div>
                        <div class="col-md-7">
                            <textarea class="form-control" name="your_address" id="your_address" required readonly>{{ $user_invoice_data['address'] }}</textarea>
                        </div>
                    </div>
                    @error('your_address')
                        <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-4"><label for="your_contact">Your contact No.</label></div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="your_contact" id="your_contact" placeholder="Contact Number" value="{{ $user_invoice_data['contact_no'] }}" required readonly />
                        </div>
                    </div>
                    @error('your_contact')
                        <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Supplier Section -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_store">Supplier Store Name <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7" id="searchfield">
                        <input type="text" id="autocomplete" name="supplier_store" class="form-control" placeholder="Store Name" required value="{{ old('supplier_store') }}" autocomplete="off" />
                    </div>
                </div>
                @error('supplier_store')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_name">Supplier name <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_name" name="supplier_name" class="form-control" placeholder="Name" required value="{{ old('supplier_name') }}" />
                    </div>
                </div>
                @error('supplier_name')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supplier Email -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_email">Supplier email id <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="email" id="supplier_email" name="supplier_email" class="form-control" placeholder="Email" required value="{{ old('supplier_email') }}" />
                    </div>
                </div>
                @error('supplier_email')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supplier Address -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_address">Supplier address 1<i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_address" name="supplier_address" class="form-control" placeholder="Address" value="{{ old('supplier_address') }}" required />
                    </div>
                </div>
                @error('supplier_address')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supplier Address 2 (Optional) -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_address2">Supplier address 2</label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_address2" name="supplier_address2" class="form-control" placeholder="Address" value="{{ old('supplier_address2') }}" />
                    </div>
                </div>
            </div>

            <!-- Supplier Town -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_town">Town <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_town" name="supplier_town" class="form-control" placeholder="Town" value="{{ old('supplier_town') }}" required />
                    </div>
                </div>
                @error('supplier_town')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supplier Country -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_country">Country <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_country" name="supplier_country" class="form-control" placeholder="Country" value="{{ old('supplier_country') }}" required />
                    </div>
                </div>
                @error('supplier_country')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supplier Postcode -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="supplier_postcode">Postcode <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                    <div class="col-md-7">
                        <input type="text" id="supplier_postcode" name="supplier_postcode" class="form-control" placeholder="Postcode" value="{{ old('supplier_postcode') }}" />
                    </div>
                </div>
                @error('supplier_postcode')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Account Name, Number, Sort Code -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="acc_name">Your bank account name</label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                    <div class="col-md-7">
                        <input id="acc-name" name="acc_name" type="text" value="{{ $user_invoice_data['acccount_name'] }}" placeholder="Enter account name" class="form-control" required>
                    </div>
                </div>
                @error('acc_name')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="acc_number">Your bank account number</label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                    <div class="col-md-7">
                        <input id="acc-number" name="acc_number" type="text" value="{{ $user_invoice_data['account_number'] ?? '' }}" placeholder="00000000000000000" pattern="\d{12,17}" class="form-control" required pattern="\d{12,17}" title="Account number must be 12 to 17 digits long">
                    </div>
                </div>
                @error('acc_number')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="acc_sort_code">Your bank account sort code</label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                    <div class="col-md-7">
                        <input id="acc-sort-code" name="acc_sort_code" type="text" value="{{ $user_invoice_data['acccount_sort_code'] }}" placeholder="Sort code" class="form-control" required>
                    </div>
                </div>
                @error('acc_sort_code')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Template Choice -->
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-4"><label for="template-choice">Select invoice template</label></div>
                    <div class="col-md-7">
                        <select class="form-control" name="template-choice" id="template-choice" required>
                            <option value="invoice1" selected>Invoice Template One</option>
                            <option value="invoice2">Invoice Template Two</option>
                        </select>
                    </div>
                </div>
                @error('template-choice')
                    <div class="text-danger col-md-7 col-md-offset-4">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="col-md-12">
                <div class="form-group text-center" style="margin-top: 20px;">
                    <button type="submit" name="preview_btn" value="preview" class="read-common-btn generated_btn pull-right">Send Invoice</button>
                </div>
            </div>
        </div>
    </section>
</form>

                                <!--<form id='invoice_form' action="/freelancer/save-send-invoice" method="POST">-->
                                <!--    <input type="hidden" name="income_id" value="{{ $income_record->id }}" style="display: none;" hidden>-->
                                <!--    <input type="hidden" name="job_id" value="{{ $income_record->job_id }}" style="display: none;" hidden>-->
                                <!--    <input type="hidden" name="job_date" value="{{ $income_record->job_date }}" style="display: none;" hidden>-->
                                <!--    <input type="hidden" name="job_rate" value="{{ $income_record->job_rate }}" style="display: none;" hidden>-->
                                <!--    @csrf-->
                                <!--    @method('POST')-->
                                <!--    <section class="add_item_form form-inline">-->
                                <!--        <div class="col-md-12" id="form-div">-->
                                <!--            <div class="col-md-12">-->
                                <!--                <div class="form-group">-->
                                <!--                    <div class="col-md-4 information-tile text-right"><a href="javascript:void(0);" title="Click here for your information" data-toggle="collapse" data-target="#yourinfo"><i-->
                                <!--                               class="fa fa-info-circle" aria-hidden="true"></i>Your information</a></div>-->
                                <!--                    <div class="col-md-7"></div>-->
                                <!--                </div>-->
                                <!--            </div>-->
                                <!--            <div id="yourinfo" class="collapse">-->
                                <!--                this is form code here when some validation error occurr when i submit the form than all the previous data is lost -->
                                <!--                so give me the solutoin for this-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Your name</label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="hidden" class="form-control" name="your_email" id="your_email" value="{{ $user_invoice_data['email'] }}" readonly />-->
                                <!--                            <input type="text" class="form-control" name="your_name" id="your_name" value="{{ $user_invoice_data['name'] }}" readonly required />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Your address</label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <textarea class="form-control" placeholder="Address" name="your_address" id="your_address" required readonly>{{ $user_invoice_data['address'] }}</textarea>-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Your contact No.</label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="text" class="form-control" name="your_contact" id="your_contact" placeholder="Contact Number" value="{{ $user_invoice_data['contact_no'] }}" required readonly />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--            </div>-->
                                <!--            <div class="col-md-12">-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4">-->
                                <!--                            <label for="exampleInputPassword1">Supplier Store Name <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label>-->
                                <!--                        </div>-->
                                <!--                        <div class="col-md-7" id="searchfield">-->
                                <!--                            <input type="text" id="autocomplete" name="supplier_store" class="form-control" placeholder="Store Name" required value="{{ old('supplier_store') }}" autocomplete="off" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                                
                                <!--                <div class="col-md-12">-->
                                <!--                    <input type="hidden" id="supplier_id" name="supplier_id" value="" />-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Supplier name <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>-->
                                <!--                        <div class="col-md-7" id="supplier_name_text">-->
                                <!--                            <input type="text" id="supplier_name" name="supplier_name" class="form-control" placeholder="Name" required value="{{old('supplier_name')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <script>-->
                                <!--                    document.getElementById('supplier_name').addEventListener('input', function (event) {-->
                                <!--                        const storeNameInput = event.target.value;-->
                                                
                                                        <!--// Check if the input contains any numbers-->
                                <!--                        if (/\d/.test(storeNameInput)) {-->
                                                            <!--// Alert user if the store name contains numbers-->
                                <!--                            alert("The Supplier name should not contain numbers.");-->
                                                            <!--event.target.value = storeNameInput.replace(/\d/g, ''); // Remove numbers-->
                                <!--                        }-->
                                <!--                    });-->
                                <!--                </script>  -->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Supplier email id <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="email" id="supplier_email" name="supplier_email" class="form-control" placeholder="Email" required value="{{old('supplier_email')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Supplier address 1<i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>-->
                                <!--                        <div class="col-md-7">-->

                                <!--                            <input required type="text" id="supplier_address" name="supplier_address" class="form-control" placeholder="Address" value="{{old('supplier_address')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Supplier address 2</label></div>-->
                                <!--                        <div class="col-md-7">-->

                                <!--                            <input type="text" id="supplier_address2" name="supplier_address2" class="form-control" placeholder="Address" value="{{old('supplier_address2')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Town <i class="fa fa-asterisk required-stars" aria-hidden="true"></i> </label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="text" required id="supplier_town" name="supplier_town" class="form-control" placeholder="Town" value="{{old('supplier_town')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Country <i class="fa fa-asterisk required-stars" aria-hidden="true"></i> </label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="text" value required id="supplier_country" name="supplier_country" class="form-control" placeholder="Country" value="{{old('supplier_country')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Postcode <i class="fa fa-asterisk required-stars" aria-hidden="true"></i> </label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input type="text" id="supplier_postcode" name="supplier_postcode" class="form-control" placeholder="Postcode" value="{{old('supplier_postcode')}}" />-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->

                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Your bank account name </label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input id="acc-name" minlength="4" maxlength="50" required name="acc_name" type="text" value="{{ $user_invoice_data['acccount_name'] }}" placeholder="Enter account name" class="form-control" required>-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4">-->
                                <!--                            <label for="exampleInputPassword1">Your bank account number </label>-->
                                <!--                            <i class="fa fa-asterisk required-stars" aria-hidden="true"></i>-->
                                <!--                        </div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input -->
                                <!--                                id="acc-number" -->
                                <!--                                name="acc_number" -->
                                <!--                                type="text" -->
                                <!--                                value="{{ $user_invoice_data['account_number'] ?? '' }}" -->
                                <!--                                placeholder="00000000000000000"-->
                                <!--                                class="form-control" -->
                                <!--                                required -->
                                <!--                                pattern="\d{12,17}" -->
                                <!--                                title="Account number must be 12 to 17 digits long"-->
                                <!--                            >-->
                                <!--                            <div id="acc_number_error"></div>-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->

                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Your bank account sort code </label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <input id="acc-sort-code" name="acc_sort_code" type="text" value="{{ $user_invoice_data['acccount_sort_code'] }}" placeholder="XX-XX-XX" class="form-control" required>-->
                                <!--                            <div id="acc_sort_code_error"></div>-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->

                                <!--                <div class="col-md-12" id="template-div">-->
                                <!--                    <style>-->
                                <!--                    .btn {-->
                                                           
                                <!--                            padding: 8px 4px;-->
                                <!--                        }-->
                                <!--                    </style>-->
                                <!--                    <div class="form-group">-->
                                <!--                        <div class="col-md-4"><label for="exampleInputPassword1">Select invoice template</label></div>-->
                                <!--                        <div class="col-md-7">-->
                                <!--                            <div class="input-group col-md-12 d-flex" style="display: flex">-->
                                <!--                                <select class="form-control" name="template-choice" id="template-choice" required style="margin-right: 10px;>-->
                                <!--                                    <option value="invoice1" selected>Invoice Template One</option>-->
                                <!--                                    <option value="invoice2">Invoice Template Two</option>-->
                                <!--                                </select>-->
                                <!--                                <span class="input-group-btn">-->
                                <!--                                    <button type="button" id="change-preview-btn" class="btn btn-info btn-flat">Change & Preview</button>-->
                                <!--                                </span>-->
                                <!--                            </div>-->
                                <!--                        </div>-->
                                <!--                    </div>-->

                                <!--                    <div id="preview-div" class="mt-4 mb-4"></div>-->
                                <!--                </div>-->

                                <!--                <div class="col-md-12">-->
                                <!--                    <div class="col-md-4"></div>-->
                                <!--                    <div class="col-md-7">-->
                                <!--                        <div class="form-group text-center" style="margin-top: 20px;">-->
                                <!--                            <button type="submit" name="preview_btn" value="preview" class="read-common-btn generated_btn pull-right">Send Invoice</button>-->
                                <!--                        </div>-->
                                <!--                    </div>-->
                                <!--                </div>-->
                                <!--            </div>-->
                                <!--        </div>-->

                                <!--    </section>-->
                                <!--</form>-->
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            /* Injecting supplier data */
            const suppliers = @json($suppliers);
            const supplierObject = [];
            suppliers.forEach(s => {
                let o = {
                    label: s.store_name,
                    value: s.store_name,
                    data: s
                };
                supplierObject.push(o);
            });

            $("#autocomplete").autocomplete({
                    minLength: 0,
                    source: supplierObject,
                    select: function(event, ui) {
                        var selectedSupplier = ui.item.data;
                        $('#supplier_name').val(selectedSupplier.name);
                        $('#supplier_id').val(selectedSupplier.id);
                        $('#supplier_email').val(selectedSupplier.email);
                        $('#supplier_address').val(selectedSupplier.address);
                        $('#supplier_address2').val(selectedSupplier.second_address);
                        $('#supplier_town').val(selectedSupplier.town);
                        $('#supplier_country').val(selectedSupplier.country);
                        $('#supplier_postcode').val(selectedSupplier.postcode);
                    },
                })
                .data("ui-autocomplete")._renderItem = function(ul, item) {
                    console.log(item);
                    return $("<li>")
                        .append("<a>" + item.data.name + "</a>")
                        .appendTo(ul);
                };

        });


        $('form#invoice_form').submit(function(e) {
            e.preventDefault();
            let accNumber = $('input#acc-number').val().trim();
            let isValid = true;
        
            // Clear previous error messages
            $('#acc_number_error').html('');
        
            // Validate Account Number
            if (accNumber.length < 12 || accNumber.length > 17 || !/^\d+$/.test(accNumber)) {
                $('#acc_number_error').html('<div id="user_error" class="css_error">Please enter a valid Account Number (12 to 17 digits).</div>');
                isValid = false;
            }
        
            if (isValid) {
                // Proceed with form submission
                this.submit();
            }
        });
        
        $('input#acc-number').on('keydown', function(e) {
            var key = e.which || e.keyCode;
            if (
                !(key >= 48 && key <= 57) &&
                !(key >= 96 && key <= 105) &&
                ![8, 9, 37, 39, 46].includes(key)
            ) {
                e.preventDefault();
            }
        
            if ($(this).val().length >= 17 && ![8, 37, 39, 46].includes(key)) {
                e.preventDefault();
            }
        });


        $('#acc-sort-code').keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            $acc_sort_code = $(this);

            if (key !== 8 && key !== 9) {
                if ($acc_sort_code.val().length === 2) {
                    $acc_sort_code.val($acc_sort_code.val() + '-');
                }
                if ($acc_sort_code.val().length === 5) {
                    $acc_sort_code.val($acc_sort_code.val() + '-');
                }
                if ($acc_sort_code.val().length === 8) {
                    return false;
                }
            }

            return (key == 8 || key == 9 || key == 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
        }).bind('focus click', function() {
            $acc_sort_code = $(this);

            if ($acc_sort_code.val().length === 0) {
            } else {
                var val = $acc_sort_code.val();
                $acc_sort_code.val('').val(val);
            }
        }).blur(function() {
            $acc_sort_code = $(this);

            if ($acc_sort_code.val() === '(') {
                $acc_sort_code.val('');
            }
        });

        $("#change-preview-btn").click(function() {
            let form = document.getElementById("invoice_form");
            if (form.checkValidity() == false) {
                form.reportValidity();
                return;
            }

            $.ajax({
                url: '/ajax/get-invoice-template',
                type: 'POST',
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                },
                data: $(form).serialize(),
                success: function(result) {
                    if (result.success) {
                        $("#preview-div").html(result.html);
                    } else {
                        alert(result.message)
                    }
                    console.log(result);
                }
            });

        });
    </script>
@endpush
