@if($sql_log)
<table>
    <tr>
        <th>No.</th>
        <th>Query</th>
        <th>Bindings</th>
        <th>Time</th>
    </tr>
    @foreach($sql_log as $key => $log)
    <tr>
        <td>{{ $key+1 }}</td>
        <td>{{ $log['query'] }}</td>
        <td>
        @foreach($log['bindings'] as $k => $binding)
            @if($k != count($log['bindings'])-1)
            {{ $binding }},
            @else
            {{ $binding }}
            @endif
        @endforeach
        </td>
        <td>{{ $log['time'] }}</td>
    </tr>
    @endforeach
</table>
@endif