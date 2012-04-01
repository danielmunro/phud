<?php
namespace Phud;
use \Mechanics\Command\Command,
	\Living\User,
	\Mechanics\Ability\Ability,
	\Mechanics\Event\Event,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Broadcaster;

class Client
{
	use Broadcaster;

	private $user = null;
	private $unverified_user = null;
	private $socket = null;
	private $command_buffer = array();
	private $login = array('alias' => false);
	protected $input_subscriber = null;
	protected $last_input = '';
	
	public function __construct($socket)
	{
		$this->socket = $socket;
		$this->input_subscriber = new Subscriber(
			Event::EVENT_GAME_CYCLE,
			$this,
			function($subscriber, $server, $client) {
				$client->checkCommandBuffer();
				if(!is_resource($client->getSocket())) {
					$subscriber->kill();
				}
			}
		);
	}

	public function getInputSubscriber()
	{
		return $this->input_subscriber;
	}
	
	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
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
			$satisfied = $this->fire(Event::EVENT_INPUT, $args);
			if(!$satisfied) {
				Server::out($this, "\nHuh?"); // No subscriber could make sense of input
			}
			if($this->user) {
				Server::out($this, "\n".$this->user->prompt(), false);
			}
		}
	}
}
?>
