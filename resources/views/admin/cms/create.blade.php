@extends('layouts.admin.app')

@section('title', 'Content Management')

@push('style')
{{-- Summernote --}}
<link rel="stylesheet" href="{{asset('assets/summernote/summernote.min.css')}}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Create Content Page</h3>
            </div>
            <form method="POST" action="{{ route('admin.cms.store') }}" enctype='multipart/form-data' onsubmit="return CMSValidation()">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title" class="required">Page Title</label>
                                <input type="text" name="title" class="form-control title" placeholder="Enter Page Title">
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                                <p class="title error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="content" class="required">Content</label>
                                <textarea name="content" id="content" cols="30" rows="10"></textarea>
                                @if ($errors->has('content'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('content') }}</strong>
                                    </span>
                                @endif
                                <p class="content_err error"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Create</button>
                    <a href="{{ url('/admin/cms') }}" class="btn btn-sm btn-danger"> <i class="fa fa-long-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript" src="{{ asset('assets/summernote/summernote.min.js') }}"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('#content').summernote({
            placeholder: 'Please add content here',
            height: 300,
        });
    });
</script>
@endpush
