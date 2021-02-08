<?php
namespace ZoranWong\SubPubMessageQueue\Listener;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Support\Facades\Event;

class RedisBroadcasterListener extends BroadcasterListener
{
    protected $redis = null;
    protected $connection = '';

    /**
     *
     * @param Redis $redis
     * @param string|null $connection
     */
    public function __construct(Redis $redis, $connection = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
        $this->broadcaster = $this->redis->connection($connection);
    }

    public function sub($channels)
    {
        $this->broadcaster->subscribe($channels, function ($payload) {
            $payload = json_decode($payload, true);
            $event =  $payload['event'];
            if($event) {
                if (class_exists($event)) {
                    $event = new $event($payload['data']);
                    Event::dispatch($event);
                } else {
                    Event::dispatch($event, $payload['data']);
                }
            }
        });
    }
}
