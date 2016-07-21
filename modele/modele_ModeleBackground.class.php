<?php


class Background
{
    protected $time = '';

    protected $type = '';

    protected $level = '';

    /**
     * Background constructor.
     * @param string $time
     * @param string $type
     * @param string $level
     */
    public function __construct($time, $type, $level)
    {
        $this->time = $time;
        $this->type = $type;
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }




}