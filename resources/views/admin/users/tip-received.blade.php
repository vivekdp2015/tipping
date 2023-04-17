@extends('layouts.admin.app')

@section('title', 'Tip Received')

@push('style')
{{-- Datatable --}}
<link rel="stylesheet" href="{{asset('assets/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
{{-- DateRangePicker --}}
<link rel="stylesheet" href="{{asset('assets/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Filter</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="pull-right">
            <div class="form-group">
                <label>Date range:</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control" id="date-range" name="range">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Tip Received</h3>
            </div>
            <div class="box-body">
                <table id="tip-received-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Tipper</th>
                            <th>Date</th>
                            <th>Amount</th>
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
{{-- DateRangePicker --}}
<script src="{{asset('assets/moment/min/moment.min.js')}}"></script>
<script src="{{asset('assets/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

<script type="text/javascript">
    var dTable = '';
	jQuery(function() {
	    dTable = jQuery('#tip-received-table').DataTable({
	        processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.users.tip_received_data', $user->id) }}',
                type: "POST",
                data: function (d) {
                    d.range = jQuery('input[name=range]').val();
                }
            },
	        columns: [
	            { data: 'id', name: 'id' },
	            { data: 'tippee', name: 'tippee' },
	            { data: 'created_at', name: 'created_at' },
	            { data: 'amount', name: 'amount' },
	        ]
        });
    });

    jQuery('#date-range').daterangepicker();
    jQuery('#date-range').val('');
    jQuery('#date-range').attr('placeholder', 'Choose Dates');

    jQuery(document).on('click', '.applyBtn', function () {
        loadDataTable();
    });

    jQuery(document).on('click', '.cancelBtn', function () {
        jQuery('#date-range').val('');
        jQuery('#date-range').attr('placeholder', 'Choose Dates');
        loadDataTable();
    });
</script>
@endpush
