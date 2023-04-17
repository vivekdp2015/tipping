@extends('layouts.admin.app')

@section('title', 'Content Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-body box-profile">
                <h3 class="text-center">{{$cms->title}}</h3>
                <p class="text-muted text-center"></p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <a class="word-break">{{ $cms->content }}</a>
                    </li>
                </ul>
                <center><a href="{{ url('/admin/cms') }}" class="btn btn-sm bg-orange word-break"> <i class="fa fa-long-arrow-left"></i> Back</a></center>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
@endsection
