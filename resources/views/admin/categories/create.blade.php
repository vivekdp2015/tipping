@extends('layouts.admin.app')

@section('title', 'Category')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Create Category</h3>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" onsubmit="return categoryValidation()">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="required">Title</label>
                                <input type="text" name="title" class="form-control title" placeholder="Enter Category Title" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                                <p class="title_err error"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Create</button>
                    <a href="{{ url('/admin/categories') }}" class="btn btn-sm btn-danger"> <i class="fa fa-long-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
