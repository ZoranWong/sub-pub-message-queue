<?php


namespace ZoranWong\SubPubMessageQueue;


use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;

class RabbitMQBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;
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
    }
}
