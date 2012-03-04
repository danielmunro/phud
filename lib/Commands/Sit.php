<?php
namespace Commands;
use \Mechanics\Actor;

class Sit extends Change_Disposition
{
	protected $alias = 'sit';

	public function perform(Actor $actor, $args = [])
	{
		$this->changeDisposition($actor, $args, 'sitting');
	}
}
?>
