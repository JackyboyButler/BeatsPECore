<?php
/**
 * Created by PhpStorm.
 * User: CortexPE
 * Date: 4/5/2018
 * Time: 3:10 AM
 */

namespace BeatsCore\commands;


use BeatsCore\Core;
use pocketmine\utils\TextFormat;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class HeadCommand extends PluginCommand {
	public function __construct(string $name, Plugin $owner){
		parent::__construct($name, $owner);
		$this->setDescription("Heads");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			$item = $sender->getInventory()->getItemInHand();
			if($item->getNamedTag()->hasTag("head", StringTag::class)){
				$target = $item->getNamedTag()->getString("head");
				$eco = EconomyAPI::getInstance();
				$money = $eco->myMoney($target) * 0.05;
				$eco->reduceMoney($target, $money, true);
				$eco->addMoney($sender, $money, true);
				$sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "(!) " . TextFormat::RESET . TextFormat::GREEN . "You got $" . $money . " from " . $target);
				$item->setCount(1);
				$sender->getInventory()->removeItem($item);
			}
		}
	}
}
