@extends('layouts.admin.app')

@section('title', 'Notification Sounds')

@push('style')
{{-- Datatable --}}
<link rel="stylesheet" href="{{asset('assets/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@endpush

@section('content')
@if(session()->has('message'))
    <p class="alert {{ session()->get('alert', 'alert-info') }}">{{ session()->get('message') }}</p>
@endif
<div class="box box-success collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Add Sound</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.notification_sounds.store') }}" enctype='multipart/form-data'>
        @csrf
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="Enter Sound Title">
                        @if ($errors->has('title'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Sound</label>
                        <input type="file" class="form-control" name="sound" accept="audio/*">
                        @if ($errors->has('sound'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('sound') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-sm btn-success">Add</button>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Notification Sounds</h3>
            </div>
            <div class="box-body">
                <table id="sounds-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Sound</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
{{-- Datatable --}}
<script src="{{asset('assets/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript">
    var dTable = '';
	jQuery(function() {
	    dTable = jQuery('#sounds-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{ route('admin.notification_sounds.data') }}',
	        columns: [
	            { data: 'id', name: 'id' },
	            { data: 'sound', name: 'sound' },
	            { data: 'title', name: 'title' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, sClass : 'text-center' }
	        ]
        });
	});
</script>
@endpush
