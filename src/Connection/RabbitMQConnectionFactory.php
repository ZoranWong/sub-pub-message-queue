<?php


namespace ZoranWong\SubPubMessageQueue\Connection;


use Workerman\RabbitMQ\Client;

class RabbitMQConnectionFactory implements ConnectionFactory
{

    public function connection($connection)
    {
        // TODO: Implement connection() method.
        return new Client([]);
    }
}
