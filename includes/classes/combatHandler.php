<?php
    require_once('charHandler.php');

    class combatHandler extends charHandler {
        
        public function __construct() {
            parent::__construct();
        }
        public function combatWon($bars, $mobId, $pId) {
            
            $pId = $_SESSION['charId'];
            $mobId = $_SESSION['mobId'];
            $oldLevel = $_SESSION['oldLevel'];
            
            $sql = "SELECT `exp` FROM `npc` WHERE `id` = $mobId";
            $exp = $this->select($sql);
            $exp = $exp[0]['exp'];
            $sql = "UPDATE `characters` SET `curHp` = $bars[0], `curMp` = $bars[1], `curCp` = $bars[2], `exp` = `exp` + $exp  WHERE `account_id` = $this->account_id && `obj_id` = $pId";
            $this->query($sql);
            
            $sql = "SELECT `exp` FROM `characters` WHERE `obj_Id` = $pId";
            $curExp = $this->select($sql);
            $curExp = $curExp[0]['exp'];
            
            $level = $this->getLevel($curExp, "level");
            if($level != $oldLevel){
                $data = ['char_id' => $pId]; 
                $this->updateStats($data);
            }
            
            $sqlLoot = "SELECT * FROM `droplist` WHERE `mobId` = $mobId";
            $lootResult = $this->select($sqlLoot);
            $drops = array();
            $drops[0] = $pId;                 
            $drops[1] = array();
            $loot = array();
            if(!empty($lootResult)){
                foreach($lootResult as $drop) {
                    $hit = rand(1, 1000000);
                    if($hit < $drop['chance']) {
                        $count = rand($drop['min'], $drop['max']);
                        array_push($drops[1], array($drop['itemId'], $count));
                    }
                }
                if(!empty($drops[1])){
                    $result = $this->addItemHandler($drops, 0);
                }
                array_push($loot, $result);
            }
            return $loot;
        }
        public function addMob($level, $vary, $faction = 0) {
            $mobs = array();
            //$level = $data['level']; // selected level
            if($faction == 0) {
                $sql = "SELECT * FROM `npc` where `type` = 'L2Monster' and `level` between " . ($level-$vary). " and " . ($level+$vary) . " and `class` LIKE 'Monster%';";
            }
            else {
                $sql = "SELECT * FROM `npc` where `type` = 'L2Monster' and `level` between " . ($level-$vary). " and " . ($level+$vary) . " and `class` LIKE 'Monster%' and `faction_id` = " . $faction . ";";
            }
            $mobsResult = $this->select($sql); 
            
            $mobPick = rand(0, count($mobsResult)-1);
            if($mobPick < 0) {
                $mobPick = 0;
            }
            $mob = $mobsResult[$mobPick];
            
            
            $sqlMobWeapon = "SELECT weaponType, sum(critical) as crit, sum(shield_def_rate) as shieldDefRate, sum(shield_def) as shieldDef FROM `weapon` where `item_id` = " . $mob['rhand'] ." or `item_id` = " . $mob['lhand'] ." ORDER BY CASE WHEN weaponType = 'none' then 1 else 0 end;";
            $mobWeapon = $this->select($sqlMobWeapon);
            
            if($mobWeapon[0]['crit'] == NULL){
                $mobWeapon[0]['crit'] = 0;
            }
            $mobAcc = (sqrt(30)*6)+$mob['level'];
            $mobEva = (sqrt(30)*6)+$mob['level'];
            $mobCrit = 80*1.10;

            if($mobCrit > 500){
                $mobCrit=500;
            }
            $_SESSION['mobId'] = $mob['id'];
            $mobInfo = ['id' => $mob['id'], 'name' => $mob['name'],'level' => $mob['level'], 'hp' => $mob['hp'], 'mp' => $mob['mp'], 'shieldDef' => $mobWeapon[0]['shieldDef'], 'shieldDefRate' => $mobWeapon[0]['shieldDefRate'], 'acc' => $mobAcc, 'eva' => $mobEva, 'crit' => $mobCrit, 'mAtk' => $mob['matk'], 'mDef' => $mob['mdef'], 'cSpd' => $mob['matkspd'], 'pDef' => $mob['pdef'], 'pAtk' => $mob['patk'], 'pSpd' => $mob['atkspd'], 'runSpd' => $mob['runspd'], 'attackRange' => $mob['attackrange'], 'weaponType' => $mobWeapon[0]['weaponType'], 'factionRange' => $mob['faction_range'], 'exp' => $mob['exp']];
            array_push($mobs, $mobInfo);
            return $mobs;
        }
        public function getCharactersInfo() {
            $sqlChars = "SELECT * FROM `characters` WHERE account_id = $this->account_id and `online` = 1;";
            $chars = $this->select($sqlChars);
            //session sum chars?
            $participants = array();
            
            foreach ($chars as $char) {
                
                $crit = $char['crit'];
                if($crit>500){
                    $crit=500;
                }
                
                $_SESSION['charId'] = $char['obj_Id'];
                $_SESSION['oldLevel'] = $char['level'];
                $charInfo = ['Id' => $char['obj_Id'], 'level' => $char['level'], 'name' => $char['char_name'], 'maxHp' => $char['maxHp'], 'curHp' => $char['curHp'], 'maxMp' => $char['maxMp'], 'curMp' => $char['curMp'], 'curExp' => $char['exp'], 'shieldDef' => $char['sDef'], 'shieldDefRate' => $char['sRate'], 'acc' => $char['acc'], 'crit' => $crit, 'eva' => $char['evasion'], 'mAtk' => $char['mAtk'], 'mDef' => $char['mDef'], 'mSpd' => $char['mSpd'], 'pDef' => $char['pDef'], 'pAtk' => $char['pAtk'], 'pSpd' => $char['pSpd'], 'runSpd' => $char['runSpd']];
                array_push($participants, $charInfo); //session?
            }
            return $participants;
        }
        public function combatDelay($weapon, $mobRange) {
            
            $combatDelay = 0;
            
            if($mobRange >= 400 && $weapon != "bow") {
                $combatDelay = 3;
            }
            else if ($weapon == "bow" && $mobRange < 400) {
                $combatDelay = -3;
            }
            else {
                $combatDelay = 0;
            }
            return $combatDelay;
            
        }
        public function blockRate($dShieldRate, $aWeapon) {
            if($aWeapon == "bow") {
                $blockRate = $dShieldRate*3;
            }
            else {
                $blockRate = $dShieldRate;
            }
            return $blockRate;
            
        }
        public function damageWindow($aPAtk, $dDef, $dShieldDef, $ranDmg = 0.1) {
             $damage = $aPAtk*70/($dDef);
             $minDamage = floor($damage*(1-$ranDmg));
             $maxDamage = floor($damage*(1+$ranDmg));
             
             $bDamage = $aPAtk*70/($dDef+$dShieldDef);
             $bMinDamage = floor($bDamage*(1-$ranDmg));
             $bMaxDamage = floor($bDamage*(1+$ranDmg));
             
             $damageWindow = array($minDamage, $maxDamage, $bMinDamage, $bMaxDamage);
             
             return $damageWindow; 
        }
        public function hitChance($aAcc, $dEva) {
            $hitChance = $aAcc-$dEva;
            if ($hitChance<-24.5){
                $hitChance=0.275;     
            }
            else if ($hitChance<-20 && $hitChance>=-24.5){
                $hitChance=0.25+(($hitChance+25)*5/100);
            }
            else if ($hitChance<-15 && $hitChance>=-20){
                $hitChance=0.50+(($hitChance+20)*3/100);
                }
            else if ($hitChance<-10 && $hitChance>=-15){
                $hitChance=0.65+(($hitChance+15)*2/100);
                }
            else if ($hitChance<-0 && $hitChance>=-10){
                $hitChance=0.75+(($hitChance+10)*1.5/100);
                }
            else if ($hitChance<5 && $hitChance>=0){
                $hitChance=0.905+($hitChance*1/100);
                }
            else if ($hitChance<10 && $hitChance>=5){
                $hitChance=0.955+(($hitChance-5)*0.5/100);
                }
            else if ($hitChance>10){
                $hitChance=0.98;
            }
            return $hitChance;
        }
        
        public function getCombatInfo($levelRange) {
            $characters = $this->getCharactersInfo(); ///////////edit 1,2 ////////////
            
            $participants = [];
                
            
            
            foreach($characters as $char){ 
                
                $sqlWeapon = "select * from `weapon` as w left join `items` as  i on i.item_id = w.item_id where i.owner_id =" . $char['Id'] . " and i.loc = 'paperdoll' ORDER BY CASE WHEN weaponType = 'none' then 1 else 0 end;";
            
                $weapon = $this->select($sqlWeapon);
                $weaponType = $weapon[0]['weaponType']; 
                $pLevelInfo = $this->getLevel($char['curExp'], "both");
                $mobs = $this->addMob($pLevelInfo['currentLevel'], $levelRange);
                
                if ($weaponType=="dualfist"||$weaponType=="dual") {
                    $char['pAtk']=$char['pAtk']/2;
                    $char['pSpd']=$char['pSpd']*2;
                }
                
                $pHitsPerSecond = $char['pSpd']/500;
                $charSummary = ["pId" => $char['Id'], "pName" => $char['name'], "pMaxHp" => $char['maxHp'], "pCurHp" => $char['curHp'], "pMaxMp" => $char['maxMp'], "pCurMp" => $char['curMp'], "pHitsPerSecond" => $pHitsPerSecond, "curExp" => $char['curExp'], "expWindow" => $pLevelInfo];
                
                foreach($mobs as $mob) {
                    
                    $combatDelay = $this->combatDelay($weaponType, $mob['attackRange']);
                    
                    $pHitChance = $this->hitChance($char['acc'], $mob['eva']);
                    $pBlockRate = $this->blockRate($char['shieldDefRate'], $mob['weaponType']);
                    $pDamageWindow = $this->damageWindow($char['pAtk'], $mob['pDef'], $mob['shieldDef'], ($weapon[0]['rnd_dam']/100));
                    $playerCritChance = $char['crit']/10;
                    
                    $mHitChance = $this->hitChance($mob['acc'], $char['eva']);
                    $mBlockRate = $this->blockRate($mob['shieldDefRate'], $weaponType);
                    $mDamageWindow = $this->damageWindow($mob['pAtk'], $char['pDef'], $char['shieldDef']);
                    $mobCritChance = $mob['crit']/10;
                    
                    $mHitsPerSecond = $mob['pSpd']/500;
                    
                    $combatSummary = ["combatDelay" => $combatDelay, "pHitChance" => $pHitChance*100, "pBlockRate" => $pBlockRate, "pDamageWindow" => $pDamageWindow, "playerCritChance" => $playerCritChance, "mHitChance" => $mHitChance*100, "mBlockRate" => $mBlockRate, "mDamageWindow" => $mDamageWindow, "mobCritChance" => $mobCritChance, "mHitsPerSecond" => $mHitsPerSecond, "mName" => $mob['name'], "mLevel" => $mob['level'], "mHp" => $mob['hp'], "mMp" => $mob['mp'], "mXp" => $mob['exp'], "mobId" => $mob['id']];
                    array_push($charSummary, $combatSummary);
                }
     
                $participants = ($participants + [$char['Id'] => $charSummary]);
            }
            return $participants;
            //return var_dump(Array("lv40 Temple Knight",40,"player",35,1.15,1.04,1.1,0.9,"Sword",0,80,379,"C","bu",165,1.085,1,19.1,394,1,73.5,0,-8,73.3,0,1,0,1,0,206,1,0,false,1.94731,0,0,"SR"));
            
            
            //return array($characters, $mobs);
                
                // TODO: crit damamge
        }
        public function killHandler() {
                //$curExp = $char['exp'];
                //add mob exp
                // set exp
                //$loot
            
        }
        
        public function deathHandler() {
            
        }

    }