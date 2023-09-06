@extends('layouts.app')

@section('content')
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
                        <div class="form-group mb-4">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" class="form-control" placeholder="Enter Title...">
                        </div>
                        <div class="form-group mb-4">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" id="author" class="form-control" placeholder="Enter Author...">
                        </div>
                        <button class="btn btn-success w-100">Create</button>
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
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Laravel</td>
                                    <td>Destiny</td>
                                    <td>12.1.2023</td>
                                    <td>12.2.2023</td>
                                    <td>
                                        <button class="btn btn-warning">Edit</button>
                                        <button class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
