<?php


class Comment
{
    protected $desc = '';

    /**
     * Comment constructor.
     * @param string $desc
     */
    public function __construct($desc)
    {
        $this->desc = str_replace('&', '&amp;', $desc);
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }




}