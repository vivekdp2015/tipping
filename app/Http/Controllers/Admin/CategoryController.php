<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;
use App\Category;

class CategoryController extends Controller
{
    /**
     * This will render create category view.
     *
     * @param void
     * @return view
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * This will store categories data.
     *
     * @param Illuminate\Http\Request $request
     * @return response array
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:categories|string|max:255',
        ]);

        $category = Category::create([
            'title' => $request->title,
        ]);

        $this->__sessionMsgs([
            'msg' => 'Category Created Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Data methods display all the records through datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() :  \Illuminate\Http\JsonResponse
    {
        $categories = Category::query();
        return Datatables::of($categories)
        ->rawColumns(['actions'])
        ->addColumn('actions', function ($categories) {
            return view('admin.categories.actions', compact('categories'));
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
        return view('admin.categories.index');
    }

    /**
     * This will delete category
     *
     * @param Category $category
     * @return response array
     */
    public function delete(Category $category)
    {
        try {
            $category->users()->detach($category->id);
            if (Category::destroy($category->id)) {
                return response()->json([
                    'response' => 1,
                    'msg' => 'Category deleted successfully',
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
     * This will render Category details.
     *
     * @param Category $category
     * @return view
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * This will update category data
     *
     * @param Illuminate\Http\Request $request
     * @param Category $category
     *
     * @return reponse array
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255|'.($request->title === $category->title) ? '' : 'unique:categories',
        ]);

        $categoryUpdate = Category::where('id', $category->id)->update([
            'title' => $request->title,
        ]);

        $this->__sessionMsgs([
            'msg' => 'Category Updated Successfully',
            'status' => 'success'
        ]);

        return redirect()->route('admin.categories.index');
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
