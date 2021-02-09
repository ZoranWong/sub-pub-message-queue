<?php

namespace ZoranWong\SubPubMessageQueue\Listener;


use Bunny\Channel;
use Bunny\Message;
use Illuminate\Support\Facades\Event;
use Workerman\RabbitMQ\Client;
use ZoranWong\SubPubMessageQueue\Connection\RabbitMQConnectionFactory;

class RabbitMQBroadcasterListener extends BroadcasterListener
{
    protected $broadcaster = null;
    protected $rabbitMQFactory = null;
    protected $connection = null;

    public function __construct(RabbitMQConnectionFactory $connectionFactory, string $connection)
    {
        $this->broadcaster = $connectionFactory->connection($connection);
        $this->connection = $connection;
        $this->rabbitMQFactory = $connectionFactory;
    }

    public function sub($channels)
    {
        $connection = $this->broadcaster->connect();
        foreach ($channels as $queue) {
            $connection->then(function (Client $client) {
                $channel = $client->channel();
                $client->ack($channel);
                return $channel;
            })->then(function (Channel $channel) use ($queue) {
                return $channel->queueDeclare($queue)->then(function () use ($channel) {
                    return $channel;
                });
            })->then(function (Channel $channel) use ($queue) {
                $channel->consume(function (Message $message, Channel $channel, Client $client) {
                    $content = $message->content;
                    $payload = json_encode($content, true);
                    $event = $payload['event'];
                    try{
                        if ($event) {
                            if (class_exists($event)) {
                                Event::dispatch(new $event($payload['data']));
                            }else{
                                Event::dispatch($event, $payload['data']);
                            }
                        }
                        $channel->ack($message);
                    }catch (\Exception $exception){
                       $channel->nack($message);
                    }

                }, $queue);
            });
        }
    }
}
