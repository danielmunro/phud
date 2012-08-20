<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Sit extends Change_Disposition
{
	protected $alias = 'sit';

	public function perform(Actor $actor, $args)
	{
		$this->changeDisposition($actor, $args, 'sitting');
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [$args];
	}
}
