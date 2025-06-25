<h2>Franchise Scheduled Posts</h2>

<form method="GET">
    <label>Filter by Date:</label>
    <input type="date" name="date" value="{{ request('date') }}">
    <label>Status:</label>
    <select name="status">
        <option value="">All</option>
        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
    </select>
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Scheduled For</th>
            <th>Message</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($posts as $post)
        <tr>
            <td>{{ $post->scheduled_for }}</td>
            <td>{{ $post->message }}</td>
            <td>{{ $post->posted ? 'Posted' : 'Pending' }}</td>
            <td>
                @if (!$post->posted)
                <form action="/marketing/post/{{ $post->id }}/cancel" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Cancel</button>
                </form>

                <form action="/marketing/post/{{ $post->id }}/reschedule" method="POST" style="display:inline;">
                    @csrf
                    <input type="datetime-local" name="new_time">
                    <button type="submit">Reschedule</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
