<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Franchise</title>
    <style>
        body {
            background: #e9f9fb;
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .franchise-select-container {
            max-width: 1200px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .franchise-select-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            text-align: center;
            width: 100%;
        }

        .franchise-select-subtitle {
            color: #6c757d;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
            text-align: center;
            width: 100%;
        }

        .franchise-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            justify-content: center;
            width: 100%;
        }

        .franchise-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            padding: 32px 24px 24px 24px;
            min-width: 320px;
            max-width: 350px;
            flex: 1 1 320px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .franchise-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 30px rgba(0, 182, 201, 0.2), 0 2px 16px rgba(0, 0, 0, 0.08);
            z-index: 2;
        }

        .franchise-card h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .franchise-card p {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }

        .franchise-card strong {
            font-weight: 600;
        }

        .franchise-card form {
            width: 100%;
            margin-top: 1.5rem;
        }

        .franchise-card button {
            width: 100%;
            padding: 10px 0;
            background: #00b6c9;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .franchise-card button:hover {
            background: #0097a7;
        }

        .alert.alert-warning {
            font-size: 1rem;
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 1rem;
            border-radius: 8px;
            width: 100%;
        }

        @media (max-width: 900px) {
            body {
                padding: 40px 20px;
            }

            .franchise-cards {
                flex-direction: column;
                align-items: center;
            }

            .franchise-select-title,
            .franchise-select-subtitle {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="franchise-select-container">
        <div class="franchise-select-title">Select Franchise</div>
        <div class="franchise-select-subtitle">Please select a franchise to continue.</div>
        <div class="franchise-cards">
            @forelse($franchises as $franchise)
                <div class="franchise-card">
                    <h4>{{ $franchise->business_name }}</h4>
                    <p><strong>City:</strong> {{ $franchise->city }}</p>
                    <p><strong>State:</strong> {{ $franchise->state }}</p>
                    <p><strong>Address:</strong> {{ $franchise->address1 }} {{ $franchise->address2 }}</p>
                    <form action="{{ route('franchise.set_franchisee') }}" method="POST">
                        @csrf
                        <input type="hidden" name="franchise_id" value="{{ $franchise->franchise_id }}">
                        <button type="submit">Select</button>
                    </form>
                </div>
            @empty
                <div class="franchise-card">
                    <div class="alert alert-warning">No franchises assigned to your account.</div>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html> 