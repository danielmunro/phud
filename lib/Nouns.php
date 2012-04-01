<?php
namespace Phud;

trait Nouns
{
	protected $nouns = '';

	protected function assignNouns()
	{
		$this->nouns = implode(' ', array_filter(explode(' ', strtolower($this->getDefaultNouns())), function($noun) {
			return !in_array($noun, ['a', 'an', 'the']);
		}));
	}

	public function getNouns()
	{
		return $this->nouns;
	}
}
?>
