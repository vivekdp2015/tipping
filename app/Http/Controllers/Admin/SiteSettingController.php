<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SiteSetting;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{

    /**
     * This will render site settings
     */
    public function index()
    {
        $siteSettings = SiteSetting::first();
        return view('admin.site-settings.index', compact('siteSettings'));
    }

    /**
     * This will update and create settings
     */
    public function updateOrCreate(Request $request)
    {
        $logo = null;

        $settings = SiteSetting::where('slug', 'site_settings')->first();
        if (isset($settings->data['general']['logo']) && !empty($settings->data['general']['logo'])) {
            $logo = $settings['general']['logo'];
        }

        if ($request->has('logo')) {
            $img_name = sha1(time());
            $ext = $request->file('logo')->extension();
            $request->file('logo')->move('uploads', $img_name.'.'.$ext);
            $logo = $img_name.'.'.$ext;
        }

        if ($request->has('marketTemplate')) {
            $file = Storage::put('public', $request->marketTemplate);
        }

        SiteSetting::updateOrCreate([
            'slug' => 'site_settings'
        ],[
            'data' => [
                'general' => [
                    'logo' => $logo
                ],
                'social_media' => [
                    'facebook' => $request->facebook,
                    'twitter' => $request->twitter,
                    'instagram' => $request->instagram,
                ],
                'content' => [
                    'market_place_template' => $file,
                ]
            ]
        ]);

        $this->__sessionMsgs([
            'msg' => 'Settings Updated Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.site_settings.index');
    }

    /**
     * This will set session messages
     */
    private function __sessionMsgs(array $sessionStatus)
    {
        session()->flash('message', $sessionStatus['msg']);
        session()->flash('alert', 'alert-'.$sessionStatus['status']);
    }
}
