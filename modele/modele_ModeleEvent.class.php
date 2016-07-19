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
        $this->desc = $desc;
        $this->type = $type;
        $this->extent = $extent;
    }


}