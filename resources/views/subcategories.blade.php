@foreach ($subcategories as $subcategory)
    <li>
        <a href="/categories/{{ $subcategory->id }}">{{ $subcategory->categoryName }}</a>
        @if ($subcategory->subcategories->isNotEmpty())
            <ul>
                @include('partials.subcategories', ['subcategories' => $subcategory->subcategories])
            </ul>
        @endif
    </li>
@endforeach
