<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .error-container {
            text-align: center;
            max-width: 420px;
            padding: 40px 32px 32px 32px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 1.5px 6px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
        }

        .error-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 24px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%);
            border-radius: 50%;
            box-shadow: 0 4px 16px rgba(255, 95, 109, 0.15);
        }
        .error-icon svg {
            width: 40px;
            height: 40px;
            color: #fff;
        }

        h1, h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 12px 0;
            color: #ff5f6d;
            letter-spacing: -1px;
        }
        h2 {
            font-size: 1.5rem;
            color: #ff5f6d;
        }
        p {
            margin-bottom: 20px;
            color: #444;
            font-size: 1.08rem;
        }
        .details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 18px;
            color: #b91c1c;
            font-size: 1rem;
            word-break: break-word;
        }
        a {
            color: #2563eb;
            background: #e0e7ef;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(37,99,235,0.07);
        }
        a:hover {
            background: #2563eb;
            color: #fff;
        }
        @media (max-width: 600px) {
            .error-container {
                padding: 24px 8px 16px 8px;
                max-width: 98vw;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        @if ($code)
            <h2>Error Code: {{ $code }}</h2>
        @else
            <h1>500 - Server Error</h1>
        @endif

        @if (isset($message))
            <div class="details"><strong>Error Details:</strong> {{ $message }}</div>
        @else
            <p>We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists.</p>
        @endif
        <a href="{{ url('/') }}">Go back to the homepage</a>
    </div>
</body>

</html>
