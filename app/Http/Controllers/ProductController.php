<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    //
    public function search(Request $request)
    {
        $term = $request->input('term');


        $products= Product::where('title', 'like', "%$term%")
            ->orWhere('body', 'like', "%$term%")
            ->orWhere('price', 'like', "%$term%")
            ->orWhereHas('location', function ($query) use ($term) {
                $query->where('name', 'like', "%$term%");
            })
            ->orWhereHas('category', function ($query) use ($term) {
                $query->where('categoryName', 'like', "%$term%");
            })
            ->paginate(5); // 5 products per page
            return view('homepage-feed', compact('products'));
    }

    public function update(Product $product, Request $request)
    {
        // Validate incoming fields
        $incomingFields = $request->validate([
            'title' => 'string|max:255',
            'body' => 'string',
            'price' => 'numeric|min:0',
            'status' => 'in:New,Used',
            'phonenumber' => 'phone_number',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoryId' => 'exists:categories,id',
            'locationId' => 'exists:locations,id',
        ]);

        // Check if image is being updated
        if ($request->hasFile('image')) {
            // Store the original image
            $newImage = $request->file('image')->store('images', 'public');

            // Resize the image
            $image = Image::make(public_path("storage/{$newImage}"))->fit(300, 300);
            $image->save();

            // Delete the previous image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Update the product with the resized image
            $incomingFields['image_path'] = $newImage;
        }

        // Assign other fields
        $incomingFields['location_id'] = $request->input('locationId');
        $incomingFields['category_id'] = $request->input('categoryId');

        // Update the product
        $product->update($incomingFields);

        // Redirect with success message
        return back()->with('success', 'Post successfully updated');
    }
    public function showEditForm(Product $product)
    {
        $locations = Location::all();
        $categories= Category::all();
        return view('edit-product', ['product' => $product,'categories' => $categories, 'locations' => $locations]);
    }

    public function delete(Product $product)
    {
        $product->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success','Product is deleted');
    }

    public function viewSingleProduct(Product $product)
    {

        return view('single-product',['product' => $product]);
    }



    public function storeProduct(Request $request)
    {
        // Validate incoming fields
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:New,Used',
            'phonenumber' => 'required|phone_number',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoryId' => 'required|exists:categories,id',
            'locationId' => 'required|exists:locations,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if file upload was successful
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the original image
            $imagePath = $request->file('image')->store('images', 'public');

            // Create ImageManager instance
            $manager = ImageManager::gd();
            // Open and manipulate the image
            $image = $manager->read(public_path("storage/{$imagePath}"));

            // Resize the image
            $image->resize(128, 128);

            // Save the resized image
            $image->save();

            // Assign other fields
            $incomingFields = [
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
                'phonenumber' => $request->input('phonenumber'),
                'user_id' => auth()->id(),
                'location_id' => $request->input('locationId'),
                'category_id' => $request->input('categoryId'),
                'image_path' => $imagePath,
            ];

            // Create the product
            Product::create($incomingFields);

            // Redirect with success message
            return redirect('/')->with('success', 'You created product!');
        } else {
            // File upload failed
            return redirect()->back()->with('failure', 'Failed to upload image.');
        }
    }





    public function showCreateForm()
    {

        $locations = Location::all();
        $categories= Category::all();
        return view('create-product', ['categories' => $categories, 'locations' => $locations]);
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

        return back()->with('success', 'Comment added successfully!');
    }


}
