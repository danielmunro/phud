<?php
namespace Phud;
use Phud\Actors\User,
	Phud\Commands\Command;

class Client implements \Beehive\Client
{
	use Listener;

	protected $user = null;
	protected $unverified_user = null;
	protected $command_buffer = array();
	protected $last_input = '';
	protected $id = '';
	protected $connection = null;
	protected $handshake = false;
	
	public function __construct(\Beehive\Server $server, $id, $connection)
	{
		$this->id = $id;
		$this->connection = $connection;
	}

	public function disconnect()
	{
		fclose($this->connection);
	}

	public function getHandshake()
	{
		return $this->handshake;
	}

	public function decodeIncoming($message)
	{
		return $message;
	}

	public function handshake($headers)
	{
		return $this->handshake = true;
	}

	public function getID()
	{
		return $this->id;
	}
	
	public function getUser()
	{
		return $this->user;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function write($message)
	{
		@stream_socket_sendto($this->connection, $message);
	}

	public function writeLine($message)
	{
		$this->write($message."\r\n");
	}

	public function setUser(User $user)
	{
		$this->user = $user;
		$user->on('input', function($event, $user, $args) {
			$command = Command::lookup($args[0]);
			if($command) {
				$command->tryPerform($user, $args);
				$event->satisfy();
			}
		});
		$this->on('pulse', function() {
			$this->user->fire('pulse');
			if($this->user->getDelay()) {
				$this->user->decrementDelay();
			}
			$t = $this->user->getTarget();
			if($t) {
				$this->write(ucfirst($t).' '.$t->getStatus().".\r\n\r\n".$this->user->prompt());
			}
		});
	}

	public function checkCommandBuffer()
	{
		$input = $this->readInput();

		if(empty($input)) {
			return;
		}

		$input === '~' ? $this->command_buffer = [] : $this->command_buffer[] = $input;

		// The client has a delay
		if(($this->user && $this->user->getDelay())) {
			return;
		}

		// Read from the user's command buffer
		$input = array_shift($this->command_buffer);

		// Check a repeat statement
		$input === '!' ? $input = $this->last_input : $this->last_input = $input;
		
		// Break down client input into separate arguments and evaluate
		$args = explode(' ', trim($input));
		$fire_from = empty($this->user) ? $this : $this->user;
		$satisfied = $fire_from->fire('input', $args);
		if($this->user) {
			if(!$satisfied) {
				$this->write("\nHuh?"); // No listener could make sense of input
			}
			$this->write("\n".$this->user->prompt());
		}
	}

	protected function readInput()
	{
		$n = null;
		$read = [$this->connection];
		$reading = stream_select($read, $n, $n, 0, 0);
		if($reading) {
			$input = stream_socket_recvfrom($read[0], 5120);
			return trim($input);
		}
	}
}
