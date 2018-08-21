<?php

declare(strict_types=1);

namespace BeatsCore;

use BeatsCore\anti\{
    AntiAdvertising, AntiSwearing
};
use BeatsCore\commands\{
	FlyCommand, HeadCommand, HUDCommand, MaskCommand, NickCommand, RulesCommand, CustomPotion, WildCommand
};
use BeatsCore\tasks\{
	BroadcastTask, ClearLagTask, HUDTask, MaskTask
};
use BeatsCore\stacker\StackEvent;

use pocketmine\utils\TextFormat;
use pocketmine\block\Bedrock;
use pocketmine\block\BlockFactory;
use pocketmine\block\TNT;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use BeatsCore\custompotion\CustomPotionEvent;
use BeatsCore\potions\Potions;
use BeatsCore\items\ItemManager;
use BeatsCore\entity\EntityManager;
use pocketmine\utils\Config;

class Core extends PluginBase{

    public const PERM_RANK = "§l§8[§c+§8]§r §7You don't have permission to use this command!";
	public const PERM_STAFF = "§l§8[§c+§8]§r §7Only staff members can use this command!";
	public const USE_IN_GAME = "§l§8[§c+§8]§r §7Please use this command in-game!";

	public const MASK_DAMAGE_TO_NAME = [
		3 => "Steve Mask",
		4 => "Creeper Mask",
		5 => "Dragon Mask",
		6 => "Rabbit Mask",
		7 => "Witch Mask",
		8 => "Enderman Mask",
		9 => "Chef Mask",
		10 => "Miner Mask",
	];

	public const MASK_DAMAGE_TO_LORE = [
		3 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Steve Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Amazing Abilities",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Discover these for yourself ^_^",
			],
		4 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Creeper Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Rare",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Look someone in the eye, and explode! And gain 5 extra health!",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Regeneration I",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Gain 5 extra health",
			],
		5 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Dragon Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Legendary",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Gain many effects",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Regeneration I",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Gain 20 extra health",
			],
		6 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Rabbit Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Amazing Abilities",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Discover these for yourself ^_^",
			],
		7 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Witch Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Amazing Abilities",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Discover these for yourself ^_^",
			],
		8 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Enderman Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Rare",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Have a chance to look someone in the eye! And teleport!",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Night Vision II",
			],
		9 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Chef Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"You will never go hungry again!",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Saturarion",
			],
		10 => [
				TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Miner Mask",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "RARITY",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
				"Be able to mine like a drill!",
				"",
				TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
				TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed I",
			],
	];

	public static $TNT_timeouts = [];

    /** @var null */
    private static $instance = null;
    /** @var int[] */
    public $chat = [];
    /** @var bool[] */
    public $hud = [];
    /** @var Config */
    public $config;

    public static function getInstance() : self{
        return self::$instance;
    }

    public function onEnable() : void{
        // COMMANDS \\
        $this->getServer()->getCommandMap()->registerAll("BeatsCore", [
            new FlyCommand("fly", $this),
            new NickCommand("nick", $this),
            new RulesCommand("rules", $this),
            new CustomPotion("custompotion", $this),
            new WildCommand("wild", $this),
            new HUDCommand("hud", $this),
            new MaskCommand("mask", $this),
            new HeadCommand("head", $this),
        ]);
        // CONFIGS \\
        @mkdir($this->getDataFolder());
        $this->saveResource("changelog.txt");
        $this->saveResource("rules.txt");
        $this->saveResource("config.yml");
		$this->saveResource("settings.json");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        // EVENTS \\
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new CustomPotionEvent(), $this);
        $this->regManagers();
        $this->getServer()->getPluginManager()->registerEvents(new Potions(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiAdvertising($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiSwearing($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        // TASKS \\
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this), 2400);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new HUDTask($this), 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MaskTask($this), 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ClearLagTask($this), 20 * 60 * 5);
        // Block overrides -- @CortexPE
        BlockFactory::registerBlock(new class extends Bedrock {
        	public function getBlastResistance(): float{
				return 38;
			}
		}, true);
        BlockFactory::registerBlock(new class extends TNT {
        	public function onActivate(Item $item, Player $player = \null): bool{
				if($item->getId() === Item::FLINT_STEEL){
					if(isset(Core::$TNT_timeouts[$player->getId()])){
						$diff = time() - Core::$TNT_timeouts[$player->getId()];
						if($diff > 15){
							Core::$TNT_timeouts[$player->getId()] = time();
							$item->useOn($this);
							$this->ignite();
						} else {
							$player->sendMessage(TextFormat::BOLD . TextFormat::RED . "(!)" . TextFormat::RESET . TextFormat::RED . " TNT is in cooldown at the moment." . TextFormat::RESET . TextFormat::RED . "\nPlease wait for $diff seconds.");
						}
					} else {
						Core::$TNT_timeouts[$player->getId()] = time();
						$item->useOn($this);
						$this->ignite();
					}
					return true;
				}

				return false;
			}
		}, true);
    }

    public function regManagers() : void{
        EntityManager::Start();
        ItemManager::Start();
        new StackEvent($this);
    }
}
