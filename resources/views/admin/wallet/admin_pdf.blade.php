<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Driver Wallets</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Driver Wallets</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Admin Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Balance</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wallets as $wallet)
                <tr>
                    <td>{{ $wallet->id }}</td>
                    <td>{{ $wallet->user ? $wallet->user->name : '-' }}</td>
                    <td>{{ $wallet->user ? $wallet->user->email : '-' }}</td>
                    <td>{{ $wallet->user ? $wallet->user->phone : '-' }}</td>
                    <td>{{ number_format($wallet->balance, 2) }}</td>
                    <td>{{ $wallet->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
