<?php
namespace Phud;

class Event
{
	protected $status = 'on';

	public function evaluate($trigger, $closure, &$a1, &$a2, &$a3, &$a4)
	{
		$closure($this, $trigger, $a1, $a2, $a3, $a4);
	}

	public function satisfy()
	{
		$this->status = 'satisfied';
	}

	public function kill()
	{
		$this->status = 'killed';
	}

	public function getStatus()
	{
		return $this->status;
	}
}
?>
