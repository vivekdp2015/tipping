<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\Feedback as FeedbackMail;
use App\Feedback;

class FeedbackController extends Controller
{
    /**
     * This will store and mail feedback
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            Feedback::create([
                'user_id' => auth()->user()->id,
                'message' => $request->message,
            ]);

            Mail::to(env("CONTACT_EMAIL", 'vivek@adrixus.com'))->send(new FeedbackMail([
                'first_name' => auth()->user()->first_name,
                'last_name' => auth()->user()->last_name,
                'email' => auth()->user()->email,
                'message' => $request->message,
            ]));

            $response = [
                'msg' => 'Thank you for your feedback! You\'re awesome.',
                'status' => 200,
            ];
        }

        return response()->json($response, $response['status']);
    }
}
