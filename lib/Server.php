<?php
namespace Phud;
use Phud\Actors\User,
	\Exception;

class Server
{
	use Listener;
	
	protected $host = '';
	protected $port = 0;
	protected $connection = null;
	protected $server = null;
	protected $deploy = null;
	
	public function __construct($config)
	{
		$this->host = $config['host'];
		$this->port = $config['port'];

		// create an instance of the beehive server to listen on the host/port
		$this->server = new \Beehive\Server($this->host, $this->port);
		$this->server->setClientType('\Phud\Client');
		$this->server->setConnectCallback([$this, 'connect']);
		$this->server->setupListener();

		// set up server events
		$this->on('tick', function() { $this->logStatus(); });

		// deploy the game environment
		(new Deploy($config['lib'], $config['deploy']))->deployEnvironment($this);
	}

	public function run()
	{
		$this->on('cycle', function() {
			$this->server->listen();
		});
		$pulse = intval(date('U'));
		$next_tick = $pulse + intval(round(rand(30, 40)));
		while(1) {
			$new_pulse = intval(date('U'));
			if($pulse + 1 === $new_pulse) {
				$this->fire('pulse');
				$pulse = $new_pulse;
			}
			if($pulse === $next_tick) {
				$this->fire('tick');
				$next_tick = $pulse + intval(round(rand(30, 40)));
			}
			$this->fire('cycle');
		}
	}

	public function connect(Client $client)
	{
		$this->fire('connect', $client);
		$this->on('pulse', function() use ($client) {
			$client->fire('pulse');
		});
		$this->on('tick', function() use ($client) {
			$client->fire('tick');
		});
		$this->on('cycle', function($event) use ($client) {
			$client->checkCommandBuffer();
			if(!is_resource($client->getConnection())) {
				$event->kill();
			}
		});
	}
	
	public function __toString()
	{
		return $this->server->__toString();
	}

	protected function logStatus()
	{
		Debug::log("[info] tick - memory ".memory_get_peak_usage().", allocated ".memory_get_usage()." kb");
	}
}
