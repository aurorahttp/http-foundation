<?php

namespace Panlatent\Http;

interface ContextInterface
{
    /**
     * @return ContextInterface
     */
    public function getParent();

    /**
     * @return ContextInterface[]
     */
    public function getParents();

    /**
     * Set parent context
     *
     * @param ContextInterface $context
     */
    public function withParent(ContextInterface $context);
}