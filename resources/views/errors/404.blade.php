<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f9fafb;
            color: #1a1a1a;
        }
        .error-page {
            text-align: center;
            padding: 40px 20px;
        }
        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: #e5e7eb;
            line-height: 1;
            margin-bottom: 16px;
        }
        .error-message {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .error-detail {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 32px;
        }
        .error-page a {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .error-page a:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">404</div>
        <div class="error-message">Halaman Tidak Ditemukan</div>
        <div class="error-detail">Halaman yang Anda cari tidak tersedia atau telah dipindahkan.</div>
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </div>
</body>
</html>
