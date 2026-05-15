<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpusku - Error {{ $status }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f4f6f8;
            color: #1f2937;
        }

        .card {
            max-width: 560px;
            margin: 24px;
            padding: 32px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .code {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 18px 0 10px;
            font-size: 28px;
        }

        p {
            margin: 0;
            line-height: 1.7;
            color: #475569;
        }
    </style>
</head>

<body>
    <main class="card">
        <div class="code">Error {{ $status }}</div>
        <h1>Terjadi kesalahan pada sistem</h1>
        <p>{{ $message }}</p>
    </main>
</body>

</html>