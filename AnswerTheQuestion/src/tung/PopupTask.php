<?php

namespace tung;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;


class PopupTask extends Task{

	public $ques;

	private $owner;

	public function __construct(Plugin $owner, $ques){
		$this->ques = $ques;
		$this->owner = $owner;
	}


	public function onRun($Tick){
		$this->owner->getServer()->broadcastPopup($this->ques);
	}
}