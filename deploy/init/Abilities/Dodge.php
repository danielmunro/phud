<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Races\Race;

class Dodge extends Skill
{
	protected $alias = 'dodge';
	protected $proficiency = 'evasive';
	protected $required_proficiency = 25;
	protected $easy_modifier = ['dex'];
	protected $event = 'attacked';

	protected function initializeListener()
	{
		$skill = $this;
	}

	public function modifyRoll(Actor $actor)
	{
		return ($actor->getRace()->getSize() - Race::SIZE_NORMAL) * 10;
	}

	protected function success(Actor $actor, Actor $target, $args)
	{
		$actor->getRoom()->announce([
			['actor' => $actor, 'message' => "You dodge ".$target."'s attack."],
			['actor' => $target, 'message' => $actor." dodges your attack."],
			['actor' => '*', 'message' => $actor." dodges ".$target."'s attack."]
		]);
	}
}
?>
