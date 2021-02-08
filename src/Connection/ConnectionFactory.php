<?php


namespace ZoranWong\SubPubMessageQueue\Connection;


interface ConnectionFactory
{
    public function connection($connection);
}
