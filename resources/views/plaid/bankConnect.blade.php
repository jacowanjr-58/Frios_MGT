<!-- In your Blade view -->
<button id="link-button">Connect Bank</button>
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script>
fetch('/plaid/link-token')
    .then(res => res.json())
    .then(data => {
        const handler = Plaid.create({
            token: data.link_token,
            onSuccess: function(public_token, metadata) {
                fetch('/plaid/exchange', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({public_token})
                }).then(() => {
                    alert('Bank linked! Now fetching transactions...');
                    fetch('/plaid/fetch', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    }).then(() => alert('Transactions imported!'));
                });
            }
        });
        document.getElementById('link-button').onclick = function() {
            handler.open();
        };
    });
</script>
