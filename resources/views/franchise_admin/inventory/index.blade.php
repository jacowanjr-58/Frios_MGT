@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <h1 class="text-2xl font-bold mb-4">Inventory List</h1>

  <div class="bg-white p-6 rounded shadow">
    <table class="table-auto w-full">
      <thead>
        <tr class="bg-gray-200">
          <th class="px-4 py-2 text-left">Item</th>
          <th class="px-4 py-2 text-center">On Hand</th>
          <th class="px-4 py-2 text-right">Default Cost</th>
          <th class="px-4 py-2 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inventories as $inv)
          <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}">
            <td class="px-4 py-2 border">{{ $inv->item_name }}</td>
            <td class="px-4 py-2 border text-center">{{ $inv->total_quantity }}</td>
            <td class="px-4 py-2 border text-right">${{ number_format($inv->default_cost, 2) }}</td>
            <td class="px-4 py-2 border text-center">
              <a href="{{ route('franchise.inventory.edit', $inv->inventory_id) }}" class="text-blue-600 hover:underline">Edit</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-2 text-center">No active inventory found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-4">
      {{ $inventories->links() }}
    </div>
  </div>
</div>
@endsection
