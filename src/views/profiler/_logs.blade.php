<table>
    <tr>
        <th>Type</th>
        <th>Message</th>
    </tr>
    @foreach($app_logs as $log)
    <tr class="omni-{{ $log[0] }}">
        <td>{{ $log[0] }}</td>
        <td>{{ (is_object($log[1])) ? null : $log[1] }}</td>
    </tr>
    @endforeach
</table>