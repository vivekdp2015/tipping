<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Feedback;
use DataTables;

class FeedbackController extends Controller
{
    /**
     * This will display all the feedback
     */
    public function index()
    {
        return view('admin.feedbacks.index');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $feedbacks = Feedback::with('user')->get();
        return Datatables::of($feedbacks)
        ->rawColumns(['actions', 'status'])
        ->editColumn('status', function ($feedbacks) {
            return ($feedbacks->status) ? '<span class="text text-success">Read</span>' : '<span class="text text-danger">Unread</span>';
        })
        ->editColumn('first_name', function ($feedbacks) {
            return $feedbacks->user->first_name;
        })
        ->editColumn('last_name', function ($feedbacks) {
            return $feedbacks->user->last_name;
        })
        ->editColumn('email', function ($feedbacks) {
            return $feedbacks->user->email;
        })
        ->editColumn('created_at', function ($feedbacks) {
            return $feedbacks->created_at->toDayDateTimeString();
        })
        ->addColumn('actions', function ($feedbacks) {
            return view('admin.feedbacks.actions', compact('feedbacks'));
        })
        ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  Feedback  $Feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        $feedback = Feedback::with('user')->findOrFail($feedback->id);
        return view('admin.feedbacks.view', compact('feedback'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  id  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            if (Feedback::destroy($id)) {
                return response()->json([
                    'response' => 1,
                    'msg' => 'Feedabck deleted successfully',
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
     * This will change status of feedback
     *
     * @param Feedback $feedback
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Feedback $feedback)
    {
        try {
            Feedback::findOrFail($feedback->id)->update([
                'status' => ($feedback->status) ? 0 : 1,
            ]);

            return response()->json([
                'response' => 1,
                'msg' => 'Feedabck Updated successfully',
            ]);
        } catch(\Exception $th) {
            return response()->json([
                'response' => 2,
                'msg' => $th->getMessage(),
            ]);
        }
    }
}
