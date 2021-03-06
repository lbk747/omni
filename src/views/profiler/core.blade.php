<!-- Omni -->
<div id="omni">
    <style type="text/css">
        #omni-toolbar *, #omni-profiler * {
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }
        #omni-profiler ul, #omni-profiler li {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        #omni-profiler h1 {
            background: #333;
            color: #DDD;
            font-size: 1em;
            padding: .80em;
            padding-right: 4em;
            margin: 0;
        }
        .omni-content {
            height: 100%;
            padding-right: 2.9em;
            overflow: scroll;
        }
        .omni-content table {
            border: none;
            border-spacing: 0;
            width: 100%;
        }
        .omni-content table th {
            background: #555;
            color: #FFF;
            font-style: italic;
            font-weight: normal;
            padding: .5em .8em .5em .5em;
            text-align: left;
        }
        .omni-content table td {
            border-bottom: 1px solid #EEE;
            color: #000;
            padding: .5em .8em .5em .5em;
            vertical-align: top;
        }
        #omni-profiler, #omni-toolbar {
            background: #333;
            font-family: Arial, sans-serif;
            font-size: 16px;
            right: 0;
            top: 0;
            position: fixed;
            text-align: center;
            width: 4em;
            z-index: 5000;
        }
        #omni-profiler {
            background: #FFF;
            border-left: 3px solid #333;
            bottom: 0;
            display: none;
            width: 50%;
        }
        .omni-btn {
            border: none;
            background: none;
            color: #FFF;
            font-weight: bold;
            padding: 1em .3em;
            width: 100%;
        }
        .omni-btn span {
            font-size: .9em;
            font-style: italic;
            font-weight: normal;
        }
        .omni-btn:hover {
            background: #555;
        }
        .omni-arrow {
            border-style: solid;
            display: inline-block;
            margin-top: .3em;
            text-align: center;
            height: 0;
            width: 0;
        }
        .omni-dwn {
            border-color: #FFF transparent transparent transparent;
            border-width: .7em .5em 0 .5em;
        }
        .omni-left {
            border-color: transparent #FFF transparent transparent;
            border-width: .5em .7em .5em 0;
        }
        .omni-right {
            border-color: transparent transparent transparent #FFF;
            border-width: .5em 0 .5em .7em;
        }
        .omni-hide {
            display: none;
        }
        #omni-profiler.omni-full {
            width: 100%;
        }
        .omni-debug td { background: #FFF; }
        .omni-info td { background: rgba(200, 240, 255, .2); }
        .omni-notice td { background: rgba(200, 240, 255, .5); }
        .omni-warning td { background: rgba(255, 231, 173, .5); }
        .omni-error td { background: rgba(255, 0, 0, .25); }
        .omni-critical td { background: rgba(255, 255, 0, .8); }
        .omni-alert td { background: rgba(255, 255, 0, .5); }
        .omni-emergency td { background: rgba(255, 0, 0, .8); }
    </style>
    <div id="omni-profiler">
        <ul>
            <li class="omni-view omni-hide">
                <h1>View Parameters</h1>
                <div class="omni-content">
                    @include('omni::profiler._view_data')
                </div>
            </li>
            <li class="omni-times omni-hide">
                <h1>Time</h1>
                <div class="omni-content">
                    @include('omni::profiler._times')
                </div>
            </li>
            <li class="omni-sql omni-hide">
                <h1>SQL</h1>
                <div class="omni-content">
                    @include('omni::profiler._sql')
                </div>
            </li>
            <li class="omni-logs omni-hide">
                <h1>Logs</h1>
                <div class="omni-content">
                    @include('omni::profiler._logs')
                </div>
            </li>
        </ul>
    </div>
    <div id="omni-toolbar">
        <button class="omni-btn omni-minimize" data-jsfunc="minimize">_</button>
        <div class="omni-sub">
            <button class="omni-btn" data-jsfunc="flexi" data-jsparams="view">
                <span>Mem</span>
                {{ Omni::getMemoryUsage() }}
            </button>
            <button class="omni-btn" data-jsfunc="flexi" data-jsparams="times">
                {{ round($times['total'], 3) }}
                <span>seconds</span>
            </button>
            <button class="omni-btn" data-jsfunc="flexi" data-jsparams="sql">
                <span>SQL</span>
                ({{ count($sql_log) }})
            </button>
            <button class="omni-btn" data-jsfunc="flexi" data-jsparams="logs">
                <span>Logs</span>
                ({{ count($app_logs) }})
            </button>
            <button class="omni-btn" data-jsfunc="resize">
                <span class="omni-arrow omni-left"></span>
                <span class="omni-arrow omni-right"></span>
            </button>
        </div>
    </div>
    @if(Config::get('omni::jquery'))
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    @endif
    <script>
        var $omni_profiler   = $(document.getElementById('omni-profiler')),
            $omni_toolbar    = $(document.getElementById('omni-toolbar')),
            $omni_sub        = $omni_toolbar.children('.omni-sub');
        var func, params;
        var omni = {
            flexi : function(type)
            {
                $ul = $omni_profiler.children('ul');
                var $box = $ul.children('.omni-'+type);
                if($box.hasClass('omni-hide'))
                {
                    $omni_profiler.show();
                    $ul.children('li').not('.omni-'+type).addClass('omni-hide');
                    $box.removeClass('omni-hide');
                }
                else
                {
                    $omni_profiler.hide();
                    $box.addClass('omni-hide');
                }
            },
            minimize : function()
            {
                $omni_profiler.hide();
                if($omni_sub.css('display') == 'block')
                {
                    $omni_sub.css('display', 'none');
                    $omni_toolbar.children('.omni-minimize').html('<span class="omni-arrow omni-dwn"></span>');
                }
                else
                {
                    $omni_sub.css('display', 'block');
                    $omni_toolbar.children('.omni-minimize').html('_');
                }
            },
            resize : function ()
            {
                $omni_profiler.toggleClass('omni-full');
            }
        };
        $(document).ready(function ()
        {
            $omni_toolbar.on('click', 'button', function (event) {
                var $this = $(this);
                if(func = $this.attr('data-jsfunc'))
                {
                    var params = ($this.attr('data-jsparams')) ? $this.attr('data-jsparams') : null;
                    omni[func](params);
                }
            });
        });
    </script>
</div>