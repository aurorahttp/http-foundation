<?php

namespace Panlatent\Http;

interface ContextAcceptInterface
{
    /**
     * @param ContextInterface $context
     */
    public function acceptContext(ContextInterface $context);
}