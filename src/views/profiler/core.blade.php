<div id="omni">
    <button style="width:100%;" onclick="javascript: showHideProfiler();">
        Show / Hide Profiler
    </button>
    <ul style="display: none;" id="omni-profiler">
        <li>
            <h1>Memory</h1>
            {{ Omni::memoryUsage() }}
        </li>
        <li>
            <h1>Time</h1>
            @include('omni::profiler._times')
        </li>
        <li>
            <h1>View Parameters</h1>
            @include('omni::profiler._view_data')
        </li>
        <li>
            <h1>SQL</h1>
            @include('omni::profiler._sql')
        </li>
    </ul>
    <script>
        var omni = document.getElementById('omni-profiler');
        function showHideProfiler()
        {
            if(omni.style.display == 'block')
            {
                omni.style.display = 'none';
            }
            else
            {
                omni.style.display = 'block';
            }
        }
    </script>
</div>