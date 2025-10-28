@extends('admin.layout.app')
@section('content')
<style>
    .flash-message {
    transition: opacity 0.5s ease-in-out;
}

</style>
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
                   @if(session('error'))
    <div class="alert alert-danger flash-message">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success flash-message">{{ session('success') }}</div>
@endif
                <table class="table clickable table-striped table-hover">
                    <colgroup>
                        <col width="30%">
                        <col width="40%">
                        <col width="20%">
                        <col width="5%">
                        <col width="5%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Package Description</th>
                            <th>Package Price</th>
                            @cando('package/edit')
                            <th class="text-center">Edit</th>
                            @endcando
                            @cando('package/delete')
                            <th class="text-center">Delete</th>
                            @endcando
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $index => $pkg)
                            <tr>
                                <td>{{ $pkg->name }}</td>
                                <td> 
                                    @php
                                        $words = explode(' ', $pkg->description);
                                        $wordLimit = 3;
                                    @endphp
                                
                                    @if(count($words) > $wordLimit)
                                        <span class="short-description bg-danger" id="short-description-{{ $index }}">
                                            {{ implode(' ', array_slice($words, 0, $wordLimit)) }}...
                                        </span>
                                        <span class="full-description" id="full-description-{{ $index }}" style="display: none;">
                                            {{ $pkg->description }}
                                        </span>
                                        <button class="toggle-button" style="background: #F5F5F5;border: none;text-decoration: underline;" id="toggle-button-{{ $index }}" onclick="toggleDescription({{ $index }})">
                                            Read More
                                        </button>
                                    @else
                                        <span class="full-description" id="full-description-{{ $index }}">
                                            {{ $pkg->description }}
                                        </span>
                                    @endif
                                </td>
                                

                                <td>{{ $pkg->price }}</td>
                                @cando('package/edit')
                                <td class="text-center">

                                    <a href="{{ route('admin.package.edit', $pkg) }}" class="edit-line">
                                        <img src="/backend/images/icones/edit.png" alt="Edit">
                                    </a>
                                </td>
                                @endcando
                                @cando('package/delete')
                                <td class="text-center">
                                    <form id="delete_form" action="{{ route('admin.package.destroy', $pkg->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <!--<button type="submit" class="delete-line">-->
                                        <!--    <img src="/backend/images/icones/delete.png" alt="Delete">-->
                                        <!--</button>-->
                                        <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$pkg->id}}">
                                            <img src="/backend/images/icones/delete.png"
                                                alt="Delete">
                                        </button>
                                    </form>
                                </td>
                                @endcando
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalCenter_{{$pkg->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Delete Package</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure to delete this Package?
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onClick="deleteRole()" class="btn btn-danger">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <script>
                                function toggleDescription(index) {
                                    const shortDesc = document.getElementById(`short-description-${index}`);
                                    const fullDesc = document.getElementById(`full-description-${index}`);
                                    const button = document.getElementById(`toggle-button-${index}`);
                                
                                    if (fullDesc.style.display === "none") {
                                        fullDesc.style.display = "inline";
                                        shortDesc.style.display = "none";
                                        button.textContent = "Read Less";
                                    } else {
                                        fullDesc.style.display = "none";
                                        shortDesc.style.display = "inline";
                                        button.textContent = "Read More";
                                    }
                                }
                            </script>
                        @endforeach


                    </tbody>
                </table>

            </div>
        </div>
    </div>
<script>
    // Auto-dismiss flash messages after 5 seconds
    setTimeout(function() {
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500); // remove from DOM after fade
        });
    }, 5000); // 5 seconds
</script>

    <script>
        function deleteRole() {
            $("#delete_form").submit()
        }
    </script>
@endsection
