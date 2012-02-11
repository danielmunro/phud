<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Items\Equipment,
	\Mechanics\Command\Command;

class Remove extends Command
{
	protected $alias = 'remove';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = array())
	{
		$equipment = $actor->getEquipped()->getItemByInput($args);
		
		if($equipment instanceof Equipment)
		{
			$actor->getEquipped()->remove($equipment);
			Server::out($actor, 'You remove ' . $equipment->getShort() . '.');
		}
		else
			return Server::out($actor, 'You are not wearing anything like that.');
	}
}
?>
