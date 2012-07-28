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
	protected $clients = [];
	protected $deploy = null;
	protected static $instance = null;
	
	public function __construct($config)
	{
		$this->host = $config['host'];
		$this->port = $config['port'];

		// open the socket
		$this->connection = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_option($this->connection, SOL_SOCKET, SO_REUSEADDR, 1);
		$success = @socket_bind($this->connection, $this->host, $this->port);
		if($success) {
			socket_listen($this->connection);
			Debug::log("[init] server is listening for incoming transmissions on (".$this.")");
		}

		// set up server events
		$this->on('cycle', function() { $this->scanNewConnections(); });
		$this->on('tick', function() { $this->logStatus(); });

		(new Deploy($config['lib'], $config['deploy']))->deployEnvironment($this);
	}
	
	public function __destruct()
	{
		if(is_resource($this->connection)) {
			socket_close($this->connection);
		}
	}

	public static function instance()
	{
		return self::$instance;
	}

	public function getClients()
	{
		return $this->clients;
	}

	public function run()
	{
		self::$instance = $this;
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

	public static function out($client, $message, $break_line = true)
	{
		if($client instanceof User) {
			$client = $client->getClient();
		}

		if($client instanceof Client) {
			if(!is_resource($client->getSocket())) {
				return false;
			}
			
			$data = $message . ($break_line === true ? "\r\n" : "");
			$bytes_written = socket_write($client->getSocket(), $data, strlen($data));

			if($bytes_written === false) {
				Debug::log("[warn] socket write error, client link dead");
				return false;
			}
		}
	}

	public function __toString()
	{
		return $this->host.':'.$this->port;
	}

	protected function addClient(Client $client)
	{
		$this->clients[] = $client;
		$this->on('cycle', function($event) use ($client) {
			$client->checkCommandBuffer();
			if(!is_resource($client->getSocket())) {
				$event->kill();
			}
		}, 'end');
		$this->on('pulse', function() use ($client) {
			$client->fire('pulse');
		}, 'end');
		$client->on('quit', function() use ($client) {
			$this->disconnectClient($client);
		});
		$this->fire('connect', $client);
	}
	
	protected function disconnectClient(Client $client)
	{
		// Take the user out of its room
		$user = $client->getUser();
		if($user) {
			if($user->getRoom()) {
				$user->getRoom()->actorRemove($user);
			}
		}

		// clean out the client
		socket_close($client->getSocket());
		$key = array_search($client, $this->clients);
		unset($this->clients[$key]);

		// reindex arrays
		$this->clients = array_values($this->clients);
		Debug::log($user." disconnected");
	}
	
	protected function scanNewConnections()
	{
		$n = null;

		// check for new connections
		$s = [$this->connection];
		$new_connection = socket_select($s, $n, $n, 0, 0);
		if($new_connection) {
			$this->addClient(new Client(socket_accept($this->connection)));
		}
	}
	
	protected function logStatus()
	{
		Debug::log(
			"[memory ".(memory_get_peak_usage(true)/1024)." kb\n".
			"[allocated ".(memory_get_usage(true)/1024)." kb"
		);
	}
}
