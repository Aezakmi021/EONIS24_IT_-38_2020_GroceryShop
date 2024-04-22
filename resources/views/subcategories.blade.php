@foreach ($subcategories as $subcategory)
    <li>
        <a href="/categories/{{ $subcategory->id }}">{{ $subcategory->categoryName }}</a>
    </li>
@endforeach
