<?php
namespace Phud;
use Phud\Actors\User,
	Onit\Listener,
	\Exception;

class Server
{
	use Listener;
	
	protected $host = '';
	protected $port = 0;
	protected $connection = null;
	protected $server = null;
	protected $deploy = null;
	protected $clients = [];
	
	public function __construct($config)
	{
		$this->host = $config['host'];
		$this->port = $config['port'];

	}

	public function run()
	{
		// start the beehive listener
		$this->start();

		// set up server events
		$this->on('cycle', function() { $this->server->listen(); });
		$this->on('tick', function() { $this->logStatus(); });

		// start looping
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
		Debug::log("client ".$client->getID(). " connected");
		$this->fire('connect', $client);
		$this->onMany([
			['pulse', function($event) use ($client) {
				$client->fire('pulse');
				if(!is_resource($client->getConnection())) {
					$event->unlisten();
				}
			}],
			['tick', function($event) use ($client) {
				$client->fire('tick');
				if(!is_resource($client->getConnection())) {
					$event->unlisten();
				}
			}],
			['cycle', function($event) use ($client) {
				$client->checkCommandBuffer();
				if(!is_resource($client->getConnection())) {
					$event->unlisten();
				}
			}]
		]);
		$client->onMany([
			['broadcast', function($event, $client_sender, $message) {
				foreach($this->clients as $client) {
					if($client_sender != $client) {
						$client->writeLine($message);
					}
				}
			}],
			['who', function($event, $client_sender, &$out, &$n) {
				foreach($this->clients as $client) {
					$u = $client->getUser();
					if($u) {
						$n++;
						$out .= "[".$u->getLevel()." ".$u->getRace()->getAlias()."] ".$u."\n";
					}
				}
			}],
			['disconnect', function($event, $client) {
				$this->removeClient($client);
				$event->unlisten();
			}]
		]);
		$this->clients[] = $client;
	}

	public function removeClient(Client $client)
	{
		$i = array_search($client, $this->clients);
		if($i === false) {
			Debug::error('disconnectClient did not find client in client array');
		} else {
			unset($this->clients[$i]);
			Debug::log('client '.$client->getID().' disconnected');
		}
	}
	
	public function __toString()
	{
		return $this->server->__toString();
	}

	protected function start()
	{
		// create an instance of the beehive server to listen on the host/port
		$this->server = new \Beehive\Server($this->host, $this->port);
		$this->server->setClientType('\Phud\Client');
		$this->server->setConnectCallback([$this, 'connect']);
		$this->server->setupListener();
	}

	protected function logStatus()
	{
		Debug::log("tick - peak memory ".memory_get_peak_usage().", allocated ".memory_get_usage());
	}
}
