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

		// open the socket
		$this->server = new \Beehive\Server($this->host, $this->port);
		$this->server->setClientType('\Phud\Client');
		$this->server->setConnectCallback(function($client) {
			$this->fire('connect', $client);
		});
		$this->server->setReadCallback(function($client, $message) {
			$client->appendCommandBuffer($message);
		});

		// set up server events
		$this->on('tick', function() { $this->logStatus(); });

		(new Deploy($config['lib'], $config['deploy']))->deployEnvironment($this);
	}
	
	public function __destruct()
	{
	}

	public function run()
	{
		$this->server->listen(EVLOOP_NONBLOCK);
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

	public function __toString()
	{
		return $this->server->__toString();
	}
	
	protected function logStatus()
	{
		Debug::log(
			"[memory ".(memory_get_peak_usage(true)/1024)." kb\n".
			"[allocated ".(memory_get_usage(true)/1024)." kb"
		);
	}
}
