@extends('admin.layout.app')
@section('content')
 <style>
        /* Modal styles */
        #myModal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
            text-align: center;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover {
            color: #000;
        }
    </style>
<div class="main-container container">
        @include('admin.pages.sidebar')
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
                <li class="active">


                    Content </li>
            </ul>
        </div>

    <div class="page-content">

        <form action="{{route('admin.page.update')}}" class="relative form-horizontal" method="post">
            @csrf
            <div class="form-group">
                <div class="col-lg-2">
                    <label class="required&#x20;control-label" for="name">Name</label>
                </div>
                <div class="col-lg-10">
                    <input type="hidden" name="old_name" value="{{$name}}">
                    <input type="text" name="document-name" id="name" mminlength="4" maxlength="20" class="form-control" value="{{$name}}" >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2" for="email">Description</label>
                <div class="col-lg-10">
                    <textarea name="description" class="form-control" id="description" required>{{$content}}</textarea>
                    <div id="descriptionError" style="display: none; color: red;"></div>
                </div> 
            </div>

            <!--<input type="button" class="btn btn-danger" onclick="DeletePage()" value="Delete Page">-->
            <input type="submit" class="btn btn-warning" value="Save">
            <button class="btn btn-warning" id="openModalBtn">Delete Page</button>
        </form>
        
        
    </div>
</div>
<form action="{{ route('admin.page.delete_page') }}" method="POST" id="delete_page">
    @csrf
    <input type="hidden" name="page_name" id="setname">
</form>
 

    <!-- Modal -->
    <div id="myModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <p>Are You Sure To Delete Page</p>
            <input type="button" class="btn btn-danger" onclick="DeletePage()" value="Delete Page">
        </div>
    </div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('myModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.querySelector('.close-btn');

    // Open the modal
    openModalBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    // Close the modal when the close button is clicked
    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close the modal when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

<script>
    function DeletePage() {
        const name = document.getElementById("name").value;
        document.getElementById("setname").value = name; 
        document.getElementById("delete_page").submit();
    }
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
<script type="text/javascript">
    $(document).ready(function() {
        Gc.setOption('routes', $.parseJSON('\x7B\x22edit\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fedit\x5C\x2FitemId\x22,\x22new\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcreate\x22,\x22delete\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fdelete\x5C\x2FitemId\x22,\x22copy\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcopy\x5C\x2FitemId\x22,\x22cut\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcut\x5C\x2FitemId\x22,\x22paste\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fpaste\x5C\x2FitemId\x22,\x22publish\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fpublish\x5C\x2FitemId\x22,\x22unpublish\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Funpublish\x5C\x2FitemId\x22,\x22refresh\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Frefresh\x2Dtreeview\x5C\x2FitemId\x22\x7D'));
        Gc.initDocumentMenu(0, '\x2Fadmin\x2Fcontent\x2Fdocument\x2Fsort');
    });
</script>
@endsection
