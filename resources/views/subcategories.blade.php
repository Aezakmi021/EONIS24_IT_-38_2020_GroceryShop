@foreach ($subcategories as $subcategory)
    <li>
        <a class="dm-mono-regular"  href="/categories/{{ $subcategory->id }}">{{ $subcategory->categoryName }}</a>
    </li>
@endforeach
