<h2>All Franchise Scheduled Posts</h2>

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
            <th>Franchise ID</th>
            <th>Scheduled For</th>
            <th>Message</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($posts as $post)
        <tr>
            <td>{{ $post->franchise_id }}</td>
            <td>{{ $post->scheduled_for }}</td>
            <td>{{ $post->message }}</td>
            <td>{{ $post->posted ? 'Posted' : 'Pending' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
