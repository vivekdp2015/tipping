@extends('layouts.admin.app')

@section('title', 'User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Edit User</h3>
            </div>
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype='multipart/form-data' onsubmit="return userValidation()">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName" class="required">First Name</label>
                                <input type="text" name="firstName" class="form-control firstName" placeholder="Enter First Name" value="{{ $user->first_name }}">
                                @if ($errors->has('firstName'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('firstName') }}</strong>
                                    </span>
                                @endif
                                <p class="first_name_err error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastName" class="required">Last Name</label>
                                <input type="text" name="lastName" class="form-control lastName" placeholder="Enter Last Name" value="{{ $user->last_name }}">
                                @if ($errors->has('lastName'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('lastName') }}</strong>
                                    </span>
                                @endif
                                <p class="last_name_err error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailAdd" class="required">Email address</label>
                                <input type="email" name="email" class="email form-control" placeholder="Enter Email Address" value="{{ $user->email }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                                <p class="email_err error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Select User Type</label>
                                <select class="form-control" name="type">
                                    <option value="Tipper" {{ ($user->type === 'Tipper') ? 'selected' : '' }}>Tipper</option>
                                    <option value="Tippee" {{ ($user->type === 'Tippee') ? 'selected' : '' }}>Tippee</option>
                                    <option value="admin" {{ ($user->type === 'admin') ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>About User</label>
                                <textarea name="about" class="form-control" rows="3" placeholder="Enter something about user">{{ $user->about }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-xs-3">
                                <img src="{{ (!empty($user->profile_img)) ? asset('uploads/profile-images/'.$user->profile_img) : asset('assets/images/default-user.png') }}" class="img-responsive" id="default-user-img" height="90" width="90">
                            </div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <label>Profile Image</label>
                                    <input type="file" name="profileImg" id="profile_image" onchange="imgChange(this);">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ ($user->status) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (!$user->status) ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Update</button>
                    <a href="{{ url('/admin/users') }}" class="btn btn-sm btn-danger"> <i class="fa fa-long-arrow-left"></i> Cancel</a>
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
