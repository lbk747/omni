<?php namespace Sorora\Omni;

class Omni {

    protected $view_data = array();
    protected $time = array();

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

    public function outputData()
    {
        $this->time['total'] = $this->time['__end'] - $this->time['__start'];
        unset($this->time['__start']);
        unset($this->time['__end']);
        ksort($this->view_data);
        $sql_log = array_reverse(\DB::getQueryLog());

        echo \View::make('omni::profiler.core', array('times' => $this->time, 'view_data' => $this->view_data, 'sql_log' => $sql_log));
    }

    public function setTimer($key)
    {
        $mtime = explode(' ', microtime());
        $this->time[$key] = $mtime[1] + $mtime[0];
    }

    public function cleanArray($data)
    {
        array_walk_recursive($data, function (&$data)
        {
            $data = htmlspecialchars($data);
        });
        return $data;
    }

    public function memoryUsage()
    {
        return $this->formatBytes(memory_get_usage());
    }

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
}
