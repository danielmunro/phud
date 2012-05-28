<?php
namespace Phud;
use Phud\Actors\User,
	Phud\Commands\Command;

class Client
{
	use Listener;

	private $user = null;
	private $unverified_user = null;
	private $socket = null;
	private $command_buffer = array();
	protected $last_input = '';
	
	public function __construct($socket)
	{
		$this->socket = $socket;
	}
	
	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
		$user->on(
			'input',
			function($event, $user, $args) {
				$command = Command::lookup($args[0]);
				if($command) {
					$command->tryPerform($user, $args);
					$event->satisfy();
				}
			}
		);
	}

	public function getSocket()
	{
		return $this->socket;
	}

	public function getLastInput()
	{
		return $this->last_input;
	}

	public function checkCommandBuffer()
	{
		$n = null;
		
		// Check for input from the socket
		$s = [$this->socket];
		socket_select($s, $n, $n, 0, 0);
		if($s) {
			$input = socket_read($s[0], 5120);
			if($input === '~')
				$this->command_buffer = [];
			else
				$this->command_buffer[] = trim($input);
		}

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
				Server::out($this, "\nHuh?"); // No listener could make sense of input
			}
			if($this->user) {
				Server::out($this, "\n".$this->user->prompt(), false);
			}
		}
	}
}
?>
