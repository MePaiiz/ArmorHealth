<?php
  
namespace armor;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\ArmorInventory;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use armor\Run;

class Main extends PluginBase implements Listener{
    //แก้ใขข้อความ
    public $tag = "§l§8(§cชุ§6ด§eเ§aก§bร§dา§cะ§8)§r ";
    public $usemes = "คุณสวมใส่ชุดเกราะแล้ว!";
    public $unusemes = "คุณถอดชุดเกราะแล้ว!";
    
    //แก้ใขการเพิ่มเลือด
    public $armor = [310 => 100];
    
	function onEnable() {
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    @mkdir($this->getDataFolder());
	    $this->health = new Config($this->getDataFolder()."health.yml", Config::YAML);
	    $this->getServer()->getScheduler()->scheduleRepeatingTask(new Run($this), 20 * 0.01);
	}
	function onJoin(PlayerJoinEvent $ev){
	    $p = $ev->getPlayer();
	    if(!$this->health->get($p->getName())){
            $this->health->set($p->getName(), 40);
            $this->health->save();
        }
        if($this->myHP($p) >= 40){
            $this->health->set($p->getName(), 40);
            $this->health->save();
        }
	}
	public function myHP($p){
        return $this->health->get($p->getName());
    }
	public function addHP($p, $count){
        $this->health->set($p->getName(), $this->health->get($p->getName()) + $count);
        $this->health->save();
    }
	public function delHP($p, $count){
        $this->health->set($p->getName(), $this->health->get($p->getName()) - $count);
        $this->health->save();
        $p->setHealth($p->getHealth() - 1);
    }
    public function updateHP(){
        foreach($this->getServer()->getOnlinePlayers() as $p){
            $p->sendTip($this->health->get($p->getName()));
            $p->setMaxHealth($this->health->get($p->getName()));
        }
    }
    function onArmorChange(EntityArmorChangeEvent $ev){
        $player = $ev->getEntity();
        if($player instanceof Player){
            if(isset($this->armor[$ev->getNewItem()->getId()])){
                $this->addHP($player, isset($this->armor[$ev->getNewItem()->getId()]) ? $this->armor[$ev->getNewItem()->getId()] : "");
                $player->sendMessage($this->tag.$this->usemes);
            }elseif(isset($this->armor[$ev->getOldItem()->getId()])){
                $this->delHP($player, isset($this->armor[$ev->getOldItem()->getId()]) ? $this->armor[$ev->getOldItem()->getId()] : "");
                $player->sendMessage($this->tag.$this->unusemes);
            }
        }
    }
}
