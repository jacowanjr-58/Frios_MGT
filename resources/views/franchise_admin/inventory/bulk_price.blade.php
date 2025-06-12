{{-- filepath: resources/views/franchise_admin/inventory/bulk_price.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-body default-height">
  
        <form method="POST" action="{{ route('franchise.inventory.bulk_price.update', ['franchisee' => request()->route('franchisee')]) }}">
            @csrf
            <div class="container-fluid">
                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Bulk Price Adjustment</h2>
                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                    </div>
                </div>
                <table class="table table-bordered">
                   <thead>
                        <tr>
                            <th> </th>
                            <th colspan="2" class="text-center">Cost of Goods</th>

                            <th class="text-center">Markup %</th>
                            <th colspan="2" class="text-center">Wholesale</th>

                            <th class="text-center">Markup %</th>
                            <th colspan="2" class="text-center">Retail</th>

                            <th> </th>
                        </tr>
                        <tr>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Case</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">

                                <input type="number" id="global-wholesale-markup" class="form-control" value="100" min="0" max="1000"
                                    style="width: 80px;">

                            </th>
                            <th class="text-center">Case</th>
                            <th class="text-center">Unit</th>

                            <th class="text-center">
                                <input type="number" id="global-retail-markup" class="form-control" value="250" min="0" max="1000"
                                    style="width: 80px; ">

                            </th>
                            <th class="text-center">Case</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($inventoryMasters as $master)
                        <tr>
                            <td>{{ $master->item_name }}</td>
                            <td><input type="number" step="0.01" name="cogs_case_{{ $master->inventory_id }}" value="{{ $master->cogs_case }}" class="form-control" /></td>
                            <td><input type="number" step="0.01" name="cogs_unit_{{ $master->inventory_id }}" value="{{ $master->cogs_unit }}" class="form-control" /></td>
                            <td >

                                <button type="button" class="suggest-btn px-1 py-0.5 text-xs border rounded bg-white text-gray-700 hover:bg-gray-100" data-id="Whole_{{ $master->inventory_id }}">
                                    % apply
                                </button>
                            </td>
                            <td><input type="number" step="0.01" name="wholesale_case_{{ $master->inventory_id }}" value="{{ $master->wholesale_case }}" class="form-control" /></td>
                            <td><input type="number" step="0.01" name="wholesale_unit_{{ $master->inventory_id }}" value="{{ $master->wholesale_unit }}" class="form-control" /></td>
                            <td >

                                <button type="button" class="suggest-btn px-1 py-0.5 text-xs border rounded bg-white text-gray-700 hover:bg-gray-100" data-id="Ret_{{ $master->inventory_id }}">
                                    % apply
                                </button>
                            </td>
                            <td><input type="number" step="0.01" name="retail_case_{{ $master->inventory_id }}" value="{{ $master->retail_case }}" class="form-control" /></td>
                            <td><input type="number" step="0.01" name="retail_unit_{{ $master->inventory_id }}" value="{{ $master->retail_unit }}" class="form-control" /></td>
                            <td><textarea name="notes_{{ $master->inventory_id }}" class="form-control"></textarea></td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="10" class="text-center bg-white">
                            <button type="submit" class="btn btn-primary">Update Prices</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    @foreach($inventoryMasters as $master)
    (function() {
        const id = {{ $master->inventory_id }};
        const splitFactor = {{ $master->split_factor ?? 1 }};
        const cogsCaseInput = document.querySelector('input[name="cogs_case_{{ $master->inventory_id }}"]');
        const cogsUnitInput = document.querySelector('input[name="cogs_unit_{{ $master->inventory_id }}"]');
        const wholesaleCaseInput = document.querySelector('input[name="wholesale_case_{{ $master->inventory_id }}"]');
        const wholesaleUnitInput = document.querySelector('input[name="wholesale_unit_{{ $master->inventory_id }}"]');
        const retailCaseInput = document.querySelector('input[name="retail_case_{{ $master->inventory_id }}"]');
        const retailUnitInput = document.querySelector('input[name="retail_unit_{{ $master->inventory_id }}"]');
        const suggestWholesaleBtn = document.querySelector('.suggest-btn[data-id="Whole_{{ $master->inventory_id }}"]');
        const suggestRetailBtn = document.querySelector('.suggest-btn[data-id="Ret_{{ $master->inventory_id }}"]');

        function getCogs() {
            let cogsCase = parseFloat(cogsCaseInput.value) || 0;
            let cogsUnit = parseFloat(cogsUnitInput.value) || 0;
            // If only one is present, calculate the other
            if (cogsCase && !cogsUnit && splitFactor) {
                cogsUnit = cogsCase / splitFactor;
                cogsUnitInput.value = cogsUnit.toFixed(2);
            } else if (!cogsCase && cogsUnit && splitFactor) {
                cogsCase = cogsUnit * splitFactor;
                cogsCaseInput.value = cogsCase.toFixed(2);
            }
            return { cogsCase, cogsUnit };
        }

        function suggestWholesale() {
            const { cogsCase, cogsUnit } = getCogs();
            const wholesaleMarkup = parseFloat(document.getElementById('global-wholesale-markup').value) || 100;
            const wholesaleCase = cogsCase * (1 + wholesaleMarkup / 100);
            const wholesaleUnit = cogsUnit * (1 + wholesaleMarkup / 100);
            wholesaleCaseInput.value = wholesaleCase.toFixed(2);
            wholesaleUnitInput.value = wholesaleUnit.toFixed(2);
        }

        function suggestRetail() {
            const { cogsCase, cogsUnit } = getCogs();
            const retailMarkup = parseFloat(document.getElementById('global-retail-markup').value) || 400;
            const retailCase = cogsCase * (1 + retailMarkup / 100);
            const retailUnit = cogsUnit * (1 + retailMarkup / 100);
            retailCaseInput.value = retailCase.toFixed(2);
            retailUnitInput.value = retailUnit.toFixed(2);
        }

        if (suggestWholesaleBtn) {
            suggestWholesaleBtn.addEventListener('click', suggestWholesale);
        }
        if (suggestRetailBtn) {
            suggestRetailBtn.addEventListener('click', suggestRetail);
        }
    })();
    @endforeach
});
</script>
@endpush

<style scoped>
    .btn-primary {
        background-color: #00abc7 !important;
    }
</style>