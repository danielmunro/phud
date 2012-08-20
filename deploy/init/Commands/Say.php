<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Say extends Command
{
	protected $alias = 'say';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Say what?";

	public function perform(Actor $actor, $message)
	{
		foreach($actor->getRoom()->getActors() as $a) {
			$a->notify(($a === $actor ? "You say" : ucfirst($actor)." says").", \"".$message."\"");
		}
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [recombine($args, 1)];
	}
}
