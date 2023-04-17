@extends('layouts.admin.app')

@section('title', 'User')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="box box-success">
            <div class="box-body box-profile">
                <h3 class="profile-username text-center">{{$user->first_name.' '.$user->last_name}}</h3>
                <p class="text-muted text-center"></p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>First Name</b> <a class="pull-right">{{ $user->first_name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Last Name</b> <a class="pull-right">{{ $user->first_name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Email Id</b> <a class="pull-right">{{ $user->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Type</b> <a class="pull-right">{{ ucfirst($user->type) }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="pull-right"> {{ ($user->status) ? 'Active' : 'Inactive' }} </a>
                    </li>
                </ul>
                <center><a href="{{ url('/admin/users') }}" class="btn btn-sm bg-orange"> <i class="fa fa-long-arrow-left"></i> Back</a></center>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
@endsection
