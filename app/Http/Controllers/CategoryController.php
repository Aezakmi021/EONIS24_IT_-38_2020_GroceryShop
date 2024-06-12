<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function delete(Category $category)
    {
        $category->delete();
        return redirect(route('categories'))->with('success', 'Category is deleted');
    }

    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'categoryName' => 'required'
        ]);

        $parentId = $request->get('parentId');
        $incomingFields['categoryName'] = strip_tags($incomingFields['categoryName']);
        $incomingFields['parent_id'] = $parentId;

        Category::create($incomingFields);

        return back()->with('success', 'You created category!');
    }

    public function viewPage()
    {
        $categories = Category::all();
        return view('categories', ['categories' => $categories]);
    }

    public function showProducts($categoryId)
    {
        $mainCategory = Category::find($categoryId);

        if (!$mainCategory) {
            abort(404); // Category not found
        }

        $products = $mainCategory->allProducts();
        return view('category-products', compact('mainCategory', 'products'));
    }


    public function update(Category $category, Request $request)
    {
        $incomingFields = $request->validate([
            'categoryName' => [
                'required',
                Rule::unique('categories')->ignore($category->id),
            ],
        ]);

        $incomingFields['categoryName'] = strip_tags($incomingFields['categoryName']);

        // Check if the provided username already exists for another user
        if ($category->categoryName !== $incomingFields['categoryName'] && Category::where('categoryName', $incomingFields['categoryName'])->exists()) {
            return back()->with('failure', 'Category name already exists.')->setStatusCode(409); // Conflict
        }


        try {
            $category->update([
                'categoryName' => $incomingFields['categoryName'],
            ]);

                $category->save();

            return back()->with('success', 'Category details updated successfully.');
        } catch (QueryException $e) {
            // If any other database error occurs, return a generic error message
            return back()->with('failure', 'An error occurred while updating category details.')->setStatusCode(500); // Internal Server Error
        }
    }

    public function viewCategory(Category $category)
    {
        return view('edit-categories', ['category' => $category]); // No need to set status code for views
    }
}
