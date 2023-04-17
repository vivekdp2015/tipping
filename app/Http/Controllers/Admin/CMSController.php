<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CMS;
use DataTables;

class CMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.cms.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cms.create');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $cms = CMS::query();
        return Datatables::of($cms)
        ->rawColumns(['actions'])
        ->editColumn('status', function ($cms) {
            return ($cms->status == 1) ? 'Active' : "Inactive";
        })
        ->addColumn('actions', function ($cms) {
            return view('admin.cms.actions', compact('cms'));
        })
        ->make(true);
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
            'content' => 'required'
        ]);

        $cms = CMS::create([
            'title' => $request->title,
            'slug' => str_slug($request->title, '-'),
            'content' => $request->content,
            'status' => $request->status
        ]);

        $this->__sessionMsgs([
            'msg' => 'CMS Page Created Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.cms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cms = CMS::findOrFail($id);
        return view('admin.cms.view', compact('cms'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cms = CMS::findOrFail($id);
        return view('admin.cms.edit', compact('cms'));
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
            'content' => 'required'
        ]);

        $cms = CMS::findOrFail($id)->update([
            'title' => $request->title,
            'slug' => str_slug($request->title, '-'),
            'content' => $request->content,
            'status' => $request->status
        ]);

        $this->__sessionMsgs([
            'msg' => 'CMS Page Updated Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.cms.index');
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
            if (CMS::destroy($id)) {
                return response()->json([
                    'response' => 1,
                    'msg' => 'Page deleted successfully',
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
