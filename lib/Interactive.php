<?php
namespace Phud;

trait Interactive
{
	protected $alias = '';
	protected $long = '';
	protected $short = '';
	protected $nouns = '';

	public function getUsableByInput($collection, $input)
	{
		return $this->getUsables($collection, $input, true);
	}

	public function getManyUsablesByInput($collection, $input)
	{
		return $this->getUsables($collection, $input, false);
	}

	protected function getUsables($collection, $input, $scalar)
	{
		$found = [];
		foreach($collection as $c) {
			foreach($c->getNouns() as $n) {
				if(stripos($n, $input) === 0) {
					if($scalar) {
						return $c;
					} else {
						$found[] = $c;
					}
				}
			}
		}
		if($scalar && !$found) {
			return false;
		}
		return $found;
	}

	protected function assignNouns()
	{
		$use = $this->nouns;
		if(empty($use)) {
			$use = $this->short;
		} else if(empty($use)) {
			$use = $this->alias;
		}
		$this->nouns = array_filter(explode(' ', strtolower($use)), function($noun) {
			return !in_array($noun, ['a', 'an', 'the']);
		});
	}

	public function getNouns()
	{
		if(empty($this->nouns)) {
			$this->assignNouns();
		}
		return $this->nouns;
	}

	public function getAlias()
	{
		return $this->alias;
	}

	public function getLong()
	{
		return $this->long;
	}

	public function getShort()
	{
		return $this->short;
	}
}
?>
