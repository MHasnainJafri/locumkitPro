@extends('admin.layout.app')
@section('content')

<div class="main-container container">
    @include('admin.config.sidebar')

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

            <script type="text/javascript" src="/backend/assets/datatypes/textrich/ckeditor.js"></script>
            <script type="text/javascript" src="/backend/assets/datatypes/textrich/ckeditor-adapters-jquery.js"></script>
            <div class="relative&#x20;form-horizontal">
                <div id="accordion">
                    <h3>Email Manager Subscibe user</h3>
                    <div>
                        <form action="{{route('email.newsletter.email')}}" method="post">
                            @csrf
                            <script>
                                $(document).ready(function() {
                                    $(".select-all-checkbox").click(function() {
                                        $(".checkBoxClassSubscriber").prop('checked', $(this).prop('checked'));
                                    });
                                });
                            </script>
                            <script>
                                $(document).ready(function() {
                                    $(".select-all-checkbox2").click(function() {
                                        $(".checkBoxClassUser").prop('checked', $(this).prop('checked'));
                                    });
                                });
                            </script>
                            <div class="form-group">

                                <label class="required&#x20;control-label&#x20;col-lg-2&#x20;email-list" for="email">User Email</label>
                                <div class="col-lg-10 email-box">
                                    <label class="required control-label col-lg-2 email-list" style="color: #00A9E0; cursor: pointer;">
                                        <input type="checkbox" class="form-control select-all-checkbox"> Select All
                                    </label>
                                    @foreach($users as $user)
                                    <label class="required control-label col-lg-2 email-list"><input type="checkbox" name="email[]" id="email" class="form-control checkBoxClassSubscriber" value="{{$user -> email ?? ''}}">{{$user -> email ?? ''}}</label>
                                    @endforeach
                                     @if ($errors->has('email'))
        <div class="text-danger">
            <ul>
                @foreach ($errors->get('email') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="mail_subject">Subject</label>
                                <div class="col-lg-10 ">
                                    <input type="text" name="mail_subject" id="mail_subject" class="form-control" value="">
                                     @error('mail_subject')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="mail_message">Message</label>
                                <div class="col-lg-10 ">
                                    <textarea name="mail_message" id="description" class="form-control" rows="10"></textarea>
                                     @error('mail_message')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                            <input id="input-save" type="submit" class="btn btn-warning" value="Send" name="submit" style="margin-top:10px;">
                        </form>
                    </div>
                    <h3>Email Manager Site user</h3>
                    <div>
                        <form action="{{route('email.newsletter.emailManager')}}" novalidate method="POST">
                            @csrf
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2&#x20;email-list" for="email">User Email</label>
                                <div class="col-lg-10 email-box">'
                                <input name="sendmails" value='1' type='hidden'>

                                    <select id="filter_by_user_type" class="form-control" onchange="fetchUser(this.value);">
                                        <option value="all">All</option>
                                        @foreach($profession as $key => $values)
                                        <option value='{{ $values -> id }}'> {{$values -> name}} </option>
                                        @endforeach
                                    </select>

                                    <label class="required control-label col-lg-2 email-list" style="    color: #00A9E0;cursor: pointer;"><input type="checkbox" id="select_all_user_email" class="form-control select-all-checkbox2">Select All</label>
                                    <div id='all_u_email'>

                                        @foreach($users as $keys => $values)
                                        <label class="required control-label col-lg-2 email-list"><input type="checkbox" name="email[]" id="email" class="form-control checkBoxClassUser" value="{{$values -> email ?? ''}}"> {{$values -> email ?? ''}} </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="mail_subject">Subject</label>
                                <div class="col-lg-10 ">
                                    <input type="text" name="user_mail_subject" id="mail_subject" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="mail_message">Message</label>
                                <div class="col-lg-10 ">
                                    <textarea name="user_mail_message" id="mail_message" class="form-control" rows="10"></textarea>
                                </div>
                            </div>
                            <input id="input-save" type="submit" class="btn btn-warning" value="Send" name="submit" style="margin-top:10px;">
                        </form>
                    </div>
                    <h3>Marketing Invitation Email Manager</h3>
                    <div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{{ session('success') }}</li>
                                </ul>
                            </div>
                        @endif


                        <form action="{{route('email.newsletter.email')}}" novalidate id="formsubmit" method="post">
                            @csrf
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="marketing_mail_ids">Marketing Emails</label>
                                <div class="col-lg-10 ">
                                    <textarea name="user_marketing_mail_ids" id="marketing_mail_ids" class="form-control" placeholder="Enter&#x20;emails&#x20;like&#x20;&quot;locumkit1&#x40;gmail.com&#x3B;locumkit2&#x40;gmail.com&quot;" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="required&#x20;control-label&#x20;col-lg-2" for="marketing_mail_subject">Subject</label>
                                <div class="col-lg-10 ">
                                    <input type="text" name="user_marketing_mail_subject" id="marketing_mail_subject" class="form-control" value="">
                                    <input type="hidden" name='email' id="emailid" value="seperate_email">
                                </div>
                            </div>
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="marketing_mail_message">Message</label>
                            <div class="col-lg-10 ">
                                <textarea name="user_marketing_mail_message" id="descriptions" class="form-control" rows="10"></textarea>
                            </div>
                            <input id="input-save" type="submit" class="btn btn-warning" value="Send" name="submit" style="margin-top:10px;">
                    </div>
                    </form>

                </div>
            </div>


            </d>
            <!--<script type="text/javascript">-->
            <!--    $(function() {-->
            <!--        Gc.saveCommand();-->
            <!--        Gc.checkDataChanged();-->
            <!--        $('#accordion').accordion({-->
            <!--            heightStyle: "content",-->
            <!--            collapsible: true-->
            <!--        });-->
            <!--    });-->
            <!--</script>-->
            <script type="text/javascript">
                $(document).ready(function() {
                    var activeAccordion = "{{ session('activeAccordion', 0) }}"; // Default to 0 (first tab)
                    
                    // Initialize the accordion with the active tab set from session data
                    $("#accordion").accordion({
                        heightStyle: "content",
                        collapsible: true,
                        active: parseInt(activeAccordion) // Set the active tab based on the session value
                    });
                });
            </script>

            <script type="text/javascript">
                $(function() {
                    var config = {
                        skin: "moono",
                        toolbar: [
                            ['Source', 'Save', 'NewPage', 'DocProps', 'Preview', 'Print', 'Templates'],
                            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'Undo', 'Redo'],
                            ['Find', 'Replace', 'SelectAll', 'SpellChecker', 'Scayt'],
                            ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
                            ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'],
                            ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl'],
                            ['Link', 'Unlink', 'Anchor'],
                            ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'],
                            ['Styles', 'Format', 'Font', 'FontSize'],
                            ['TextColor', 'BGColor'],
                            ['Maximize', 'ShowBlocks', 'About'],
                        ],
                        allowedContent: true
                    };

                    $("#mail_message").ckeditor(config)
                        .ckeditor(function() {
                            this.addCommand("saveDocument", {
                                exec: function(editor, data) {
                                    $("#input-save").click();
                                }
                            });
                            this.keystrokeHandler.keystrokes[CKEDITOR.CTRL + 83 /* S */ ] = "saveDocument";
                        });
                    $("textarea#marketing_mail_message").ckeditor(config)
                        .ckeditor(function() {
                            this.addCommand("saveDocument", {
                                exec: function(editor, data) {
                                    $("#input-save").click();
                                }
                            });
                            this.keystrokeHandler.keystrokes[CKEDITOR.CTRL + 83 /* S */ ] = "saveDocument";
                        });
                });
            </script>
            <script type="text/javascript">
                function fetchUser(filter_id) {
                    $("div#all_u_email").html('');
                    $("div#all_u_email").html('<h3 style="text-align:center;"><img src="/public/frontend/images/loader.gif"> Please wait... </h3>');
                    console.log(filter_id, ' herere ');
                    $.ajax({
                        url: "{{ route('email.newsletter') }}",
                        type: 'GET',
                        data: {
                            filter_id: filter_id
                        },
                        success: function(result) {

                            $("#select_all_user_email").prop('checked', false);
                            $("#all_u_email").empty();

                            result.result.forEach(function(email) {
                                var checkbox = $("<label>", {
                                    class: "required control-label col-lg-2 email-list",
                                    html: '<input type="checkbox" name="email[]" class="form-control checkBoxClassUser" value="' + email + '"> ' + email
                                });
                                $("#all_u_email").append(checkbox);
                            });
                        }
                    });


                }
                $(document).ready(function() {
                    $("#select_all_subscriber_email").click(function() {
                        $(".checkBoxClassSubscriber").prop('checked', $(this).prop('checked'));
                    });
                });
                $(document).ready(function() {
                    $("#select_all_user_email").click(function() {
                        $(".checkBoxClassUser").prop('checked', $(this).prop('checked'));
                    });
                });
            </script>
            <style type="text/css">
                div#all_u_email {
                    display: block;
                    float: left;
                }
            </style>
        </div>
    </div>
</div>


<script type="text/javascript">
    Gc.keepAlive('/admin/keep-alive');
</script>

<a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
    <i class="glyphicon glyphicon-open"></i>
</a>
<script type="text/javascript">
    $(function() {
        Gc.saveCommand();
        Gc.checkDataChanged();
        $('#accordion').accordion({
            heightStyle: "content",
            collapsible: true
        });
    });
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/super-build/ckeditor.js"></script>
<script>
    CKEDITOR.ClassicEditor.create(document.getElementById("description"), {
        toolbar: {
            items: [
                'exportPDF', 'exportWord', '|',
                'findAndReplace', 'selectAll', '|',
                'heading', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                '-',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                'alignment', '|',
                'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                'textPartLanguage', '|',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: true
        },
        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },
        heading: {
            options: [{
                    model: 'paragraph',
                    title: 'Paragraph',
                    class: 'ck-heading_paragraph'
                },
                {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Heading 1',
                    class: 'ck-heading_heading1'
                },
                {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Heading 2',
                    class: 'ck-heading_heading2'
                },
                {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 3',
                    class: 'ck-heading_heading3'
                },
                {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 4',
                    class: 'ck-heading_heading4'
                },
                {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 5',
                    class: 'ck-heading_heading5'
                },
                {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 6',
                    class: 'ck-heading_heading6'
                }
            ]
        },
        placeholder: 'Enter The Message...',
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 22],
            supportAllValues: true
        },
        htmlSupport: {
            allow: [{
                name: /.*/,
                attributes: true,
                classes: true,
                styles: true
            }]
        },
        htmlEmbed: {
            showPreviews: true
        },
        link: {
            decorators: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                toggleDownloadable: {
                    mode: 'manual',
                    label: 'Downloadable',
                    attributes: {
                        download: 'file'
                    }
                }
            }
        },
        mention: {
            feeds: [{
                marker: '@',
                feed: [
                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                    '@sugar', '@sweet', '@topping', '@wafer'
                ],
                minimumCharacters: 1
            }]
        },
        removePlugins: [
            'AIAssistant',
            'CKBox',
            'CKFinder',
            'EasyImage',
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',
            'Pagination',
            'WProofreader',
            'MathType',
            'SlashCommand',
            'Template',
            'DocumentOutline',
            'FormatPainter',
            'TableOfContents',
            'PasteFromOfficeEnhanced',
            'CaseChange'
        ]
    });
</script>
<script>
    CKEDITOR.ClassicEditor.create(document.getElementById("descriptions"), {
        toolbar: {
            items: [
                'exportPDF', 'exportWord', '|',
                'findAndReplace', 'selectAll', '|',
                'heading', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                '-',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                'alignment', '|',
                'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                'textPartLanguage', '|',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: true
        },
        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },
        heading: {
            options: [{
                    model: 'paragraph',
                    title: 'Paragraph',
                    class: 'ck-heading_paragraph'
                },
                {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Heading 1',
                    class: 'ck-heading_heading1'
                },
                {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Heading 2',
                    class: 'ck-heading_heading2'
                },
                {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 3',
                    class: 'ck-heading_heading3'
                },
                {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 4',
                    class: 'ck-heading_heading4'
                },
                {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 5',
                    class: 'ck-heading_heading5'
                },
                {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 6',
                    class: 'ck-heading_heading6'
                }
            ]
        },
        placeholder: 'Enter The Message...',
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 22],
            supportAllValues: true
        },
        htmlSupport: {
            allow: [{
                name: /.*/,
                attributes: true,
                classes: true,
                styles: true
            }]
        },
        htmlEmbed: {
            showPreviews: true
        },
        link: {
            decorators: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                toggleDownloadable: {
                    mode: 'manual',
                    label: 'Downloadable',
                    attributes: {
                        download: 'file'
                    }
                }
            }
        },
        mention: {
            feeds: [{
                marker: '@',
                feed: [
                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                    '@sugar', '@sweet', '@topping', '@wafer'
                ],
                minimumCharacters: 1
            }]
        },
        removePlugins: [
            'AIAssistant',
            'CKBox',
            'CKFinder',
            'EasyImage',
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',
            'Pagination',
            'WProofreader',
            'MathType',
            'SlashCommand',
            'Template',
            'DocumentOutline',
            'FormatPainter',
            'TableOfContents',
            'PasteFromOfficeEnhanced',
            'CaseChange'
        ]
    });
</script>
@endsection