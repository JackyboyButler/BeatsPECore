<?php

declare(strict_types=1);

namespace BeatsCore\commands;

use BeatsCore\Core;
use BeatsCore\tasks\HUDTask;
use pocketmine\command\{
    Command, CommandSender, PluginCommand
};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;

class HUDCommand extends PluginCommand{

    /** @var Core */
    private $plugin;

    public function __construct(string $name, Core $plugin){
        parent::__construct($name, $plugin);
        $this->setDescription("Enable OR Disable your HUD!");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$sender instanceof Player) return false;
        if(isset($args[0])){
            switch(strtolower($args[0])){
                case "on":
                    if(isset($this->plugin->hud[$sender->getName()])) return false;
					$this->plugin->hud[$sender->getName()] = true;
                    $sender->sendMessage("§8§l(§a!§8)§r §7HUD Enabled!");
                    break;
                case "off":
                    if(!isset($this->plugin->hud[$sender->getName()])) return false;
                    unset($this->plugin->hud[$sender->getName()]);
                    $sender->sendMessage("§8§l(§a!§8)§r §7HUD Disabled!");
                    break;
				default:
					$sender->sendMessage("§8§l(§6!§8)§r §7Usage§8:§a /hud <on|off>");
					break;
            }
        }else{
            $sender->sendMessage("§8§l(§6!§8)§r §7Usage§8:§a /hud <on|off>");
            return false;
        }
        return true;
    }
}
