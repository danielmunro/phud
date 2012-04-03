<?php
namespace Phud\Affects;
use Phud\Server,
	Phud\Event\Subscriber,
	Phud\Event\Event;

class Poison extends Affect
{
	protected $affect = 'poison';
	protected $message_affect = 'Poisoned';
	protected $message_start = 'You suddenly feel ill';
	protected $message_end = 'Your illness passes';

	protected function initSubscribers()
	{
		$this->subscribers = [
			new Subscriber(
				Event::EVENT_TICK_ATTRIBUTE_MODIFIER,
				function($subscriber, $affectable, $poison, $attribute, &$modifier) {
					if($attribute === 'hp') {
						$modifier = -1;
						$subscriber->suppress();
						Server::out($affectable, "You feel poison coursing through your blood.");
					}
				}
			)
		];
	}
}
?>
