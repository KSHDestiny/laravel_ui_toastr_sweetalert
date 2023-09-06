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
                        </div>
                        <div class="form-group mb-4">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" id="author" class="form-control" placeholder="Enter Author...">
                        </div>
                        <button class="btn btn-success w-100" onclick="store()" id="cuBtn">Create</button>
                    </article>

                    <article>
                        <table class="table mt-5">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Created_at</th>
                                    <th>Updated_at</th>
                                    <th>Actions</th>
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
        $(document).ready(function(){
            fetch();
        })

        {{--! action --}}
        function store(){
            let title = $('#title').val();
            let author = $('#author').val();
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

        function destroy(id){
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
                    toastrMessage(status,action)
                    fetch();
                }
            })
        }

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

        function update(){
            let id = $("#id").val();
            let title = $('#title').val();
            let author = $('#author').val();
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

        {{--! function --}}
        function toastrMessage(status,action){
            if( status == 'success' ){
                let text = "";
                text = action == 'create' ? "Created" : text;
                text = action == 'update' ? "Updated" : text;
                text = action == 'destroy' ? "Deleted" : text;

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

        function fetch(){
            $.ajax({
                url: "{{ route('home.fetch') }}",
                methd: "GET",
                success: function({lists}){
                    let data="";
                    let i = 1;
                    if(lists.length > 0){
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
            })
        }
@endsection
