{{-- filepath: resources/views/corporate_admin/expense/category/view.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="container py-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
                <i class="fa fa-arrow-left me-1"></i> Go Back
            </a>
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-list me-2"></i>Expense Categories (P&amp;L Format)</h5>
                </div>
                <div class="card-body">
                    @if($categories->count())
                        <div class="pl-statement">
                            @foreach($categories as $category)
                                <div class="pl-category mb-3">
                                    <div class="fw-bold text-primary fs-5 mb-2" style="border-bottom:1px solid #e3e3e3;">
                                        {{ $category->category }}
                                    </div>
                                    @if($category->expenseSubCategories->count())
                                        <ul class="pl-subcategory-list mb-0 ps-3">
                                            @foreach($category->expenseSubCategories as $sub)
                                                <li class="mb-1">
                                                    <span class="fw-semibold">{{ $sub->category }}</span>
                                                    @if($sub->description)
                                                        <span class="text-muted small">— {{ $sub->description }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-muted ps-3">No subcategories</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">No categories found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pl-statement {
        font-size: 1.08rem;
    }
    .pl-category {
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
    }
    .pl-category:last-child {
        border-bottom: none;
    }
    .pl-subcategory-list {
        list-style-type: disc;
    }
    .pl-subcategory-list li {
        margin-left: 1.2em;
        line-height: 1.7;
    }
</style>
@endpush
