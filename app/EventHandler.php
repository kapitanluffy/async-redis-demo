<?php

namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class EventHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        app('async-redis')->subscribe(["event/global"], function ($payload, $channel) use ($conn) {
            $payload = json_decode($payload);
            $conn->send($payload->data->message);
        });
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if ($msg == "ping🏓") {
            sleep(2);
            return $from->send("🏓pong");
        }

        $from->send($msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $conn->send("bye");
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send("error: " . $e->getMessage());
        $conn->close();
    }
}
