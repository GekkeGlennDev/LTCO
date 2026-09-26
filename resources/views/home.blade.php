@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Welcome, {{ auth()->user()->name }}</h1>

    <div class="converter">
        <form method="GET" action="{{ route('home') }}" class="card">
            <div class="field">
                <label for="currency">Currency</label>
                <select id="currency" name="currency">
                    @foreach ($currencies as $option)
                        <option value="{{ $option }}" @selected($option === $currency)>{{ strtoupper($option) }}</option>
                    @endforeach
                </select>
                @error('currency') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" step="any" min="0" value="{{ old('amount', $amount) }}">
                @error('amount') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit">Convert</button>
        </form>

        <div class="card">
            @if ($rates->isEmpty())
                <p class="hint">No exchange rates available for {{ strtoupper($currency) }}.</p>
            @else
                <p class="hint">Rates valid on {{ $rates->first()->valid_on->toDateString() }}</p>
                <table>
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Rate</th>
                            <th>{{ number_format($amount, 2) }} {{ strtoupper($currency) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rates as $rate)
                            <tr>
                                <td>{{ strtoupper($rate->targetCurrency->name) }}</td>
                                <td>{{ number_format($rate->rate, 6) }}</td>
                                <td>{{ number_format($amount * $rate->rate, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
