@extends('layouts.admin.app')

@section('title', 'Notification Type')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Create Notification Type</h3>
            </div>
            <form method="POST" action="{{ route('admin.notification-types.store') }}" enctype='multipart/form-data'>
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="required">Title</label>
                                <input type="text" name="title" class="form-control title" placeholder="Enter Title" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-xs-3">
                                <img src="{{ asset('assets/images/demo_img.png') }}" class="img-responsive" id="default-user-img">
                            </div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="notificationImg" id="profile_image" onchange="imgChange(this);">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Sounds</label>
                                <select name="sound" class="form-control">
                                    @foreach ($sounds as $sound)
                                        <option value="{{ $sound->id }}">{{ $sound->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Create</button>
                    <a href="{{ url('/admin/notification-types') }}" class="btn btn-sm btn-danger"> <i class="fa fa-long-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
	function imgChange(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#default-user-img')
					.attr('src', e.target.result)
					.width(200)
					.height(100);
			};
			reader.readAsDataURL(input.files[0]);
		}
    }
</script>
@endpush
