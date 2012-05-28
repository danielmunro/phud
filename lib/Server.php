<?php
namespace Phud;
use Phud\Actors\User,
	\Exception,
	\ReflectionClass,
	\stdClass;

class Server
{
	use Listener;
	
	protected $address = '';
	protected $port = 0;
	protected $socket = null;
	protected $clients = [];
	protected $initialized = false;
	private static $instance = null;
	
	protected function __construct()
	{
		global $address, $port;

		$this->address = $address;
		$this->port = $port;

		// open the socket
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		$success = @socket_bind($this->socket, $this->address, $this->port);
		if($success) {
			socket_listen($this->socket);
			$this->initialized = true;
			Debug::log("Server is listening for incoming transmissions on (".$this.")");
		}
	}
	
	public function __destruct()
	{
		if(is_resource($this->socket)) {
			socket_close($this->socket);
		}
	}

	public static function instance()
	{
		return self::$instance ? self::$instance : self::$instance = new self();
	}

	public function isInitialized()
	{
		return $this->initialized;
	}

	public function getClients()
	{
		return $this->clients;
	}
	
	public function addClient(Client $client)
	{
		$this->clients[] = $client;
		$this->on(
			'cycle',
			function($event) use ($client) {
				$client->checkCommandBuffer();
				if(!is_resource($client->getSocket())) {
					$event->kill();
				}
			},
			'end');
	}
	
	public function disconnectClient(Client $client)
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

	public function run()
	{
		$this->on(
			'connect',
			function($event, $server, $client) {
				$server->addClient($client);
			}
		);

		$this->on(
			'cycle',
			function($event, $server) {
				$server->scanNewConnections();
			}
		);

		$this->on(
			'pulse',
			function($event, $server) {
				foreach($server->getClients() as $c) {
					$u = $c->getUser();
					if($u && $u->getTarget()) {
						Server::out($u, ucfirst($u->getTarget()).' '.$u->getTarget()->getStatus().".\n");
						Server::out($u, $u->prompt(), false);
					}
				}
			},
			'end'
		);

		Debug::start();

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
				Debug::log("Socket write error, client link dead");
				return false;
			}
		}
	}

	public function __toString()
	{
		return $this->address.':'.$this->port;
	}

	protected function scanNewConnections()
	{
		$n = null;

		// check for new connections
		$s = [$this->socket];
		$new_connection = socket_select($s, $n, $n, 0, 0);
		if($new_connection) {
			$this->fire('connect', new Client(socket_accept($this->socket)));
		}
	}

	public function deployEnvironment($lib, $deploy)
	{
		// Set the server instance so that the deploy scripts may reference it
		self::$instance = $this;

		// phud framework classes
		Debug::log("Including libs");
		$this->readDeploy($lib.'/');

		// all the game classes
		Debug::log("Including deploy scripts");
		$this->readDeploy($deploy.'/init/');

		// game is initialized
		$this->fire('initialized');

		// area scripts
		Debug::log("Including area scripts");
		$this->readDeploy($deploy.'/areas/');

		// finished deployment
		$this->fire('deployed');
	}
	
	protected function readDeploy($start)
	{
		global $global_path;
		$path = $global_path.'/'.$start;
		if(file_exists($path)) {
			$d = dir($path);
			$deferred = [];
			while($cd = $d->read()) {
				if(strpos($cd, '.') === false) {
					$this->readDeploy($start.$cd.'/');
					continue;
				}
				list($class, $ext) = explode('.', $cd);
				if($ext === 'php') {
					$deferred[] = $class;
				} else if($ext === 'area') {
					new Parser($path.'/'.$cd);
				}
			}
			foreach($deferred as $class) {
				call_user_func(function() use ($d, $class) {
					require_once($d->path.$class.'.php');
				});
			}
		} else {
			throw new Exception('Invalid deploy directory defined: '.$start);
		}
	}
}
?>
