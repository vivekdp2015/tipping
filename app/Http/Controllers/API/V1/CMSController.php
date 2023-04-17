<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CMS;
use App\SiteSetting;

class CMSController extends Controller
{
    /**
     * This will fetch page
     */
    public function content(string $page)
    {
        return $this->__contentFetch($page);
    }

    /**
     * This will fetch social media accounts.
     */
    public function socialMedia()
    {
        return SiteSetting::first()->data['social_media'];
    }

    private function __contentFetch(string $name)
    {
        if (CMS::where('slug', $name)->exists()) {
            $content = CMS::where([
                'slug' => $name,
                'status' => 1
            ])->first()->content;

            $response = [
                'content' => $content,
                'status' => 200,
            ];
        } else {
            $response = [
                'errors' => $name." doesn't exists",
                'status' => 422,
            ];
        }

        return response()->json($response, $response['status']);
    }

    public function downloadFile()
    {
        $settings = SiteSetting::first();

        $response = [
            'path' => env('APP_URL').(str_replace('public', '/storage', $settings->data['content']['market_place_template'])),
            'status' => 200
        ];

        return response()->json($response, $response['status']);
    }
}
