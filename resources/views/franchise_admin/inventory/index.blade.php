@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">

        <h3 class="mb-4">Inventory List</h3>

        <div class="row">


            <div class="col text-end">
                <a href="{{ route('franchise.inventory.create') }}" class="btn btn-primary">
                    + Add Inventory
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Location</th>
                        <th>Stock On Hand</th>
                        <th>Stock Count Date</th>
                        <th>Pops On Hand</th>
                        <th>Wholesale Price (Case)</th>
                        <th>Retail Price (Pop)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inv)
                    <tr>
                        <td>{{ $inv->inventory_id }}</td>
                        <td>{{ $inv->item->name ?? '—' }}</td>
                        <td>{{ $inv->location->name ?? '—' }}</td>
                        <td>{{ $inv->total_quantity }}</td>
                        <td>{{ \Carbon\Carbon::parse($inv->stock_count_date)->format('M d, Y') }}</td>
                        <td>{{ $inv->pops_on_hand }}</td>
                        <td>{{ number_format($inv->whole_sale_price_case, 2) }}</td>
                        <td>{{ number_format($inv->retail_price_pop, 2) }}</td>
                        <td>
                            <a href="{{ route('franchise.inventory.edit', $inv->inventory_id) }}"
                                class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            <form action="{{ route('franchise.inventory.destroy', $inv->inventory_id) }}" method="POST"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No inventory records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $inventories->links() }} {{-- Pagination links --}}
        </div>
    </div>
</div>
@endsection
