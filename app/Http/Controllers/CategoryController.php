<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function delete(Category $category)
    {
        $category->delete();
        return redirect('/categories')->with('success', 'Category is deleted');
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
}
