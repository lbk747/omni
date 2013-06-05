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
        foreach($data AS $key => $value)
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
        if(!isset($this->view_data[$key]))
        {
            $this->view_data[$key] = $value;
        }
    }

    public function outputData()
    {
        echo \View::make('omni::profiler.core', array('times' => $this->time, 'view_data' => $this->view_data));
    }

    public function setTimer($key)
    {
        $mtime = explode(' ', microtime());
        $this->time[$key] = $mtime[1] + $mtime[0];
    }
}
