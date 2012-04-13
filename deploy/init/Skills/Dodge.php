<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Race;

class Dodge extends Skill
{
	protected $alias = 'dodge';
	protected $proficiency = 'evasive';
	protected $required_proficiency = 25;
	protected $easy_modifier = ['dex'];
	protected $event = 'melee attacked';

	protected function initializeListener()
	{
		$skill = $this;
		$this->listener = function($fighter) {
			if($skill->perform($fighter)) {
				return 'satisfy';
			}
		};
	}

	public function modifyRoll(Actor $actor)
	{
		return ($actor->getSize() - Race::SIZE_NORMAL) * 10;
	}

	protected function success(Actor $actor, Actor $target, $args)
	{
		$args[0]->suppress();
		$args[1]->satisfyBroadcast();
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "You dodge ".$target."'s attack."],
			['actor' => $target, 'message' => $actor." dodges your attack."],
			['actor' => '*', 'message' => $actor." dodges ".$target."'s attack."]
		]);
	}
}
?>
