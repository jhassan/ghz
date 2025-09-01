@extends('layouts.app')

@section('content')
<div class="container">
    <h2>📊 Bullish Engulfing Spot Candles</h2>

    <form method="GET" class="form-inline mb-4">
        <label for="symbol" class="mr-2">Filter by Symbol:</label>
        <select name="symbol" id="symbol" class="form-control mr-2">
            <option value="">-- All --</option>
            @foreach($symbols as $symbol)
                <option value="{{ $symbol }}" {{ request('symbol') == $symbol ? 'selected' : '' }}>
                    {{ $symbol }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Symbol</th>
                <th>Interval</th>
                <th>Open Time</th>
                <th>Open</th>
                <th>High</th>
                <th>Low</th>
                <th>Close</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candles as $candle)
                <tr>
                    <td>{{ $candle->symbol }}</td>
                    <td>{{ $candle->interval }}</td>
                    <td>{{ $candle->open_time }}</td>
                    <td>{{ $candle->open }}</td>
                    <td>{{ $candle->high }}</td>
                    <td>{{ $candle->low }}</td>
                    <td>{{ $candle->close }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $candles->withQueryString()->links() }}
</div>
@endsection
