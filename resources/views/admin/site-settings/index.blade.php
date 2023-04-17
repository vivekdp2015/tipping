@extends('layouts.admin.app')

@section('title', 'Site Settings')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session()->has('message'))
            <p class="alert {{ session()->get('alert', 'alert-info') }}">{{ session()->get('message') }}</p>
        @endif
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Manage Site Settings</h3>
            </div>
            <form method="POST" action="{{ route('admin.site_settings.update') }}" enctype='multipart/form-data'>
                @csrf
                <div class="box-body">
                    <h4>General Settings</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-xs-3">
                                <img src="{{ (!empty($siteSettings->data['general']['logo'])) ? asset('uploads/'.$siteSettings->data['general']['logo']) : asset('assets/images/demo_img.png') }}" class="img-responsive" id="default-img">
                            </div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <label>Logo</label>
                                    <input type="file" name="logo" id="logo" onchange="imgChange(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <h4>Social Media</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="facebook" class="required">Facebook</label>
                        <input value="{{ (isset($siteSettings->data['social_media']['facebook'])) ? $siteSettings->data['social_media']['facebook'] : '' }}" type="text" name="facebook" class="form-control facebook" placeholder="Enter Facebook Profile" required>
                            @if ($errors->has('facebook'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('facebook') }}</strong>
                                </span>
                            @endif
                            <p class="facebook_err error"></p>
                        </div>
                        <div class="col-md-4">
                            <label for="twitter" class="required">Twitter</label>
                            <input value="{{ (isset($siteSettings->data['social_media']['twitter'])) ? $siteSettings->data['social_media']['twitter'] : '' }}" type="text" name="twitter" class="form-control twitter" placeholder="Enter Twitter Profile" required>
                            @if ($errors->has('twitter'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('twitter') }}</strong>
                                </span>
                            @endif
                            <p class="twitter_err error"></p>
                        </div>
                        <div class="col-md-4">
                            <label for="instagram" class="required">Instagram</label>
                            <input value="{{ (isset($siteSettings->data['social_media']['instagram'])) ? $siteSettings->data['social_media']['instagram'] : '' }}" type="text" name="instagram" class="form-control instagram" placeholder="Enter Instagram Profile" required>
                            @if ($errors->has('instagram'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('instagram') }}</strong>
                                </span>
                            @endif
                            <p class="instagram_err error"></p>
                        </div>
                    </div>
                    <hr/>
                    <h4>Content</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="marketTemplate">Marketplace Template</label>
                            <input class="form-control" type="file" name="marketTemplate" accept="application/pdf">
                            @if ($errors->has('marketTemplate'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('marketTemplate') }}</strong>
                                </span>
                            @endif
                            <p class="marketTemplate_err error"></p>
                            <p>{{ (isset($siteSettings->data['content']['market_place_template'])) ? asset($siteSettings->data['content']['market_place_template']) : '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn bg-green btn-sm">Save</button>
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
				$('#default-img')
					.attr('src', e.target.result)
					.width(200)
					.height(100);
			};
			reader.readAsDataURL(input.files[0]);
		}
    }
</script>
@endpush
