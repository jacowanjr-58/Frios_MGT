<h2>Choose a Post Template</h2>

<ul>
@foreach ($templates as $template)
    <li>
        <strong>{{ $template->title }}</strong><br>
        <p>{{ $template->body }}</p>
        <form method="POST" action="/marketing/use-template">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">
            <button type="submit">Use This Template</button>
        </form>
    </li>
@endforeach
</ul>
