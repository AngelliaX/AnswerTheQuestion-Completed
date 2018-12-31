<?php

namespace tung;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\Item;


class Main extends PluginBase{

	public $repeattime = 5;
	/** @var string $answer */
	private $answer;
	/** @var PopupTask $task */
	private $task;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->reloadConfig();
		$this->getLogger()->info("ยงlยงdAnswer the question ");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{

		$q = $this->getConfig()->get("Questions");
		$a = $this->getConfig()->get("Answers");
		$t = $this->getConfig()->get("Money");
		$this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		switch(strtolower($command->getName())){
			case "ask":
				if(!isset($args[0])){
					$sender->sendMessage("Usage: /ask <number>");
					return false;
				}
				if(!is_numeric($args[0])){
					$sender->sendMessage("Usage: /ask <number>");
					return false;
				}
				$i = intval($args[0]);
				if(!in_array($i, array_keys($q))){
					$sender->sendMessage("Khong Tim Thay Cau Hoi");
					return false;
				}
				$this->task = new PopupTask($this, $q[$i]);
				$this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, $this->repeattime);
				$this->answer = $a[$i];
				return true;
			case "t":
				if(!isset($args[0])){
					$sender->sendMessage("Usage: /t <answer>");
					return false;
				}
				if(!isset($this->task)){
					$sender->sendMessage("Chua Co Cau Hoi");
					return false;
				}
				$answer = implode(" ", array_splice($args, 0, 999));
				if($answer === $this->answer){
					$this->getServer()->broadcastMessage("§d".$sender->getName() . "§e has the right answer.");
					EconomyAPI::getInstance()->addMoney($sender->getName(),$t);
					$sender->sendMessage("Ban Nhan Duoc ".$t." vi tra loi dung");
					$sender->getInventory()->addItem(Item::get(264, 0, 1));
					$this->getServer()->getScheduler()->cancelTask($this->task->getTaskId());
					$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "ask " . rand(0, count($q) - 1));
					return true;
				}else{
					$sender->sendMessage("Wrong answer.");
					return false;
				}
		}
		return false;
	}
}