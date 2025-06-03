@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pending Removals (Sales)</h3>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Item</th>
                <th>Location</th>
                <th>Quantity</th>
                <th>Sale Ref</th>
                <th>Requested By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($removals as $rq)
            <tr>
                <td>
                    @if($rq->inventoryMaster->fgp_item_id)
                        {{ $rq->inventoryMaster->flavor->name }}
                    @else
                        {{ $rq->inventoryMaster->custom_item_name }}
                    @endif
                </td>
                <td>{{ $rq->location->name }}</td>
                <td>{{ $rq->quantity }}</td>
                <td>{{ $rq->sale_reference }}</td>
                <td>{{ $rq->requestedBy->name }}</td>
                <td>
                    <form method="POST" action="{{ route('franchise.inventory.remove.confirm', $rq->id) }}" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Confirm</button>
                    </form>
                    <form method="POST" action="{{ route('franchise.inventory.remove.cancel', $rq->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-secondary">Cancel</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
