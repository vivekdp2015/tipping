<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Category;

class CategoryController extends Controller
{
    /**
     * This will create category
     *
     * @param Illuminate\Http\Request $request
     * @return array $response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'title' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $category = Category::create([
                'title' => $request->title
            ]);

            $response = [
                'msg' => 'Category Created successfully.',
                'status' => 200
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will edit category
     *
     * @param Category $category
     * @return Category $category
     */
    public function edit(Category $category)
    {
        return $category;
    }

    /**
     * This will update category
     *
     * @param Category $category
     * @param Illuminate\Http\Request $request
     *
     * @return Category array
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->post(), [
            'title' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 422,
            ];
        } else {
            $category = Category::where('id', $category->id)->update([
                'title' => $request->title,
            ]);

            $response = [
                'msg' => 'Category updated successfully',
                'status' => 200
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * This will fetch all categories.
     *
     * @param void
     * @return array
     */
    public function index()
    {
        return Category::all();
    }
}
