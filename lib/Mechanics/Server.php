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

		// open the socket
		Debug::log("Attempting to create socket on (".$this.")");
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if($this->socket === false) {
			die('No socket');
		}
		socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($this->socket, $this->address, $this->port) or die('Could not bind to address');
		socket_listen($this->socket);
		Debug::log("Server is listening for incoming transmissions on (".$this.")");
	}
	
	public function __destruct()
	{
		if(is_resource($this->socket)) {
			socket_close($this->socket);
		}
	}

	public static function instance()
	{
		return self::$instance;
	}
	
	public function addClient(Client $client)
	{
		$this->clients[] = $client;
		$this->addSubscriber($client->getInputSubscriber());
	}
	
	public function disconnectClient(Client $client)
	{
		// Take the user out of its room
		$user = $client->getUser();
		if($user) {
			if($user->getRoom()) {
				$user->getRoom()->actorRemove($user);
			}
			$this->removeSubscriber($user->getSubscriberTick());
		}

		$this->removeSubscriber($client->getInputSubscriber());
		
		// clean out the client
		socket_close($client->getSocket());
		$key = array_search($client, $this->clients);
		unset($this->clients[$key]);

		// reindex arrays
		$this->clients = array_values($this->clients);
		Debug::log($user." disconnected");
	}

	public function deployEnvironment($deploy_dir)
	{
		// Set the server instance so that the deploy scripts may reference it
		self::$instance = $this;

		// Include deploy scripts that will compose the races, skills, and spells.
		// After that, run all the area generation scripts, and validate success.
		Debug::log("Including deploy init scripts");
		$this->readDeploy($deploy_dir.'/init/');
		Debug::log("Initializing environment");
		foreach([
				'\Mechanics\Command\Command',
				'\Mechanics\Race',
				'\Mechanics\Ability\Ability'
			] as $required) {
			Debug::log("initializing ".$required);
			$required::runInstantiation();
		}
		Debug::log("Including deploy area scripts");
		$this->readDeploy($deploy_dir.'/areas/');
		$this->checkDeploySuccess();
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
			$this->fire(Event::EVENT_CONNECTED, new Client(socket_accept($this->socket)));
		}
	}

	protected function readDeploy($start)
	{
		$relative_path = dirname(__FILE__).'/../../'.$start;
		if(file_exists($relative_path)) {
			$d = dir($relative_path);
			while($cd = $d->read()) {
				$pos = strpos($cd, '.');
				if($pos === false) {
					$this->readDeploy($start.$cd);
					continue;
				}
				$ext = substr($cd, $pos+1);
				if($ext === 'php') {
					Debug::log("including deploy script: ".$cd);
					$anon = new Anonymous();
					$anon->_require_once($d->path.'/'.$cd);
				} else if($ext === 'area') {
					Debug::log("including deploy script: ".$cd);
					new Area($relative_path.'/'.$cd);
				}
			}
		} else {
			throw new Exception('Invalid deploy directory defined: '.$start);
		}
	}

	protected function checkDeploySuccess()
	{
		if(!Room::getStartRoom()) {
			throw new Exception('Start room not set');
		}
	}
}
?>
