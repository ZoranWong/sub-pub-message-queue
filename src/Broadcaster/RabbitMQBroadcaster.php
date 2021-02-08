<?php


namespace ZoranWong\SubPubMessageQueue;


use Bunny\Channel;
use Workerman\RabbitMQ\Client;
use Bunny\Message;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use ZoranWong\SubPubMessageQueue\Connection\RabbitMQConnectionFactory;

class RabbitMQBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;
    /**@var Client $broadcaster*/
    protected $broadcaster = null;
    protected $connection = '';
    /**
     * @inheritDoc
     */
    public function auth($request)
    {
        // TODO: Implement auth() method.
    }

    /**
     * @inheritDoc
     */
    public function validAuthenticationResponse($request, $result)
    {
        // TODO: Implement validAuthenticationResponse() method.
    }

    /**
     * @inheritDoc
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        // TODO: Implement broadcast() method.
        $connection = $this->broadcaster->connect();
        foreach ($channels as $queue) {
            $connection->then(function (\Workerman\RabbitMQ\Client $client) {
                $channel = $client->channel();
                $client->ack($channel);
                return $channel;
            })->then(function (Channel $channel) use ($queue) {
                return $channel->queueDeclare($queue)->then(function () use ($channel) {
                    return $channel;
                });
            })->then(function (Channel $channel) use ($queue, $event, $payload) {
                $data = [
                    'event' => $event,
                    'data' => $payload
                ];
                return $channel->publish(json_encode($data), [], '', $queue)->then(function ()use ($channel) {
                    return $channel;
                });
            })->then(function (Channel $channel) {
                $client = $channel->getClient();
                return $channel->close()->then(function () use ($client) {
                    return $client;
                });
            })->then(function (Client $client) {
                $client->disconnect();
            });
        }
    }
}
