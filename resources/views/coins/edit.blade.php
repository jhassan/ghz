<!DOCTYPE html>
<html>
<head>
    <title>Manage Coins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

    <button class="btn btn-success"><a href="/candles" class="text-white">Back to page</a></button>
    <div class="container">
        <h2>Manage Coins</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('coins.update') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="coins" class="form-label">Coins (comma separated)</label>
                <textarea name="coins" id="coins" rows="5" class="form-control">{{ old('coins', $coinString) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Coins</button>
        </form>
    </div>

</body>
</html>
