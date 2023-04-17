@extends('layouts.admin.app')

@section('title', 'Feedback')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-body box-profile">
                <h3 class="text-center">Feedback</h3>
                <p class="text-muted text-center"></p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Name</b> <a class="pull-right">{{ $feedback->user->first_name.' '.$feedback->user->last_name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <a class="pull-right">{{ $feedback->user->email }}</a>
                    </li>
                    <li class="list-group-item message">
                        <b class="w-25">Message</b> <a class="w-75">{{ $feedback->message }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="pull-right">{{ ($feedback->status) ? 'Read' : 'Unread' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Sent Date</b> <a class="pull-right">{{ $feedback->created_at->toDayDateTimeString() }}</a>
                    </li>
                </ul>
                <center><a href="{{ url('/admin/feedbacks') }}" class="btn btn-sm bg-orange"> <i class="fa fa-long-arrow-left"></i> Back</a></center>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
@endsection
