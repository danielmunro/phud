<?php
namespace Commands;
use \Mechanics\Command\Command,
	\Living\Trainer,
	\Mechanics\Actor,
	\Mechanics\Server;

class Train extends Command
{
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	protected function __construct()
	{
		self::addAlias('train', $this);
	}

	public function perform(Actor $actor, $args = [])
	{
		$args[1] = strtolower($args[1]);
		switch($args[1]) {
			case 'str':
			case 'int':
			case 'wis':
			case 'dex':
			case 'con':
			case 'cha':
				break;
			default:
				return Server::out($actor, "What stat would you like to train (str, int, wis, dex, con, cha)?");
		}

		$actors = $actor->getRoom()->getActors();
		foreach($actors as $a) {
			if($a instanceof Trainer) {
				$a->train($actor, $args[1]);
				return;
			}
		}
		Server::out($actor, "A trainer is not here to help you.");
	}
}
?>
