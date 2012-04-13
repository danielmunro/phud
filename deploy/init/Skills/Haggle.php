<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Haggle extends Skill
{
	protected $alias = 'haggle';
	protected $proficiency = 'speech';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['cha'];
	protected $event = 'buy';

	protected function initializeListener()
	{
		$this->listener = function($haggle, $buyer, $seller, $item, &$cost) {
		};
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function success(Actor $actor)
	{
	}
	
	protected function fail(Actor $actor)
	{
	}
}
?>
