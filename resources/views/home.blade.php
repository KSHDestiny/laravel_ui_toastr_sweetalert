@extends('layouts.app')


@section('content')
<link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <article>
                        <input type="hidden" id="id">
                        <div class="form-group mb-4">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" class="form-control" placeholder="Enter Title...">
                            <span class="text-danger" id="titleError"></span>
                        </div>
                        <div class="form-group mb-4">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" id="author" class="form-control" placeholder="Enter Author...">
                            <span class="text-danger" id="authorError"></span>
                        </div>
                        <button class="btn btn-success w-100" onclick="store()" id="cuBtn">Create</button>
                    </article>

                    <article class="mt-5">
                        <div class="d-flex w-50 float-end">
                            <input type="text" placeholder="Search..." id="searchKey" class="form-control">
                            <button class="btn btn-success" onclick="search()">Search</button>
                        </div>
                    </article>

                    <article class="mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Created_at</th>
                                    <th>Updated_at</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="tableData">
                            </tbody>
                        </table>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
{{--! action --}}
    {{-- ? Read --}}
    $(document).ready(function(){
        fetch();
    })

    {{-- ? Create --}}
    function store(){
        let title = $('#title').val();
        let author = $('#author').val();

        validation(title,author)

        $.ajax({
            url: "{{ route('home.store') }}",
            method: "POST",
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            },
            data: {title,author},
            success: function({status,action}){
                $('#title,#author').val("");
                toastrMessage(status,action)
                fetch();
            }
        })
    }

    {{-- ? Delete --}}
    function destroy(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Deleted!','Your file has been deleted.','success');
                let route = "{{ route('home.destroy',':id') }}";
                route = route.replace(':id',id);
                $.ajax({
                    url: route,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}'
                    },
                    data: {id},
                    success: function({status,action}){
                        fetch();
                    }
                })
            }
        })
    }

    {{-- ? Edit --}}
    function edit(id){
        let route = "{{ route('home.edit',':id') }}";
        route = route.replace(':id',id);
        $.ajax({
            url: route,
            method: "GET",
            data: {id},
            success: function({data}){
                $("#id").val(data.id);
                $("#title").val(data.title);
                $("#author").val(data.author);
                $("#cuBtn").text("Update").attr('onclick','update()');
            }
        })
    }

    {{-- ? Update --}}
    function update(){
        let id = $("#id").val();
        let title = $('#title').val();
        let author = $('#author').val();

        validation(title,author)

        let route = "{{ route('home.update',':id') }}";
        route = route.replace(':id',id);
        $.ajax({
            url: route,
            method: "PUT",
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            },
            data: {id,title,author},
            success: function({status,action}){
                $('#id,#title,#author').val("");
                $("#cuBtn").text("Create").attr('onclick','store()');
                toastrMessage(status,action)
                fetch();
            }
        })
    }

    {{-- ? Data Searching --}}
    function search(){
        let searchKey = $("#searchKey").val();
        if(searchKey !== ""){
            let route = "{{ route('home.search',':searchKey') }}";
            route = route.replace(":searchKey",searchKey);
            $.ajax({
                url: route,
                method: "GET",
                success: function({lists}){
                    fetchingData(lists);
                }
            })
        }else {
            fetch();
        }
    }

{{--! function --}}
    {{-- ? Validation Title & Author --}}
    function validation(title,author){
        titleError = title == "" && true;
        titleLengthError = title.length > 50 && true;
        authorError = author == "" && true;
        authorLengthError = author.length > 50 && true;

        if(titleError){
            $('#titleError').text("Title must not be empty.");
        } else if (titleLengthError){
            $('#titleError').text("Title must not exceed 50 charactors.");
        } else {
            $('#titleError').text("");
        }
        if (authorError){
            $('#authorError').text("Author must not be empty.");
        } else if (authorLengthError){
            $('#authorError').text("Author must not exceed 50 charactors.");
        } else {
            $('#authorError').text("");
        }

    }

    {{-- ? Using ToastrMessage for Create & Update --}}
    function toastrMessage(status,action){
        if( status == 'success' ){
            let text = "";
            text = action == 'create' ? "Created" : text;
            text = action == 'update' ? "Updated" : text;

            toastr.success(`One List is Successfully ${text}! &nbsp;<i class="far fa-check-circle"></i>`, 'SUCCESS MESSAGE', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right",
            });
        }else{
            toastr.error('Something Went Wrong! &nbsp;<i class="far fa-times-circle"></i>', 'ERROR MESSAGE', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right",
            });
        }
    }

    {{-- ? Get Data to fetch --}}
    function fetch(){
        $.ajax({
            url: "{{ route('home.fetch') }}",
            methd: "GET",
            success: function({lists}){
                fetchingData(lists);
                $("#searchKey").val("");
            }
        })
    }

    {{-- ? Fetching Data in table body --}}
    function fetchingData(lists){
        if(lists.length > 0){
            let data="";
            let i = 1;
            lists.forEach(item => {
                data += `
                <tr class='align-middle'>
                    <td>`+i+`</td>
                    <td>`+item.title+`</td>
                    <td>`+item.author+`</td>
                    <td>`+new Date(item.created_at).toDateString()+`</td>
                    <td>`+new Date(item.updated_at).toDateString()+`</td>
                    <td>
                        <button class='btn btn-warning' onclick="edit(`+item.id+`)">Edit</button>
                    </td>
                    <td>
                        <button class='btn btn-danger' onclick="destroy(`+item.id+`)">Delete</button>
                    </td>
                </tr>
                `;

                $("#tableData").html(data);
                i++;
            });
        } else {
            data = `<tr><td colspan='6' class='text-center text-danger display-6'><span>No Data!</span></td></tr>`;
            $("#tableData").html(data);
        }
    }
@endsection
