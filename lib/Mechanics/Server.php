<?php
namespace Mechanics;
use \Living\Mob,
	\Mechanics\Command\Command,
	\Mechanics\Event\Event,
	\Mechanics\Event\Broadcaster,
	\Mechanics\Event\Subscriber,
	\Living\User,
	\Exception,
	\stdClass;

class Server
{
	use Broadcaster;
	
	private $address = '';
	private $port = 0;
	private $socket = null;
	private $clients = [];
	private static $instance = null;
	
	public function __construct($address, $port)
	{
		$this->address = $address;
		$this->port = $port;
		self::$instance = $this;

		// Incorporate classes that will make up the game
		$this->readDeploy('/init/');

		// Initialize these environment variables
		Debug::addDebugLine("Initializing environment");
		foreach([
				'\Mechanics\Command\Command',
				'\Mechanics\Race',
				'\Mechanics\Ability\Ability'
			] as $required) {
			Debug::addDebugLine("initializing ".$required);
			$required::runInstantiation();
		}

		$this->readDeploy('/areas/');

		$this->checkDeploySuccess();

		// open the socket
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if($this->socket === false)
			die('No socket');
		socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($this->socket, $this->address, $this->port) or die('Could not bind to address');
		socket_listen($this->socket);
	}
	
	private function __destruct()
	{
		socket_close($this->socket);
	}

	protected function readDeploy($start)
	{
		$d = dir(dirname(__FILE__).'/../../deploy'.$start);
		while($cd = $d->read()) {
			if(substr($cd, -4) === '.php') {
				Debug::addDebugLine("init deploy: ".$cd);
				$anon = new Anonymous();
				$anon->_require_once($d->path.'/'.$cd);
			} else if(strpos($cd, '.') === false) {
				$this->readDeploy($start.$cd);
			}
		}
	}

	protected function checkDeploySuccess()
	{
		if(!Room::getStartRoom()) {
			throw new Exception('Start room not set');
		}
	}

	public static function instance()
	{
		return self::$instance;
	}
	
	public function run()
	{
		$this->addSubscriber(
			new Subscriber(
				Event::EVENT_CONNECTED,
				function($subscriber, $server, $client) {
					$server->addClient($client);
				}
			)
		);
		$this->addSubscriber(
			new Subscriber(
				Event::EVENT_GAME_CYCLE,
				function($subscriber, $server) {
					$server->scanNewConnections();
				}
			)
		);
		$this->addSubscriber(
			new Subscriber(
				Event::EVENT_PULSE,
				function($subscriber, $server) {
					$users = User::getInstances();
					array_walk(
						$users,
						function($u) {
							$target = $u->getTarget();
							if($target) {
								Server::out($u, ucfirst($target).' '.$target->getStatus().".\n");
								Server::out($u, $u->prompt(), false);
							}
						}
					);
				},
				Subscriber::DEFERRED
			)
		);
		$pulse = intval(date('U'));
		$next_tick = $pulse + intval(round(rand(30, 40)));
		while(1) {
			$new_pulse = intval(date('U'));
			if($pulse + 1 === $new_pulse) {
				$this->fire(Event::EVENT_PULSE);
				$pulse = $new_pulse;
			}
			if($pulse === $next_tick) {
				$this->fire(Event::EVENT_TICK);
				$next_tick = $pulse + intval(round(rand(30, 40)));
			}
			$this->fire(Event::EVENT_GAME_CYCLE);
		}
	}

	public function scanNewConnections()
	{
		$n = null;

		// check for new connections
		$s = [$this->socket];
		$new_connection = socket_select($s, $n, $n, 0, 0);
		if($new_connection) {
			$this->fire(Event::EVENT_CONNECTED, new Client(socket_accept($this->socket)));
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
				Debug::addDebugLine("Socket write error, client link dead");
				return false;
			}
		}
	}

	public function addClient(Client $client)
	{
		$this->clients[] = $client;
		$this->addSubscriber(
			new Subscriber(
				Event::EVENT_GAME_CYCLE,
				$client,
				function($subscriber, $server, $client) {
					$client->checkCommandBuffer();
					if(!is_resource($client->getSocket())) {
						$subscriber->kill();
					}
				}
			)
		);
	}
	
	public function disconnectClient(Client $client)
	{
		// Take the user out of its room
		$user = $client->getUser();
		if($user && $user->getRoom()) {
			$user->getRoom()->actorRemove($user);
		}

		$this->removeSubscriber($user->getSubscriberTick());
		
		// clean out the client
		socket_close($client->getSocket());
		$key = array_search($client, $this->clients);
		unset($this->clients[$key]);

		// reindex arrays
		$this->clients = array_values($this->clients);
		Debug::addDebugLine($user." disconnected");
	}
	
	public static function chance()
	{
		return rand(0, 10000) / 100;
	}

	public static function _range($min, $max, $n)
	{
		return $min > $n ? $min : ($max < $n ? $max : $n);
	}

	public function __toString()
	{
		return $this->address.':'.$this->port;
	}
}
?>
