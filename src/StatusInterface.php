<?php

namespace Panlatent\Http;

interface StatusInterface
{
    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return string
     */
    public function getStatusReason();
}