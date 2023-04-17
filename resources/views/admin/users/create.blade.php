@extends('layouts.admin.app')

@section('title', 'User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Create User</h3>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" enctype='multipart/form-data' onsubmit="return userValidation()">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName" class="required">First Name</label>
                                <input type="text" name="firstName" class="form-control firstName" placeholder="Enter First Name">
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
                                <input type="text" name="lastName" class="form-control lastName" placeholder="Enter Last Name">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="password" class="required">Password</label>
                                <input type="password" name="password" class="password form-control" placeholder="Enter Password">
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                <p class="password_err error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailAdd" class="required">Email address</label>
                                <input type="email" name="email" class="email form-control" placeholder="Enter Email Address">
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
                                    <option value="Tipper">Tipper</option>
                                    <option value="Tippi">Tippie</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>About User</label>
                                <textarea class="form-control" rows="3" name="about" placeholder="Enter something about user"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-xs-3">
                                <img src="{{ asset('assets/images/default-user.png') }}" class="img-responsive" id="default-user-img">
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
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Create</button>
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
