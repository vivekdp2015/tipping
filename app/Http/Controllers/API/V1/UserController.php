<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use File;
use DB;
use App\Traits\StripeTrait;
use App\Transaction;
use App\Notification;
use App\Notifications\TipSendNotification;
use App\Notifications\TipReceivedNotification;

class UserController extends Controller
{
    use StripeTrait;

    /**
     * This will render all details of user
     *
     * @param User $user
     * @return $user
     */
    public function edit(User $user)
    {
        $user = User::with(['categories' => function ($q1){
            $q1->select(['id']);
        }])->where('id', auth()->user()->id)->first();

        return $user;
    }

    /**
     * This will update user
     *
     * @param Illuminate\Http\Request
     * @param User $user
     *
     * @return array $user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->post(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:filter', ($request->email === auth()->user()->email) ? '' : 'unique:users', 'max:255'],
            'profileImg' => [(auth()->user()->type === 'Tippee') ? 'required' : 'nullable'],
            'about' => [(auth()->user()->type === 'Tippee') ? 'required' : 'nullable'],
            'categories' => (auth()->user()->type === 'Tippee') ? ['required'] : ['nullbale']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $profileImg = $user->profile_img;

            if ($request->has('profileImg')) {
                $profileImg = $this->__imgUpload($request->profileImg);

                if ($user->profile_img) {
                    \File::delete(public_path().'/uploads/profile-images/'.$user->profile_img);
                }
            }

            $userUpdate = User::where('id', auth()->user()->id)->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'about' => $request->about,
                'profile_img' => $profileImg
            ]);

            $user->categories()->sync($request->categories);

            $response = [
                'msg' => 'Successfully update user',
                'status' => 200
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will switch user
     *
     * @param User $user
     */
    public function switchUser(User $user)
    {
        $type = ((ucfirst($user->type)) === 'Tipper') ? 'Tippee' : 'Tipper';

        if ($user->id === auth()->user()->id) {
            User::findOrFail($user->id)->update([
                'type' => $type
            ]);
            auth()->user()->type = $type;

            if (($type === 'Tipper') && (empty(auth()->user()->stripe_cust_id))) {
                $stripeCustomer = $this->createCustomer([
                    'email' => auth()->user()->email,
                    'name' => auth()->user()->firstName.' '.auth()->user()->lastName
                ])->id;

                User::findOrFail(auth()->user()->id)->update([
                    'stripe_cust_id' => $stripeCustomer
                ]);

                auth()->user()->stripe_cust_id = $stripeCustomer;
            }

            $response = [
                'msg' => 'You succesfully converted into '.$type,
                'stripeConnected' => (($type === 'Tippee') && (empty(auth()->user()->stripe_acc_id))) ? false : true,
                'isEmptyUserData' => (empty(auth()->user()->profile_img) || empty(auth()->user()->about)) ? true : false,
                'status' => 200
            ];
        } else {
            $response = [
                'msg' => 'You are unauthorized to access details',
                'status' => 422
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will update profile for tippie
     *
     * @param Illuminate\Http\Request
     * @param User $user
     */
    public function updateTippie(Request $request, User $user)
    {
        $validator = Validator::make($request->post(), [
            'profileImg' => [(auth()->user()->type === 'Tippee') ? 'required' : 'nullable'],
            'about' => [(auth()->user()->type === 'Tippee') ? 'required' : 'nullable'],
            'categories' => (auth()->user()->type === 'Tippee') ? ['required'] : ['nullable']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            if ($user->id === auth()->user()->id) {
                $profileImg = $user->profile_img;

                if ($request->has('profileImg')) {
                    $profileImg = $this->__imgUpload($request->profileImg);

                    if ($user->profile_img) {
                        File::delete(public_path().'/uploads/profile-images/'.$user->profile_img);
                    }
                }

                User::findOrFail($user->id)->update([
                    'profile_img' => $profileImg,
                    'about' => $request->about
                ]);

                if (!empty($request->categories)) {
                    $user->categories()->sync($request->categories);
                }

                $response = [
                    'msg' => 'You are all set to start receiving tips',
                    'status' => 200
                ];
            } else {
                $response = [
                    'msg' => 'You are unauthorized to access details',
                    'status' => 422
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will give details of user by qr code
     */
    public function getUserDetailsByQRCode(string $code)
    {
        if (User::where('code', $code)->exists()) {
            return User::where('code', $code)->first();
        } else {
            return response()->json([
                'msg' => 'Sorry Code Does Not Exist. Please Try Again Later',
                'status' => 422
            ]);
        }
    }

    /**
     * This will update user location when mobile app user login or when he/she open app
     */
    public function locationUpdate(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            User::where('id', auth()->user()->id)->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            $response = [
                'msg' => 'Latitude and Longitude successfully updated',
                'status' => 200
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will search user by first name and last name via latitude and longitude
     */
    public function search(Request $request, int $page = 1)
    {
        $validator = Validator::make($request->post(), [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $page = $page-1;

            $users = $this->__calculateUserDistance([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'page' => $page
            ]);

            if ($request->name) {
                $users = $users->where([
                    ['first_name', 'like', '%'.$request->name.'%'],
                    ['stripe_acc_id', '<>', null]
                ])
                ->orWhere('last_name', 'like', '%'.$request->name.'%')
                ->orWhere('code', 'like', '%'.$request->name.'%')
                ->orWhereHas('categories', function ($q1) use ($request) {
                    $q1->where('title', 'like', '%'.$request->name.'%');
                })
                ->paginate(config('constants.paginationLimit.limit'));
            } else {
                $users = $users->where('stripe_acc_id', '<>', null)->paginate(config('constants.paginationLimit.limit'));
            }

            $response = [
                'users' => $users,
                'status' => 200,
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will find the nearest tippies realted to 10 kms distance
     */
    public function nearByTippes(Request $request, int $page = 1)
    {
        $validator = Validator::make($request->post(), [
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $page = $page-1;

            $users = $this->__calculateUserDistance([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'page' => $page
            ])->whereRaw(' ( 6367 * acos( cos( radians('.$request->latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$request->longitude.') ) + sin( radians('.$request->latitude.') ) * sin( radians( latitude ) ) ) ) < '. config('constants.nearByUserDistance.tippeskms'))->paginate(config('constants.paginationLimit.limit'));

            $response = [
                'users' => $users,
                'status' => 200,
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will fetch auth code after tippe login/register
     */
    public function getStripeAuthCode(Request $request)
    {
        if ($request->query('error')) {
            return redirect()->to('tippingjar://status=false&error='.$request->query('error').'&error_description='.$request->query('error_description'));
        } else {
            return redirect()->to('tippingjar://status=true&code='.$request->query('code'));
        }
    }

    /**
     * Connect stripe account
     */
    public function connectStripeAccount(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'code' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $stripeResponse = $this->connectAccount($request->code);

            if ($stripeResponse['status']) {
                $response = [
                    'msg' => 'Successfully connected to stripe',
                    'status' => 200,
                ];
            } else {
                $response = [
                    'error' => $stripeResponse['res']['error'],
                    'msg' => $stripeResponse['res']['error_description'],
                    'status' => 403,
                ];
            }
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will store card
     */
    public function createCard(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'cardNumber' => ['required', 'numeric', 'digits_between:13,16'],
            'expMonth' => ['required', 'numeric', 'digits:2'],
            'expYear' => ['required', 'numeric', 'digits_between:2,4'],
            'cvc' => ['required', 'numeric', 'digits_between:3,4'],
            'default' => ['nullable']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                $this->createStripeCard([
                    'number' => $request->cardNumber,
                    'exp_month' => $request->expMonth,
                    'exp_year' => $request->expYear,
                    'cvc' => $request->cvc,
                    'default' => $request->default,
                    'stripe_cust_id' => auth()->user()->stripe_cust_id
                ]);

                $response = [
                    'msg' => 'Card Added Succsesfully',
                    'status' => 200,
                ];
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
     * This will retreive all the cards
     */
    public function getCards()
    {
        try {
            $fetchCards = $this->cards(auth()->user()->stripe_cust_id);
            $defaultSource = $this->retrieveCustomerDefaultCard(auth()->user()->stripe_cust_id);
            $cards = $this->__pluckCardsDetails($fetchCards, $defaultSource);

            if (is_null($cards)) {
                $response = [
                    'msg' => 'Please add a card first',
                    'cards' => [],
                    'status' => 200
                ];
            } else {
                $response = [
                    'cards' => $cards,
                    'status' => 200
                ];
            }

        } catch(\Throwable $th) {
            $response = [
                'msg' => $th->getMessage(),
                'status' => 406
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will delete the card
     */
    public function deleteCard(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'cardNumber' => ['required', 'numeric', 'digits:4'],
            'expMonth' => ['required', 'numeric', 'digits:2'],
            'expYear' => ['required', 'numeric', 'digits:2']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                $fetchCards = $this->cards(auth()->user()->stripe_cust_id);
                $srcCardId = $this->__fetchSrcCardId($fetchCards, $request);

                if (is_null($srcCardId)) {
                    throw new \Exception('Invalid Card Details');
                }

                $this->deleteStripeCard([
                    'customerId' => auth()->user()->stripe_cust_id,
                    'src' => $srcCardId
                ]);

                $response = [
                    'msg' => 'Card Deleted Successfully',
                    'status' => 200
                ];
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
     * This will update card
     */
    public function updateCard(Request $request, $cardNumber)
    {
        $validator = Validator::make($request->post(), [
            'expMonth' => ['required', 'numeric', 'digits:2'],
            'expYear' => ['required', 'numeric', 'digits_between:2,4'],
            'default' => ['nullable']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                $fetchCards = $this->cards(auth()->user()->stripe_cust_id);
                $srcCardId = $this->__fetchCardDetails($fetchCards, $cardNumber);

                if (is_null($srcCardId)) {
                    throw new \Exception('Invalid Card Details');
                }

                $this->upadteStripeCard([
                    'customerId' => auth()->user()->stripe_cust_id,
                    'src' => $srcCardId['id'],
                    'exp_month' => $request->expMonth,
                    'exp_year' => $request->expYear,
                    'default' => $request->default,
                    // 'cvc' => '999'
                ]);

                $response = [
                    'msg' => 'Card Updated Successfully',
                    'status' => 200
                ];

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
     * This will fetch card for edit
     */
    public function editCard($cardNumber)
    {
        try {
            $fetchCards = $this->cards(auth()->user()->stripe_cust_id);
            $card = $this->__fetchCardDetails($fetchCards, $cardNumber);
            $defaultSource = $this->retrieveCustomerDefaultCard(auth()->user()->stripe_cust_id);

            if (is_null($card)) {
                throw new \Exception('Invalid Card Details');
            }

            $response = [
                'cardNumber' => $card['last4'],
                'expMonth' => $card['exp_month'],
                'expYear' => $card['exp_year'],
                'brand' => $card['brand'],
                'default' => ($card['id'] === $defaultSource) ? true : false,
                'status' => 200
            ];
        } catch(\Throwable $th) {
            $response = [
                'msg' => $th->getMessage(),
                'status' => 406
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will handle transaction
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'tippieCode' => ['required'],
            'amount' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            try {
                $stripeAccUser = User::where('code', $request->tippieCode)->first();

                if (empty(auth()->user()->stripe_cust_id)) {
                    throw new \Exception('Your account is not connected with stripe');
                }

                $fetchCards = $this->cards(auth()->user()->stripe_cust_id);

                if (empty($stripeAccUser->stripe_acc_id)) {
                    throw new \Exception('Tippe\'s Stripe Is Not Connected');
                }

                if (empty($fetchCards['data'])) {
                    $response = [
                        'cardMissing' => true,
                        'msg' => 'Please add card first',
                        'status' => 200
                    ];
                } else {
                    $transaction = $this->createCharge([
                        'src' => $fetchCards['data'][0]['id'],
                        'description' => 'send money to '.$stripeAccUser->first_name.' '.$stripeAccUser->last_name,
                        'customer' => auth()->user()->stripe_cust_id,
                        'tippieAcc' => $stripeAccUser->stripe_acc_id,
                        'amount' => $request->amount
                    ]);

                    $this->__storeTransaction([
                        'tippie_id' => $stripeAccUser->id,
                        'transection_id' => $transaction->id,
                        'amount' => $request->amount,
                    ]);
                    $this->__sendNotifications([
                        'receiver' => $stripeAccUser,
                        'amount' => $request->amount
                    ]);

                    $response = [
                        'cardMissing' => false,
                        'msg' => 'Tip send successfully',
                        'status' => 200
                    ];
                }

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
     * This will store device token
     */
    public function storeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'token' => ['required'],
            'device' => ['required', 'max:255'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $token = User::findOrFail(auth()->user()->id)->update([
                'device_token' => $request->token,
                'device' => $request->device
            ]);

            $response = [
                'msg' => 'Token Updated successfully',
                'status' => 200
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will upload base64 image.
     *
     * @param string $image - base64 format
     * @return string $profileImg
     */
    private function __imgUpload($image)
    {
        $originalImg = $image;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $profileImg = sha1(time()).'.'.explode('/', mime_content_type($originalImg))[1];
        \File::put(public_path().'/uploads/profile-images/'.$profileImg, base64_decode($image));

        return $profileImg;
    }

    /**
     * This will calculate user distance
     *
     * @param array param
     */
    private function __calculateUserDistance(array $param)
    {
        return User::select(DB::raw('*, ( 6367 * acos( cos( radians('.$param['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$param['longitude'].') ) + sin( radians('.$param['latitude'].') ) * sin( radians( latitude ) ) ) ) AS distance'))
        ->where('type', 'Tippee')
        ->orderBy('distance');
    }

    /**
     * This will pluck card details from all cards
     *
     * @param $fetchCards
     * @return array $allCards
     */
    private function __pluckCardsDetails($fetchCards, $defaultSource)
    {
        $allCards = null;

        if (!empty($fetchCards)) {
            foreach ($fetchCards->data as $key => $card) {
                $allCards[] = [
                    'exp_month' => $card['exp_month'],
                    'exp_year' => $card['exp_year']%2000,
                    'last4' => $card['last4'],
                    'brand' => $card['brand'],
                    'country' => $card['country'],
                    'default' => ($card['id'] === $defaultSource) ? true : false,
                ];
            }
        }

        return $allCards;
    }

    /**
     * This will fetch source card id
     */
    private function __fetchSrcCardId($cards, $request)
    {
        $srcCardId = null;

        foreach ($cards['data'] as $key => $card) {
            if (($card['exp_month'] == $request->expMonth) && ($card['exp_year'] == '20'.$request->expYear) && ($card['last4'] == $request->cardNumber)) {
                $srcCardId = $card['id'];
                break;
            }
        }

        return $srcCardId;
    }

    /**
     * This will update card form last 4 digits
     */
    private function __fetchCardDetails($cards, $cardNumber)
    {
        $card = null;

        foreach ($cards as $key => $card) {
            if ($card['last4'] === $cardNumber) {
                $card = $card;
                break;
            }
        }

        return $card;
    }

    /**
     * This will store transections
     */
    private function __storeTransaction($transaction)
    {
        Transaction::create([
            'tipper_id' => auth()->user()->id,
            'tippe_id' => $transaction['tippie_id'],
            'transection_id' => $transaction['transection_id'],
            'amount' => $transaction['amount']
        ]);
    }

    /**
     * This will send notifications to tipper and tippee
     */
    private function __sendNotifications($data)
    {
        $sendNotification = [
            'user_id' => auth()->user()->id,
            'title' => 'Transaction',
            'notification' => 'Thank you for tipping. Your $'.$data['amount'].' tip has been sent to '.$data['receiver']->first_name.' '.$data['receiver']->last_name,
            'user_type' => 'Tipper'
        ];

        $receiveNotification = [
            'user_id' => $data['receiver']->id,
            'title' => 'Payout',
            'notification' => 'You Received $'.$data['amount'].' From '.auth()->user()->first_name.' '.auth()->user()->last_name,
            'user_type' => 'Tippee'
        ];

        auth()->user()->notify(new TipSendNotification([
            'notification' => $sendNotification
        ]));
        $data['receiver']->notify(new TipReceivedNotification([
            'notification' => $receiveNotification
        ]));

        $storeNotifications = Notification::insert([
            $sendNotification, $receiveNotification
        ]);
    }
}
