<?php
namespace Living;
use \Mechanics\Actor;
use \Mechanics\Dbr;
class Shopkeeper extends Mob
{

	protected $alias = 'a shopkeeper';
	protected $long = 'a shopkeeper stands here.';
	protected $nouns = 'shopkeeper';
	protected $list_item_message = "Here's what I have in stock now.";
	protected $no_item_message = "I'm not selling that.";
	protected $not_enough_money_message = "Come back when you have more money.";
	
	public function setListItemMessage($message)
	{
		$this->list_item_message = $message;
	}
	
	public function getListItemMessage()
	{
		return $this->list_item_message;
	}
	
	public function setNoItemMessage($message)
	{
		$this->no_item_message = $message;
	}
	
	public function getNoItemMessage()
	{
		return $this->no_item_message;
	}
	
	public function setNotEnoughMoneyMessage($message)
	{
		$this->not_enough_money_message = $message;
	}
	
	public function getNotEnoughMoneyMessage()
	{
		return $this->not_enough_money_message;
	}
}
?>
