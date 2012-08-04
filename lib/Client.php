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
	protected $server = null;
	protected $id = '';
	protected $connection = null;
	protected $buffer = null;
	protected $handshake = false;
	
	public function __construct(\Beehive\Server $server, $id, $connection)
	{
		$this->server = $server;
		$this->id = $id;
		$this->connection = $connection;
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

	public function getServer()
	{
		return $this->server;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function setBuffer($buffer)
	{
		$this->buffer = $buffer;
	}

	public function getBuffer()
	{
		return $this->buffer;
	}

	public function wrote($message)
	{
		return $message;
	}

	public function write($message)
	{
		event_buffer_write($this->buffer, $message, strlen($message));
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
			$user->fire('pulse');
			if($user->getDelay()) {
				$user->decrementDelay();
			}
			$t = $user->getTarget();
			if($t) {
				$this->write(ucfirst($t).' '.$t->getStatus().".\r\n\r\n".$user->prompt(), false);
			}
		});
	}

	public function getLastInput()
	{
		return $this->last_input;
	}

	public function appendCommandBuffer($input)
	{
		$input === '~' ? $this->command_buffer = [] : $this->command_buffer[] = trim($input);

		// Cases where we don't want to check the buffer, the client has a delay or the command buffer is empty
		if(($this->user && $this->user->getDelay()) || empty($this->command_buffer)) {
			return;
		}

		// Read from the user's command buffer
		$input = array_shift($this->command_buffer);
		if(!empty($input)) {
			// Check a repeat statement
			if(trim($input) === '!')
				$input = $this->last_input;
			else
				$this->last_input = $input;
			
			// Break down client input into separate arguments and evaluate
			$args = explode(' ', trim($input));
			$fire_from = empty($this->user) ? $this : $this->user;
			$satisfied = $fire_from->fire('input', $args);
			if(!$satisfied) {
				$this->write("\nHuh?"); // No listener could make sense of input
			}
			if($this->user) {
				$this->write("\n".$this->user->prompt());
			}
		}
	}
}
