@extends('admin.layout.app')
@section('content')
@inject('controller', 'App\Http\Controllers\admin\IndustryNewsController')
<div class="main-container container">
    @include('admin.pages.sidebar')

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
            <script type="text/javascript">
                jQuery(function() {
                    jQuery("#tabs").tabs();
                });
            </script>
            <style type="text/css">
                .blog-save-action {
                    text-align: right;
                }
            </style>


            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    function displayError(element, message) {
                        var errorContainer = document.getElementById(element.id + "Error");
                        errorContainer.innerHTML = message;
                        errorContainer.style.display = "block";
                        element.focus();
                    }

                    function clearError(element) {
                        var errorContainer = document.getElementById(element.id + "Error");
                        errorContainer.innerHTML = "";
                        errorContainer.style.display = "none";
                    }

                    function validateForm() {
                        var titleInput = document.getElementById("title");
                        var titleValue = titleInput.value.trim();

                        var slugInput = document.getElementById("slug");
                        var slugValue = slugInput.value.trim();

                        var descriptionInput = document.getElementById("description");
                        var descriptionValue = descriptionInput.value.trim();

                        var metatitleInput = document.getElementById("metatitle");
                        var metatitleValue = metatitleInput.value.trim();

                        var metadescriptionInput = document.getElementById("metadescription");
                        var metadescriptionValue = metadescriptionInput.value.trim();

                        var metakeywordsInput = document.getElementById("metakeywords");
                        var metakeywordsValue = metakeywordsInput.value.trim();

                        var imageInput = document.getElementById("image");
                        var imageValue = imageInput.value.trim();

                        if (metatitleValue === "") {
                            displayError(metatitleInput, "Meta Title cannot be empty");
                            return false;
                        } else {
                            clearError(metatitleInput);
                        }

                        if (metadescriptionValue === "") {
                            displayError(metadescriptionInput, "Meta Description cannot be empty");
                            return false;
                        } else {
                            clearError(metadescriptionInput);
                        }

                        if (metakeywordsValue === "") {
                            displayError(metakeywordsInput, "Meta Keywords cannot be empty");
                            return false;
                        } else {
                            clearError(metakeywordsInput);
                        }

                        if (titleValue === "") {
                            displayError(titleInput, "Title cannot be empty");
                            return false;
                        } else {
                            clearError(titleInput);
                        }

                        if (slugValue === "") {
                            displayError(slugInput, "Slug cannot be empty");
                            return false;
                        } else {
                            clearError(slugInput);
                        }

                        if (descriptionValue === "") {
                            displayError(descriptionInput, "Description cannot be empty");
                            return false;
                        } else {
                            clearError(descriptionInput);
                        }

                        if (imageValue === "") {
                            displayError(imageInput, "Please select an image");
                            return false;
                        } else {
                            clearError(imageInput);
                        }

                        return true;
                    }

                    document.getElementById("myform").addEventListener("submit", function(event) {
                        var validationResult = validateForm();
                        if (!validationResult) {
                            event.preventDefault();
                        }
                    });
                });
            </script>


            <div class="col-lg-12">
                <form class="relative form-horizontal" action="{{route('Industry.store')}}" novalidate method="post" id="myform" enctype="multipart/form-data">
                    @csrf
                    <div class="blog-save-action">
                        <a style="float: left;padding: 8px;" class="btn-btn-warning" href="{{route('industry.index')}}"><span class="glyphicon glyphicon-chevron-left"></span> Back to blog</a>
                        <input type="submit" class="btn btn-warning" value="Save" name="submit">
                    </div>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Content</a></li>
                            <li><a href="#tabs-2">SEO Data</a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Title</label>
                                <div class="col-lg-10">
                                    <input type="text" name="title" class="form-control" id="title" value="">
                                    <div id="titleError" style="display: none; color: red;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Slug</label>
                                <div class="col-lg-10">
                                    <input type="text" name="slug" class="form-control" id="slug" value="">
                                    <div id="slugError" style="display: none; color: red;"></div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Description</label>
                                <div class="col-lg-10">
                                    <textarea name="description" class="form-control" id="description"></textarea>
                                    <div id="descriptionError" style="display: none; color: red;"></div>

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">User Type</label>
                                <div class="col-lg-10">
                                    <div class="col-md-3">
                                        <input type="checkbox" name="user_type[]" id="user_type" value="2">
                                        <label class="optional" for="Locums"> Locums</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="user_type[]" id="user_type" value="3">
                                        <label class="optional" for="Employers"> Employers </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Profession</label>
                                <div class="col-lg-10">

                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="9">
                                        <label class="optional" for="Employers"> Audiologists </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="1">
                                        <label class="optional" for="Employers"> Dentistry </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="10">
                                        <label class="optional" for="Employers"> Dispensing Optician / Contact lens Optician </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="8">
                                        <label class="optional" for="Employers"> Domiciliary Opticians </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="3">
                                        <label class="optional" for="Employers"> Optometry </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="profession_type[]" id="profession_type" value="4">
                                        <label class="optional" for="Employers"> Pharmacy </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Image Upload</label>
                                <div class="col-lg-10">
                                    <input type="file" name="image" id="image" />
                                    <div id="imageError" style="display: none; color: red;"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Status</label>
                                <div class="col-lg-10">
                                    <select name="status" class="form-control" id="status">
                                        <option value="1">Active</option>
                                        <option value="0" selected>Disable</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="tabs-2">
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Meta Title</label>
                                <div class="col-lg-10">
                                    <input type="text" name="metatitle" class="form-control" id="metatitle" value="">
                                    <div id="metatitleError" style="display: none; color: red;"></div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Meta Description</label>
                                <div class="col-lg-10">
                                    <input type="text" name="metadescription" class="form-control" id="metadescription" value="">
                                    <div id="metadescriptionError" style="display: none; color: red;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2" for="email">Meta Keywords</label>
                                <div class="col-lg-10">
                                    <input type="text" name="metakeywords" class="form-control" id="metakeywords" value="">
                                    <div id="metakeywordsError" style="display: none; color: red;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    Gc.keepAlive('/admin/keep-alive');
</script>

<a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
    <i class="glyphicon glyphicon-open"></i>
</a>
</div>
</div>

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
        placeholder: 'Enter Description here...',
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