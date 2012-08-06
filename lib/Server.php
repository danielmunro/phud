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
		$this->server->setReadCallback([$this, 'read']);

		// set up server events
		$this->on('tick', function() { $this->logStatus(); });

		// deploy the game environment
		(new Deploy($config['lib'], $config['deploy']))->deployEnvironment($this);
	}

	public function run()
	{
		// fork it, we'll do it live
		$pid = pcntl_fork();
		if($pid) {
			$this->server->listen();
		} else {
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
	}

	public function __toString()
	{
		return $this->server->__toString();
	}

	public function connect(Client $client)
	{
		$this->fire('connect', $client);
	}

	public function read(Client $client, $message)
	{
		$client->appendCommandBuffer($message);
	}
	
	protected function logStatus()
	{
		Debug::log("[info] memory ".memory_get_peak_usage().", allocated ".memory_get_usage()." kb");
	}
}
