@extends('layouts.admin.app')

@section('title', 'User')

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
                <h3 class="box-title">Manage Users</h3>
            </div>
            <div class="box-body">
                <table id="users-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
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
	    dTable = jQuery('#users-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{ route('admin.users.data') }}',
	        columns: [
	            { data: 'id', name: 'id' },
	            { data: 'first_name', name: 'first_name' },
	            { data: 'last_name', name: 'last_name' },
	            { data: 'email', name: 'email' },
	            { data: 'type', name: 'type' },
	            { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, sClass : 'text-center' }
	        ]
        });
	});
</script>
@endpush
