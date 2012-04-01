<?php
namespace Mechanics;

trait Affectable
{
	protected $affects = [];

	public function addAffect(Affect $affect)
	{
		$this->affects[] = $affect;
	}

	public function removeAffect(Affect $affect)
	{
		$key = array_search($affect, $this->affects);
		if($key !== false) {
			unset($this->affects[$key]);
		}
	}

	public function getAffects()
	{
		return $this->affects;
	}
}
?>
