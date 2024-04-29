<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //
    public function search(Request $request)
    {
        $term = $request->input('term');


        $products = Product::where('title', 'like', "%$term%")
            ->orWhere('body', 'like', "%$term%")
            ->orWhere('price', 'like', "%$term%")
            ->orWhereHas('category', function ($query) use ($term) {
                $query->where('categoryName', 'like', "%$term%");
            })
            ->paginate(5);

        return view('homepage-feed', compact('products'));
    }


        // Check if validation fails
        public function update(Product $product, Request $request)
    {
        // Validate incoming fields
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'body' => 'string',
            'price' => 'numeric|min:0',
            'status' => 'in:Available,Unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoryId' => 'exists:categories,id',
            'available_quantity' => 'integer|min:0',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // Update the product fields
        $product->title = $request->input('title');
        $product->body = $request->input('body');
        $product->price = $request->input('price');
        $product->status = $request->input('status'); // Assign updated status value here
        $product->category_id = $request->input('categoryId');
        $product->available_quantity = $request->input('available_quantity');

        // Check if image is being updated
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the original image
            $imagePath = $request->file('image')->store('images', 'public');

            // Delete the previous images if any
            $product->images()->delete();
            // Create a new image record for the product
            $product->images()->create(['image_path' => $imagePath]);
        }

        // Save the updated product
        $product->save();

        // Redirect with success message
        return back()->with('success', 'Product successfully updated')->setStatusCode(200);
    }



    public function showEditForm(Product $product)
    {
        $categories= Category::all();
        return view('edit-product', ['product' => $product,'categories' => $categories]);
    }

    public function delete(Product $product)
    {
        $product->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Product is deleted')->setStatusCode(200);
    }


    public function viewSingleProduct(Product $product)
    {
        return response()->view('single-product', ['product' => $product], 200);
    }


    public function storeProduct(Request $request)
    {
        // Validate incoming fields
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Available, Unavailable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoryId' => 'required|exists:categories,id',
            'available_quantity' => 'integer|min:0',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // Check if file upload was successful
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the original image
            $imagePath = $request->file('image')->store('images', 'public');

            // Create ImageManager instance

            // Assign other fields
            $incomingFields = [
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
                'user_id' => auth()->id(),
                'category_id' => $request->input('categoryId'),
                'image_path' => $imagePath,
                'available_quantity' => $request->input('available_quantity'),
            ];

            // Create the product
            $product = Product::create($incomingFields);

            // Associate the image with the product
            $product->images()->create(['image_path' => $imagePath]);

            // Redirect with success message
            return redirect('/')->with('success', 'You created product!')->setStatusCode(201);
        } else {
            // File upload failed
            return redirect()->back()->with('failure', 'Failed to upload image.')->setStatusCode(422);
        }
    }





    public function showCreateForm()
    {
        $categories = Category::all();
        return response()->view('create-product', ['categories' => $categories], 200);
    }

    public function storeComment(Request $request, Product $product)
    {
        $request->validate(
            [
                'content' => 'required|string|max:255',
            ]
        );

        $comment = new Comment(
            [
                'content' => $request->input('content'),
            ]
        );

        $comment->product()->associate($product);
        $comment->user()->associate(auth()->user());

        $comment->save();

        return back()->with('success', 'Comment added successfully!')->setStatusCode(200);
    }


}
