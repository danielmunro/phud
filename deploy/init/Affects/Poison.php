<?php
namespace Phud\Affects;
use Phud\Server;

class Poison extends Affect
{
	protected $affect = 'poison';
	protected $message_affect = 'Poisoned';
	protected $message_start = 'You suddenly feel ill';
	protected $message_end = 'Your illness passes';

	protected function initListeners()
	{
		$affect = $this;
		$this->listeners = [
			['tick',
			function($event, $poisoned, &$amount, &$modifier) use ($affect) {
				$amount = -($affect->getLevel() * 2);
				Server::out($poisoned, "You feel poison coursing through your blood.");
				$event->satisfy();
			}]
		];
	}
}
?>
