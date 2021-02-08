<?php

namespace ZoranWong\SubPubMessageQueue\Listener;

abstract class BroadcasterListener
{
    protected $broadcaster = null;

    public function sub($channels)
    {
    }
}
