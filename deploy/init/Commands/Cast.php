<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Abilities\Ability,
	Phud\Abilities\Spell as aSpell;

class Cast extends Command
{
	protected $alias = ['cast', 11];
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	
	public function perform(Actor $actor, $args = [], Subscriber $command_subscriber)
	{
		$s = sizeof($args);
		if($s === 2) {
			$arg_spell_casting = Ability::lookup(implode(' ', array_slice($args, 1)));
		} else if($s > 2) {
			$arg_spell_casting = Ability::lookup(implode(' ', array_slice($args, 1, $s-2)));
		}

		// Check if the spell exists
		if(empty($arg_spell_casting) || !($arg_spell_casting['lookup'] instanceof aSpell)) {
			return Server::out($actor, "That spell does not exist in this realm.");
		}

		// Does the caster actually know the spell?
		if(!in_array($arg_spell_casting['alias'], $actor->getAbilities())) {
			return Server::out($actor, "You do not know that spell.");
		}

		// This event announces the beginning of battle, allowing for the target to have an observer that cancels the fight
		// @TODO move this to the ability classes
		/**
		if($arg_spell_casting['lookup']->isOffensive()) {
			if($actor->reconcileTarget($target)) {
				$target->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
				if($command_subscriber->isSuppressed()) {
					return;
				}
			}
		}
		*/

		$arg_spell_casting['lookup']->perform($actor, $args);
	}
}
?>
