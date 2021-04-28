<?php

namespace Kapitanluffy\AsyncRedis;

use Illuminate\Contracts\Redis\Connector;
use Clue\React\Redis\Factory;
use React\EventLoop\Factory as LoopFactory;

class AsyncRedisConnector implements Connector
{
    public function connect(array $config, array $options)
    {
        $loop = LoopFactory::create();
        $factory = new Factory($loop);

        $auth = $config['password'] ? sprintf(":%s@", $config['password']) : null;
        $uri = sprintf(
            "redis://%s%s:%s/%s",
            $auth,
            $config['host'],
            $config['port'],
            $config['database']
        );

        $client = $factory->createLazyClient($uri);

        return new AsyncRedisConnection($client, $loop, $options);
    }

    public function connectToCluster(array $config, array $clusterOptions, array $options)
    {
        throw new \LogicException("Not yet supported");
    }
}
