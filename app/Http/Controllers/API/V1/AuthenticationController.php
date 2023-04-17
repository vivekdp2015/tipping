<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use DB;
use JWTAuth;
use App\Traits\StripeTrait;
use App\NotificationType;

class AuthenticationController extends Controller
{
    use StripeTrait;

    /**
     * This is for social login
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'email' => ['required', 'string', 'email:filter', 'max:255'],
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'socialId' => ['required', 'string'],
            'loginType' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                if (User::where('email', $request->email)->exists()) {
                    $user = User::where('email', $request->email)->first();
                } else {
                    $stripeCustomer = null;
                    if ($request->type === 'Tipper') {
                        $stripeCustomer = $this->createCustomer([
                            'email' => $request->email,
                            'name' => $request->first_name.' '.$request->last_name
                        ])->id;
                    }

                    $user = User::create([
                        'first_name' => $request->firstName,
                        'last_name' => $request->lastName,
                        'email' => $request->email,
                        'type' => $request->type,
                        'social_id' => $request->socialId,
                        'login_type' => $request->loginType,
                        'code' => 'TJ'.rand(10000, 99999),
                        'stripe_cust_id' => $stripeCustomer,
                        'notification_type_id' => NotificationType::first()->id
                    ]);
                }

                $token = JWTAuth::fromUser($user);
                $response = $this->respondWithToken($token, $user);

                Log::create([
                    'user_id' => $user->id
                ]);
            } catch(\Throwable $th) {
                $response = [
                    'msg' => $th->getMessage(),
                    'status' => 406
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will check authentication.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'email' => ['required', 'string', 'email:filter', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $credentials = $request->only('email', 'password');
            if ($token = JWTAuth::attempt($credentials)) {
                if (auth()->attempt($credentials)) {
                    $user = auth()->user();
                }

                $response = $this->respondWithToken($token, $user);

                Log::create([
                    'user_id' => $user->id
                ]);
            } else {
                $response = [
                    'msg' => 'These credentials do not match our records',
                    'status' => 422
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will register user.
     *
     * @param Illuminate\Http\Request $request
     * @return array $response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'type' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                $stripeCustomer = null;
                if ($request->type === 'Tipper') {
                    $stripeCustomer = $this->createCustomer([
                        'email' => $request->email,
                        'name' => $request->firstName.' '.$request->lastName
                    ])->id;
                }

                $user = User::create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'type' => $request->type,
                    'code' => 'TJ'.rand(10000, 99999),
                    'stripe_cust_id' => $stripeCustomer,
                    'notification_type_id' => NotificationType::first()->id
                ]);

                $token = JWTAuth::fromUser($user);
                $response = $this->respondWithToken($token, $user);

                Log::create([
                    'user_id' => $user->id
                ]);
            } catch(\Throwable $th) {
                $response = [
                    'msg' => $th->getMessage(),
                    'status' => 406
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will change password of user
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->post(), [
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8'],
            'confirmPassword' => ['required', 'string', 'min:8'],
        ], [
            'newPassword.min' => 'The New Password should be at least 8 characters in length',
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422
            ];
        } else {
            if ($user->id === auth()->user()->id) {
                if ($request->newPassword === $request->confirmPassword) {
                    if (User::findOrFail($user->id)->exists()) {
                        if ($request->newPassword !== $request->currentPassword) {
                            if (Hash::check($request->currentPassword, User::where('id', $user->id)->first()->password)) {
                                $user = User::where('id', $user->id)->update([
                                    'password' => Hash::make($request->newPassword)
                                ]);

                                $response = [
                                    'msg' => 'Password changed succesfully',
                                    'status' => 200
                                ];
                            } else {
                                $response = [
                                    'msg' => 'The Current Password doesn\'t match our records. Please try again',
                                    'status' => 422
                                ];
                            }
                        } else {
                            $response = [
                                'msg' => 'The New Password cannot be the same as the old password.',
                                'status' => 422
                            ];
                        }
                    } else {
                        $response = [
                            'msg' => 'We can\'t find a user with that e-mail address.',
                            'status' => 422
                        ];
                    }
                } else {
                    $response = [
                        'msg' => 'The Confirm Password doesn\'t match',
                        'status' => 422
                    ];
                }
            } else {
                $response = [
                    'msg' => 'You are unauthorized',
                    'status' => 422
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will send email for forgot password
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function requestForgotPassword(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'email' => ['required', 'string', 'email:filter', 'max:255'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422
            ];
        } else {
            if (User::where('email', $request->email)->exists()) {
                if (DB::table('password_resets')->where('email', $request->email)->exists()) {
                    $token = DB::table('password_resets')->where('email', $request->email)->first()->token;
                }

                if (empty($token)) {
                    $token = str_random(60);
                    DB::table('password_resets')->insert([
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);
                }

                Mail::to($request->email)->send(new ForgotPassword($token, $request->email));

                $response = [
                    'msg' => 'Successful. We have sent a link to reset the password to your registered email address',
                    'status' => 200
                ];
            } else {
                $response = [
                    'msg' => 'Sorry, we can\'t find an account associated with this email address. Please try again.',
                    'status' => 422
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will response for forgot password
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function responseForgotPassword(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'max:255'],
            'newPassword' => ['required', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422
            ];
        } else {
            if (DB::table('password_resets')->where(['token' => $request->token, 'email' => $request->email])->exists()) {
                $user = User::where('email', $request->email)->update([
                    'password' => Hash::make($request->newPassword)
                ]);

                DB::table('password_resets')->where('email', $request->email)->delete();

                $response = [
                    'status' => 200,
                    'msg' => 'Password reset successfully'
                ];
            } else {
                $response = [
                    'status' => 422,
                    'msg' => 'This password reset token is invalid.'
                ];
            }
        }
        return response()->json($response, $response['status']);
    }

    /**
     * This will logout a user from system.
     *
     * @param void
     * @return array
     */
    public function logout()
    {
        auth()->logout();
    }

    /**
     * This will generate jwt token
     *
     * @param string $token
     * @return array
     */
    protected function respondWithToken($token, $user)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'id' => $user->id,
            'type' => $user->type,
            'code' => $user->code,
            'stripeConnected' => (($user->type === 'Tippee') && (empty($user->stripe_acc_id))) ? false : true,
            'isEmptyUserData' => (empty($user->profile_img) || empty($user->about)) ? true : false,
            'status' => 200
        ];
    }
}
