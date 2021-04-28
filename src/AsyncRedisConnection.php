<?php

namespace Kapitanluffy\AsyncRedis;

use Closure;
use Clue\React\Redis\Client;
use Illuminate\Contracts\Redis\Connection as ConnectionContract;
use React\EventLoop\LoopInterface;
use Illuminate\Redis\Connections\Connection;

class AsyncRedisConnection extends Connection implements ConnectionContract
{
    protected $loop;

    protected $options;

    public function __construct(Client $client, LoopInterface $loop, $options = [])
    {
        $this->client = $client;
        $this->loop = $loop;
        $this->options = $options;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function createSubscription($channels, Closure $callback, $method = 'subscribe')
    {
        $msgEvent = ($method == "psubscribe") ? "pmessage" : "message";

        foreach ($channels as $channel) {
            $channel = sprintf("%s%s", $this->options['prefix'], $channel);
            $this->client->{$method}($channel);

            $this->client->on($msgEvent, function ($channel, $payload) use ($callback) {
                call_user_func($callback, $payload, $channel);
            });
        }
    }
}
