@component('mail::message')
# Latest Crypto Symbols

Here are the latest data:

@foreach($cryptoData as $crypto)
    Symbol Name: {{ $crypto->symbol }} Open Time: {{ date('d-F-Y H:i A', strtotime($crypto->open_time)) }} Created at: ({{ date('d-F-Y H:i A', strtotime($crypto->created_at)) }}) <br>
@endforeach

@endcomponent
