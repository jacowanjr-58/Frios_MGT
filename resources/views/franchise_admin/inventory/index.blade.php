@extends('layouts.app')

@section('content')

<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">
        <div class="container mx-auto px-4">
            <div class="row mb-4 align-items-center">
                <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                    <a href="{{ route('franchise.inventory.create') }}"
                        class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Inventory Item</a>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card m-0">
                        <div class="card-body py-3 py-md-2">
                            <div class="d-sm-flex d-block align-items-center">
                                <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                    <i class="bi bi-shop-window me-3 fs-3 text-primary"></i>
                                    <div class="media-body">
                                        <p class="mb-1 fs-12">Total Inventory</p>
                                        <h3 class="mb-0 font-w600 fs-22">{{ $inventories->count() }} Orders</h3>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="container mx-auto px-4">
                <h1 class="text-2xl font-bold mb-4">Inventory List</h1>

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="bg-white p-6 rounded shadow">
                    <table class="table-auto w-full">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 text-left">Item</th>
                                <th class="px-4 py-2 text-center">Total on Hand</th>
                                <th class="px-4 py-2 text-center">Totals Breakdown</th>
                                <th class="px-4 py-2 text-right">Base Cost</th>
                                <th class="px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inv)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}">
                                {{-- Use the item_name accessor to get custom or corporate name --}}
                                <td class="px-4 py-2 border">{{ $inv->item_name }}</td>
                                <td class="px-4 py-2 border">{{ $inv->total_quantity }}</td>
                                                    {{-- nicely broken out --}}
                                <td class="px-4 py-2 border">
                                    {{ $inv->cases }} cases,
                                    {{ $inv->units }} units
                                </td class="px-4 py-2 border">
                                <td class="px-4 py-2 border text-right">
                                    {{-- I don't think I need this "IF" since saving case_cost as cogs_case --}}
                                    @if($inv->flavor)
                                    ${{ number_format($inv->flavor->case_cost, 2) }}
                                    @else
                                    ${{ number_format($inv->cogs_case, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 border text-center">
                                    <a href="{{ route('franchise.inventory.edit', $inv->inventory_id) }}"
                                        class="text-blue-600 hover:underline">Edit</a>
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

        </div>
    </div>
    @endsection
