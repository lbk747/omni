<?php namespace Sorora\Omni;

use Sorora\Omni\Loggers\Time;

class Omni {

    protected $view_data = array();

    protected $logs = array();

    public $time;

    public function __construct(Time $time)
    {
        $this->time = $time;
    }

    /**
     * Returns view data
     *
     * @return string
     */
    public function getViewData()
    {
        return $this->view_data;
    }

    /**
     * Sets View data if it meets certain criteria
     *
     * @param array $data
     * @return void
     */
    public function setViewData($data)
    {
        foreach($data as $key => $value)
        {
            if(! is_object($value))
            {
                $this->addKeyToData($key, $value);
            }
            elseif(method_exists($value, 'toArray'))
            {
                $this->addKeyToData($key, $value->toArray());
            }
        }
    }

    /**
     * Adds data to the array if key isn't set
     *
     * @param string $key
     * @param string|array $value
     * @return void
     */
    protected function addKeyToData($key, $value)
    {
        if(is_array($value))
        {
            if(!isset($this->view_data[$key]) or (is_array($this->view_data[$key]) and !in_array($value, $this->view_data[$key])))
            {
                $this->view_data[$key][] = $value;
            }
        }
        else
        {
            $this->view_data[$key] = $value;
        }
    }

    /**
     * Outputs gathered data to make Profiler
     *
     * @return html?
     */
    public function outputData()
    {
        if(\Config::get('omni::profiler'))
        {
            // Sort the view data alphabetically
            ksort($this->view_data);

            $this->time->totalTime();

            $data = array(
                'times' => $this->time->getTimes(), 
                'view_data' => $this->view_data, 
                'sql_log' => array_reverse(\DB::getQueryLog()),
                'app_logs' => $this->logs
            );

            return \View::make('omni::profiler.core', $data);
        }
    }

    /**
     * Cleans an entire array (escapes HTML)
     *
     * @param array $data
     * @return array
     */
    public function cleanArray($data)
    {
        array_walk_recursive($data, function (&$data)
        {
            if(!is_object($data))
            {
                $data = htmlspecialchars($data);
            }
        });
        return $data;
    }

    /**
     * Gets the memory usage
     *
     * @return string
     */
    public function getMemoryUsage()
    {
        return $this->formatBytes(memory_get_usage());
    }

    /**
     * Breaks bytes into larger chunks (e.g. B => MB)
     *
     * @param strng $bytes
     * @return string
     */
    protected function formatBytes($bytes)
    {
        $measures = array('B', 'KB', 'MB', 'DB');
        $bytes = memory_get_usage();
        for($i = 0; $bytes >= 1024; $i++)
        {
            $bytes = $bytes/1024;
        }
        return number_format($bytes,($i ? 2 : 0),'.', ',').$measures[$i];
    }

    /**
     * Store log for later
     *
     * @param string $type
     * @param string|object $message
     */
    public function addLog($type, $message)
    {
        $this->logs[] = array($type, $message);
    }

    /**
     * Start timer
     *
     * @param string $key
     */
    public function start($key)
    {
        $this->time->start($key);
    }

    /**
     * End timer
     *
     * @param string $key
     */
    public function end($key)
    {
        $this->time->end($key);
    }
}
