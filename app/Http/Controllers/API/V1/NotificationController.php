<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NotificationType;
use App\NotificationSound;
use App\User;
use App\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * This will fetch all types of notifications
     */
    public function types($type = null)
    {
        if (!empty($type)) {
            return NotificationType::with('sound')->where('title', 'like', '%'.$type.'%')->paginate(config('constants.paginationLimit.limit'));
        }

        return NotificationType::with('sound')->paginate(config('constants.paginationLimit.limit'));
    }

    /**
     * This will give you details of single type
     */
    public function details($typeId)
    {
        return NotificationType::with('sound')->findOrFail($typeId);
    }

    /**
     * This will set notification for user
     */
    public function setNotificationType(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'typeId' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $userUpdate = User::findOrFail(auth()->user()->id)->update([
                'notification_type_id' => $request->typeId
            ]);

            $response = [
                'msg' => 'Type added successfully',
                'status' => 200,
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Thsi will fetch all the notifications
     */
    public function index()
    {
        $notifications = Notification::where([
            'user_id' => auth()->user()->id,
            'user_type' =>  auth()->user()->type
        ])->orderby('id', 'desc')->paginate(config('constants.paginationLimit.limit'));

        return [
            'notifications' => $notifications,
            'status' => 200
        ];
    }

    /**
     * This will delete notification
     */
    public function delete($id)
    {
        $delete = Notification::destroy($id);

        if ($delete) {
            return [
                'msg' => 'Notification deleted successfully',
                'status' => 200
            ];
        } else {
            return [
                'msg' => 'There is something problem',
                'status' => 422
            ];
        }
    }

    /**
     * This will give you notificaiton sound and img
     */
    public function soundImg()
    {
        $user = User::with('notificationType.sound')->findOrFail(auth()->user()->id);

        if (empty($user->notificationType)) {
            $sound = NotificationSound::first()->sound;
            $img = NotificationType::first()->img;
        } else {
            $sound = $user->notificationType->sound->sound;
            $img = $user->notificationType->img;
        }

        return [
            'sound' => $sound,
            'img' => $img
        ];
    }
}
