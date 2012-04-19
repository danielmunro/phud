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
	
	public function perform(Actor $actor, $args = [])
	{
		$s = sizeof($args);
		if($s === 2) {
			$spell = Ability::lookup(implode(' ', array_slice($args, 1)));
		} else if($s > 2) {
			$spell = Ability::lookup(implode(' ', array_slice($args, 1, $s-2)));
		}

		// Check if the spell exists
		if(empty($spell) || !($spell['lookup'] instanceof aSpell)) {
			return Server::out($actor, "That spell does not exist in this realm.");
		}

		// Does the caster actually know the spell?
		if(!in_array($spell['alias'], $actor->getAbilities())) {
			return Server::out($actor, "You do not know that spell.");
		}

		$actor->fire('casting', $spell);

		$spell['lookup']->perform($actor, $args);
	}
}
?>
