@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
<div class="row dashboard-widgets">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('admin.users.index') }}">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Users</span>
                    <span class="info-box-number">{{ $widgets['users'] }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('admin.feedbacks.index') }}">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-address-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Feedbacks</span>
                    <span class="info-box-number">{{ $widgets['feedbacks'] }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('admin.notification-types.index') }}">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fa fa-bell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Notification Types</span>
                    <span class="info-box-number">{{ $widgets['notificationTypes'] }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('admin.notification_sounds.index') }}">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-music"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Notification Sounds</span>
                    <span class="info-box-number">{{ $widgets['notificationSounds'] }}</span>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
