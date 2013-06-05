@if(!empty($times))
<ul>
    @foreach($times as $key => $time)
    <li>{{ $key }} : {{ number_format($time, 5) }} seconds</li>
    @endforeach
</ul>
@endif