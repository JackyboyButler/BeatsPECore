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
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class MaskCommand extends PluginCommand {
	private $owner;
	public function __construct(string $name, Plugin $owner){
		parent::__construct($name, $owner);
		$this->owner = $owner;
		$this->setDescription("Masks!");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			if($sender->isOp()){
				if(isset($args[0])){
					if(strtolower($args[0]) == "charm"){
						$amt = 1;
						if(isset($args[2])){
							$amt = intval($args[2]);
						}
						if(isset($args[1])){
							if(strtolower($args[1]) == "all"){
								foreach($this->owner->getServer()->getOnlinePlayers() as $player){
									$player->sendMessage(TextFormat::GREEN . "Given Mask Charm");
									$i = Item::get(Item::ENCHANTED_BOOK, 101, $amt);
									$i->setCustomName(TextFormat::RESET . TextFormat::YELLOW . "Mask Charm");
									$player->getInventory()->addItem($i);
								}
							} else {
								$player = $this->owner->getServer()->getPlayer($args[1]);
								if($player instanceof Player){
									$player->sendMessage(TextFormat::GREEN . "Given Mask Charm");
									$i = Item::get(Item::ENCHANTED_BOOK, 101, $amt);
									$i->setCustomName(TextFormat::RESET . TextFormat::YELLOW . "Mask Charm");
									$player->getInventory()->addItem($i);
								}
							}
						} else {
							$sender->sendMessage(TextFormat::GREEN . "Given Mask Charm");
							$i = Item::get(Item::ENCHANTED_BOOK, 101, 1);
							$i->setCustomName(TextFormat::RESET . TextFormat::YELLOW . "Mask Charm");
							$sender->getInventory()->addItem($i);
						}
					} else {
						$amt = 1;
						if(isset($args[2])){
							$amt = intval($args[2]);
						}
						$target = $sender;
						if(isset($args[1])){
							$p = $this->owner->getServer()->getPlayer($args[1]);
							if($p instanceof Player){
								$target = $p;
							}
						}
						foreach(Core::MASK_DAMAGE_TO_NAME as $dmg => $name){
							$cmdName = strtolower(explode(" ", $name)[0]);
							if(strtolower($args[0]) == $cmdName){
								$mask = Item::get(Item::SKULL, $dmg, $amt);
								$mask->setCustomName(Core::MASK_DAMAGE_TO_NAME[$dmg]);
								$mask->setLore(Core::MASK_DAMAGE_TO_LORE[$dmg]);
								$target->getInventory()->addItem($mask);
								$target->sendMessage(TextFormat::GREEN . "Given " . $name);
								break;
							}
						}
					}
				}
			}
		}
	}
}
