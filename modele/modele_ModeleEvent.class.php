<?php


class Event
{
    protected $desc = '';

    protected $type = '';

    protected $extent = '';

    /**
     * Event constructor.
     * @param string $desc
     * @param string $type
     * @param string $extent
     */
    public function __construct($desc, $type, $extent)
    {
        $this->desc = str_replace('&', '&amp;', $desc);
        $this->type = $type;
        $this->extent = $extent;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
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
    public function getExtent()
    {
        return $this->extent;
    }




}