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
        </div>
        <div class="page-content">



            <form class="relative form-horizontal" action="{{ route('admin.blog.update', $blog->id) }}" method="post" novalidate id="myform" enctype="multipart/form-data">
                @csrf
                <div class="blog-save-action" style="display: flex; justify-content: space-between;">
                    <a style="float: left;padding: 8px;" class="btn-btn-warning" href="{{ route('blog.index') }}"><span class="glyphicon glyphicon-chevron-left"></span> Back to blog</a>
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
                                <input type="text" name="title" class="form-control" id="title" value="{{$blog -> title ?? ''}}">
                                <div id="titleError" style="display: none; color: red;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Slug</label>
                            <div class="col-lg-10">
                                <input type="text" name="slug" class="form-control" id="slug" value="{{$blog -> title ?? ''}}">
                                <div id="slugError" style="display: none; color: red;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Category</label>
                            <div class="col-lg-10">
                                <div class="col-md-3">
                                    <input type="checkbox" required name="category_id" id="category_id_1" value="1" {{$blog->blog_category_id == 1 ? 'checked' : ''}} class="category-checkbox">
                                    <div id="categoryError" style="display: none; color: red;"></div>
                                    <label class="optional" for="Recent"> Recent</label>
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function() {
                                $('.category-checkbox').on('change', function() {
                                    $('.category-checkbox').prop('required', false);
                                    $(this).prop('required', true);
                                });
                            });
                        </script>
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Description</label>
                            <div class="col-lg-10">
                                <textarea name="description" class="form-control" id="description" required>{{$blog -> description ?? ''}}</textarea>
                                <div id="descriptionError" style="display: none; color: red;"></div>
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
                                <select name="status" class="form-control" id="status" required>
                                    <option value="1" {{ $blog->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $blog->status == 0 ? 'selected' : '' }}>Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="tabs-2">
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Meta Title</label>
                            <div class="col-lg-10">
                                <input type="text" name="metatitle" class="form-control" id="metatitle" value="{{$blog -> metatitle ?? ''}}">
                                <div id="metatitleError" style="display: none; color: red;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Meta Description</label>
                            <div class="col-lg-10">
                                <input type="text" name="metadescription" class="form-control" id="metadescription" value="{{$blog -> metadescription ?? ''}}">
                                <div id="metadescriptionError" style="display: none; color: red;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Meta Keywords</label>
                            <div class="col-lg-10">
                                <input type="text" name="metakeywords" class="form-control" id="metakeywords" value="{{$blog -> metakeywords ?? ''}}">
                                <div id="metakeywordsError" style="display: none; color: red;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
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

            var categoryInput = document.getElementById("category_id_1");
            var categoryvalue = categoryInput.value.trim();

            var descriptionInput = document.getElementById("description");
            var descriptionvalue = descriptionInput.value.trim();

            var metatitleInput = document.getElementById("metatitle");
            var metatitleValue = metatitleInput.value.trim();

            var metadescriptionInput = document.getElementById("metadescription");
            var metadescriptionValue = metadescriptionInput.value.trim();

            var metakeywordsInput = document.getElementById("metakeywords");
            var metakeywordsValue = metakeywordsInput.value.trim();

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

            if (categoryvalue === "") {
                displayError(categoryInput, "Category cannot be empty")
                return false;
            } else {
                clearError(categoryInput);
            }

            if (descriptionvalue === "") {
                displayError(descriptionInput, "Description cannot be empty")
                return false;
            } else {
                clearError(descriptionInput);
            }

            return true;
        }

        document.getElementById("myform").addEventListener("submit", function(event) {
            var validationResult = validateForm();
            if (!validationResult) {
                event.preventDefault();
            }
        });

        var inputFields = document.querySelectorAll("#myform input, #myform textarea");
        inputFields.forEach(function(input) {
            input.addEventListener("input", function() {
                clearError(input);
            });
        });
    });
</script>



<script type="text/javascript">
    jQuery(function() {
        jQuery("#tabs").tabs();
    });
    $(function() {
        // Gc.saveCommand();
        // Gc.checkDataChanged();
        // Gc.initRoles();
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