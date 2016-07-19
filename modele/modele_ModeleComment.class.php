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
        $this->desc = $desc;
    }


}