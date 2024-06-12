<x-layout>
    <div class="container py-md-12">
        <div class="col-lg-12 pl-lg-5 pb-3 py-lg-5">
            <form action="{{ route('update-category', ['category' => $category->id]) }}" method="POST" id="category-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="category-name" class="text-muted mb-1"><small>Category Name</small></label>
                    <input value="{{ old('categoryName', $category->categoryName) }}" name="categoryName" id="category-name" class="form-control form-control-lg form-control-title" type="text" placeholder="Category Name" autocomplete="off" />
                    @error('categoryName')
                    <p class="m-0 small alert alert-danger shadow-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row justify-content-center">
                    <button type="submit" class="mt-4 btn btn-lg btn-light-magenta">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
