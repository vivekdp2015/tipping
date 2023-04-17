<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NotificationType;
use App\NotificationSound;
use DataTables;
use File;

class NotificationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.notification-types.index');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $types = NotificationType::with('sound')->get();

        return Datatables::of($types)
        ->rawColumns(['actions', 'img'])
        ->addColumn('actions', function ($types) {
            return view('admin.notification-types.actions', compact('types'));
        })
        ->addColumn('sound', function ($types) {
            return $types->sound->title;
        })
        ->addColumn('img', function ($types) {
            return '<img height="70" width="70" src="'.$types->img.'" />';
        })
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sounds = NotificationSound::get();

        return view('admin.notification-types.create', compact('sounds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'notificationImg' => 'required|max:2000',
        ]);

        $img_name = sha1(time());
        $ext = $request->file('notificationImg')->extension();
        $request->file('notificationImg')->move('uploads/notifications', $img_name.'.'.$ext);
        $notificationImg = $img_name.'.'.$ext;

        $type = NotificationType::create([
            'title' => $request->title,
            'img' => $notificationImg,
            'notification_sounds_id' => $request->sound
        ]);

        $this->__sessionMsgs([
            'msg' => 'Type Added Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.notification-types.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $types = NotificationType::findOrFail($id);
        $sounds = NotificationSound::get();

        return view('admin.notification-types.edit', compact('types', 'sounds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'notificationImg' => 'nullable|max:2000',
        ]);

        $type = NotificationType::findOrFail($id);

        if ($request->has('notificationImg')) {
            $img_name = sha1(time());
            $ext = $request->file('notificationImg')->extension();
            $request->file('notificationImg')->move('uploads/notifications', $img_name.'.'.$ext);
            $notificationImg = $img_name.'.'.$ext;

            if ($type->img) {
                File::delete('uploads/profile-images/'.$type->img);
            }
        } else {
            $notificationImg = $type->img;
        }

        $UpdateType = NotificationType::findOrFail($id)->update([
            'title' => $request->title,
            'img' => $notificationImg,
            'notification_sounds_id' => $request->sound
        ]);

        $this->__sessionMsgs([
            'msg' => 'Notification Type Updated Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.notification-types.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (NotificationType::destroy($id)) {
                return response()->json([
                    'response' => 1,
                    'msg' => 'Type Deleted Successfully',
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
