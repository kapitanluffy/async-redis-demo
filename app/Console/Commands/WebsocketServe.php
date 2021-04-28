<?php
namespace App\Console\Commands;

use App\EventHandler;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server as Reactor;

class WebsocketServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve {--port=9000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the websocket server';

    protected $server;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $address = "0.0.0.0";
        $port = $this->option('port');
        $this->info("Running websocket server on port {$port}. Hello!");

        $loop = app('async-redis')->getLoop();
        $handler = new EventHandler();
        $socket = new Reactor($address . ':' . $port, $loop);

        $server = new IoServer(
            new HttpServer(new WsServer($handler)),
            $socket,
            $loop
        );

        // $client->end();
        $server->run();
    }
}
