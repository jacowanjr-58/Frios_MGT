<h2>Create Post from Template</h2>

@if (session('message'))
    <p style="color: green;">{{ session('message') }}</p>
@endif

<form method="POST" action="/marketing/schedule-post">
    @csrf

    <label>Post Content:</label><br>
    <textarea name="message" rows="5" cols="60">{{ old('message', session('template_body')) }}</textarea><br><br>

    <label>Schedule Time:</label><br>
    <input type="datetime-local" name="scheduled_for"><br><br>

    <button type="submit">Schedule Post</button>
</form>
