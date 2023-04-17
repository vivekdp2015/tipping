<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NotificationSound;
use DataTables;

class NotificationSoundController extends Controller
{
    /**
     * This will display all the sounds
     */
    public function index()
    {
        return view('admin.notification-sounds.index');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $sounds = NotificationSound::query();
        return Datatables::of($sounds)
        ->rawColumns(['actions', 'sound'])
        ->addColumn('actions', function ($sounds) {
            return view('admin.notification-sounds.actions', compact('sounds'));
        })
        ->editColumn('sound', function ($sounds) {
            return '<audio controls><source src="'.$sounds->sound.'" type="audio/mpeg"></audio>';
        })
        ->make(true);
    }

     /**
     * This will store sound
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'sound' => 'required|max:2000',
        ]);

        $sound = sha1(time());
        $ext = $request->file('sound')->extension();
        $request->file('sound')->move('uploads/sounds', $sound.'.'.$ext);
        $notificationSound = $sound.'.'.$ext;

        $storeSound = NotificationSound::create([
            'title' => $request->title,
            'sound' => $notificationSound
        ]);

        $this->__sessionMsgs([
            'msg' => 'Sound Created Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.notification_sounds.index');
    }

    /**
     * This will delete sound
     */
    public function delete($id)
    {
        try {
            if (NotificationSound::destroy($id)) {
                return response()->json([
                    'response' => 1,
                    'msg' => 'Sound Deleted Successfully',
                ]);
            }
        } catch(\Exception $th) {
            return response()->json([
                'response' => 2,
                'msg' => $th->getMessage(),
            ]);
        }
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
