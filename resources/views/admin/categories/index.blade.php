@extends('layouts.admin.app')

@section('title', 'Category')

@push('style')
{{-- Datatable --}}
<link rel="stylesheet" href="{{asset('assets/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@endpush

@section('content')

<div class="row">
    <div class="col-md-12">
        @if(session()->has('message'))
            <p class="alert {{ session()->get('alert', 'alert-info') }}">{{ session()->get('message') }}</p>
        @endif
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Manage Categories</h3>
            </div>
            <div class="box-body">
                <table id="categories-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Title</th>
                            <th style="width: 150px">Actions</th>
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
	    dTable = jQuery('#categories-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{ route('admin.categories.data') }}',
	        columns: [
	            { data: 'id', name: 'id' },
	            { data: 'title', name: 'title' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, sClass : 'text-center' }
	        ]
        });
	});
</script>
@endpush
