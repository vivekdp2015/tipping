<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use DataTables;
use File;
use App\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use DB;

class UserController extends Controller
{
    /**
     * This will render create user view.
     *
     * @param void
     * @return view
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * This will store users data.
     *
     * @param Illuminate\Http\Request $request
     * @return response array
     */
    public function store(Request $request)
    {
        $profileImg = null;

        $this->validate($request, [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email:filter|unique:users|max:255',
            'type' => 'required|string|max:255',
            'password' => 'required|min:8',
        ]);

        if ($request->has('profileImg')) {
            $img_name = sha1(time());
            $ext = $request->file('profileImg')->extension();
            $request->file('profileImg')->move('uploads/profile-images', $img_name.'.'.$ext);
            $profileImg = $img_name.'.'.$ext;
        }

        $user = User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'type' => $request->type,
            'about' => $request->about,
            'profile_img' => $profileImg,
            'status' => $request->status
        ]);

        $this->__sessionMsgs([
            'msg' => 'User Created Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.users.index');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $users = User::query();
        return Datatables::of($users)
        ->rawColumns(['actions'])
        ->editColumn('status', function ($users) {
            return ($users->status == 1) ? 'Active' : "Inactive";
        })
        ->editColumn('type', function ($users) {
            return ucfirst($users->type);
        })
        ->addColumn('actions', function ($users) {
            return view('admin.users.actions', compact('users'));
        })
        ->make(true);
    }

    /**
     * This will render manage view.
     *
     * @param view
     * @return array
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * This will fetch user.
     *
     * @param User $user
     * @return User $user
     */
    public function view(User $user)
    {
        return view('admin.users.view', compact('user'));
    }

    /**
     * This will delete user
     *
     * @param User $user
     * @return response array
     */
    public function delete(User $user)
    {
        try {
            if ($user->id === auth()->user()->id) {
                throw new \Exception('You cannot delete login user');
            } else {
                if (User::destroy($user->id)) {
                    return response()->json([
                        'response' => 1,
                        'msg' => 'User deleted successfully',
                    ]);
                }
            }
        } catch(\Exception $th) {
            return response()->json([
                'response' => 2,
                'msg' => $th->getMessage(),
            ]);
        }
    }

    /**
     * This will render user details.
     *
     * @param User $user
     * @return view
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * This will update user data
     *
     * @param Illuminate\Http\Request $request
     * @param User $user
     *
     * @return reponse array
     */
    public function update(Request $request, User $user)
    {
        $profileImg = $user->profile_img;

        $this->validate($request, [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => ($user->email === $request->get('email')) ? 'required|email:filter|max:255' : 'required|email:filter|unique:users|max:255',
            'type' => 'required|string|max:255',
        ]);

        if ($request->has('profileImg')) {
            $img_name = sha1(time());
            $ext = $request->file('profileImg')->extension();
            $request->file('profileImg')->move('uploads/profile-images', $img_name.'.'.$ext);
            $profileImg = $img_name.'.'.$ext;

            if ($user->profile_img) {
                File::delete('uploads/profile-images/'.$user->profile_img);
            }
        }

        $userUpdate = User::where('id', $user->id)->update([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'about' => $request->about,
            'type' => $request->type,
            'profile_img' => $profileImg,
            'status' => $request->status
        ]);

        $this->__sessionMsgs([
            'msg' => 'User Updated Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.users.index');
    }

    /**
     * This will send forgot password mail to user.
     */
    public function sendForgotPasswordLink(User $user)
    {
        try {
            $credentials = ['email' => $user->email];
            $response = Password::sendResetLink($credentials, function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            return response()->json([
                'response' => 1,
                'msg' => 'Reset Link Send Successfully',
            ]);
        } catch(\Exception $th) {
            return response()->json([
                'response' => 2,
                'msg' => $th->getMessage(),
            ]);
        }
    }

    /**
     * This will display tips sent of tipper.
     */
    public function tipSent(User $user)
    {
        return view('admin.users.tip-sent', compact('user'));
    }

    /**
     * This will fetch tipps sent data
     */
    public function tipSentData(User $user, Request $request)
    {
        $tipSent = Transaction::with('oneTippe')->where('tipper_id', $user->id);

        if ($request->filled('range')) {
            $range = explode('-', str_replace(' ', '', $request->get('range')));
            $min = Carbon::parse($range[0]);
            $max = Carbon::parse($range[1]);
            $tipSent->where(DB::raw('DATE(created_at)'), '>=', $min->toDateString())
                    ->where(DB::raw('DATE(created_at)'), '<=', $max->toDateString());
        }
        return Datatables::of($tipSent)
        ->editColumn('tippee', function ($tipSent) {
            return $tipSent->oneTippe->first_name.' '.$tipSent->oneTippe->last_name;
        })
        ->editColumn('created_at', function ($tipSent) {
            return $tipSent->created_at->toDayDateTimeString();
        })
        ->make(true);
    }

    /**
     * This will display tip received of tippe.
     */
    public function tipReceived(User $user)
    {
        return view('admin.users.tip-received', compact('user'));
    }

    /**
     * This will fetch tipps received data
     */
    public function tipReceivedData(User $user, Request $request)
    {
        $tipReceived = Transaction::with('oneTipper')->where('tippe_id', $user->id);

        if ($request->filled('range')) {
            $range = explode('-', str_replace(' ', '', $request->get('range')));
            $min = Carbon::parse($range[0]);
            $max = Carbon::parse($range[1]);
            $tipReceived->where(DB::raw('DATE(created_at)'), '>=', $min->toDateString())
                        ->where(DB::raw('DATE(created_at)'), '<=', $max->toDateString());
        }
        return Datatables::of($tipReceived)
        ->editColumn('tippee', function ($tipReceived) {
            return $tipReceived->oneTipper->first_name.' '.$tipReceived->oneTipper->last_name;
        })
        ->editColumn('created_at', function ($tipReceived) {
            return $tipReceived->created_at->toDayDateTimeString();
        })
        ->make(true);
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
