<?php
    require_once('itemHandler.php');

    class charHandler extends itemHandler {
        /*
        0 = Human Fighter [0]
        10 = Human Wizard [0]
        18 = Elf Fighter [1]
        25 = Elf Mage [1]
        31 = Dark Elf Fighter [2]
        38 = Dark Elf Mage [2]
        44 = Orc Fighter [3]
        49 = Orc Mage [3]
        53 = Dwarf Fighter [4]
        */
        public function __construct() {
            parent::__construct();
        }
        public function getLevel($currentExp, $request){
            require_once('levelArray.php');
            $levelArr = new levelArray();
            $i = 0;
            $hit = 0;
            $setLevel;
            $result;
            reset($levelArr);
            $levelArr = current($levelArr);
            while($hit == 0) {
                if ($currentExp > 6299994999) {
                    $setLevel = 80; 
                    $exp = 6299994999;
                    $hit = 1;
                }
                else if (($levelArr[$i]["exp"] - $currentExp) <= 0 && ($levelArr[$i]["exp"] - $currentExp) < 6299994999) {
                    $i++;
                }
                else {
                    $setLevel = $levelArr[$i]["level"]-1; 
                    $hit = 1;
                }
            }
            if($request == "level") {
                $result = $setLevel;
            }
            if($request == "both") {
                $minXp = $levelArr[$setLevel]["exp"];
                $maxXp = $levelArr[$setLevel+1]["exp"];
                $result = ["currentLevel" => $setLevel, "minXp" => $minXp, "maxXp" => $maxXp];
            }
            
            return $result;

        }
        public function createChar($data) {
            //$sqlSelect = "SELECT * FROM `char_templates` WHERE `ClassId` = " . $data['class'] . ";";
            //$result = $this -> select($sqlSelect);
            $sqlSelect = "SELECT * FROM `characters` WHERE char_name = '" . $data['name'] ."';";
            $result = $this -> select($sqlSelect);
            
            if(empty($result) == true) {
                
                $sqlSelect2 = "SELECT * FROM `char_templates` WHERE `ClassId` = '" . $data['class'] ."';";
                $result = $this -> select($sqlSelect2);
                
                
                /*
                    $result[0]['STR']
                    $result[0]['CON']
                    $result[0]['DEX'] 
                    $result[0]['_INT']
                    $result[0]['WIT']
                    $result[0]['MEN']
                    $result[0]['P_ATT']
                    $result[0]['P_DEF']
                    $result[0]['M_ATT']
                    $result[0]['M_DEF']
                    $result[0]['P_SPD']
                    $result[0]['M_SPD']
                    $result[0]['ACC']
                    $result[0]['CRITICAL']
                    $result[0]['EVASION']
                    $result[0]['MOVE_SPD']
                    $result[0]['_LOAD']
                    $result[0]['canCraft']
                        
                    $result[0]['items1'] Pants
                    $result[0]['items2'] Chest
                    $result[0]['items3']
                    $result[0]['items4']
                    $result[0]['items5']
                */
              
                $sql = "INSERT INTO `characters` (`account_id`, `char_name`, `sex`, `level`, `classid`, `base_class`, `race`, `str`, `con`, `dex`, `_int`, `wit`, `men`, `pAtk`, `pDef`, `mAtk`, `mDef`, `pSpd`, `mSpd`, `acc`, `crit`, `evasion`, `runSpd`, `maxload`, `cancraft`) VALUES (" . $this->account_id . ", '" . $data['name'] . "'," . $data['sex'] . ", 1," . $data['class'] . "," . $data['class'] . "," . $data['race'] . "," . $result[0]['STR'] . "," . $result[0]['CON'] . "," . $result[0]['DEX'] . "," . $result[0]['_INT'] . "," . $result[0]['WIT'] . "," . $result[0]['MEN'] . "," . $result[0]['P_ATK'] . "," . $result[0]['P_DEF'] . "," . $result[0]['M_ATK'] . "," . $result[0]['M_DEF'] . "," . $result[0]['P_SPD'] . "," . $result[0]['M_SPD'] . "," . $result[0]['ACC'] . "," . $result[0]['CRITICAL'] . "," . $result[0]['EVASION'] . "," . $result[0]['MOVE_SPD'] . "," . $result[0]['_LOAD'] . "," . $result[0]['canCraft'] . ")";
                $results = $this -> query($sql);
                
                $ownerId = $this->lastId();
                
                $data = array($ownerId, array(array($result[0]['items1'],1), array($result[0]['items2'],1), array($result[0]['items3'],1), array($result[0]['items4'],1), array($result[0]['items5'],1)));
                
                $test = $this -> addItemHandler($data, 1);
                $data = array('char_id' => $ownerId);
                $this -> updateStats($data);
                
                $this->regenerateChar($ownerId);
                return;
            }
            else {
                return "name already exists";
            }
        }
        public function setActiveChar($data) {
            //security check
            if ($this->checkChar($data['char_id']) == 0){
                exit("Wrong Character Id!");
            }
            $activeChar = $data['char_id'];
            
            $queryReset = "UPDATE `characters` SET `online` = 0 WHERE `account_id` = $this->account_id";
            $this->query($queryReset);
            
            $querySelect = "UPDATE `characters` SET `online` = 1 WHERE `Obj_Id` = $activeChar";
            $this->query($querySelect);
            
            
            
        }
        public function regenerateChar($char) {
            
                $sql = "SELECT `maxHp`, `maxCp`, `maxMp` from characters where `obj_Id` = " . $char . ";";
                $maxBars = $this->select($sql);
                $sql = "UPDATE `characters` SET `curHp` = " . $maxBars[0]['maxHp'] . ", `curCp` = " . $maxBars[0]['maxCp'] . ", `curMp` = " . $maxBars[0]['maxMp'] . " where `obj_Id` = " . $char . ";"; 
                $this->query($sql);
            
        }
        public function updateStats($data) {
            if ($this->checkChar($data['char_id']) == 0){
                exit("Wrong Character Id!");
            }
            function issetor(&$var, $default = 1) {
                return isset($var) ? $var : $default;
            }
            require_once('statsArrays.php');
            $statsArr = new statsArrays();
            $char_id = $data['char_id'];
            $sql = "select * from  `char_templates` as ct
        LEFT join
    `characters` b
        on ct.ClassId = b.classid
        
        where b.`account_id` = " . $this->account_id  . " and b.`Obj_id` = " . $data['char_id'] . ";";
            $result = $this->select($sql);
            $LVL = $this->getLevel($result[0]['exp'], "level");
            $class = $result[0]['classid'];
            
            $CON = $result[0]['CON'];
            $DEX = $result[0]['DEX'];
            $INT = $result[0]['_INT'];
            $MEN = $result[0]['MEN'];
            $STR = $result[0]['STR'];
            $WIT = $result[0]['WIT'];
            
            
            // Yet To Be Reworked
            $LVLMOD=($LVL+89)/100;
            
            $fighter1MP = $statsArr->FIGHTER1MP;
            $fighter2MP = $statsArr->FIGHTER2MP;
            $fighter3MP = $statsArr->FIGHTER3MP;
            $mystic1MP = $statsArr->MYSTIC1MP;
            $mystic2MP = $statsArr->MYSTIC2MP;
            $mystic3MP = $statsArr->MYSTIC3MP;
            
            $CONMODIFIER = $statsArr->CONMODIFIER;
            $DEXMODIFIER = $statsArr->DEXMODIFIER;
            $INTMODIFIER = $statsArr->INTMODIFIER;
            $MENMODIFIER = $statsArr->MENMODIFIER;
            $STRMODIFIER = $statsArr->STRMODIFIER;
            $WITMODIFIER = $statsArr->WITMODIFIER;
            
            $CONMOD=$CONMODIFIER[$CON];
            $DEXMOD=$DEXMODIFIER[$DEX];
            $INTMOD=$INTMODIFIER[$INT];
            $MENMOD=$MENMODIFIER[$MEN];
            $STRMOD=$STRMODIFIER[$STR];
            $WITMOD=$WITMODIFIER[$WIT];
            
            //$resultStats = $this->select("call getStats($char_id)");
            $sql = "select sum(shield_def) as SDEF, sum(c.p_dam) as PATK, sum(b.p_def) as PDEF, sum(c.m_dam) as MATK, sum(b.m_def) as MDEF, sum(b.mp_bonus) as MPBONUS, sum(c.critical) as CRIT, sum(c.atk_speed) as ASPD from  `characters`, `items` as a
        LEFT join
    `armor` as b
        on a.item_id = b.item_id
        LEFT join 
    `weapon` as c
        on a.item_id = c.item_id
    
        where a.`owner_id` = $char_id and `loc` = 'paperdoll' and `characters`.`obj_id` = $char_id;";
            
            $resultStats = $this->select($sql);
            $resultStats = $resultStats[0];
            
            $sumSDEF = $resultStats['SDEF'];
            $sumPATK = $resultStats['PATK'];
            $sumPDEF = $resultStats['PDEF'];
            $sumMATK = $resultStats['MATK'];
            $sumMDEF = $resultStats['MDEF'];
            $sumMP = $resultStats['MPBONUS'];
            //$sumACC = $result[0]['ACC'];
            //$sumEVA = $result[0]['EVASION'];
            $sumCRIT = $result[0]['CRITICAL'] + $resultStats['CRIT'];
            $sumSPD = $result[0]['MOVE_SPD'];
            if($resultStats['ASPD'] < 300) {
                $sumASPD = 300;
            }
            else {
                $sumASPD = $resultStats['ASPD'];
            }
            if($sumSDEF != 0) {
                $shieldEvasion = -8;
                $shieldDefRate = 20;
            }
            else {
                $shieldEvasion = 0;
                $shieldDefRate = 0;
            }
            
            $sumCSPD = $result[0]['M_SPD'];
            $sumWEIGHT = $result[0]['_LOAD'];
            
            $testStats = array($sumPATK, $sumPDEF, $sumMATK, $sumMDEF, $sumMP, $sumSPD, $sumASPD, $sumCSPD, $sumWEIGHT);



            
                
            
            switch($class) {
                case 0:
                    //HF
                    $BaseHP=68.3+11.635*$LVL+0.065*($LVL*$LVL);
                    $CPMOD=0.4;
                    $BaseMP=$fighter1MP[$LVL];
                    break;
                case 1:
                    //WA
                    $BaseHP=-270+26.85*$LVL+0.15*($LVL*$LVL);
                    $CPMOD=0.8;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 2:
                case 88:
                    //GL
                    $BaseHP=-620.4+34.01*$LVL+0.19*($LVL*$LVL);
                    $CPMOD=0.9;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 3:
                case 89:
                    //WD
                    $BaseHP=-795.6+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.8;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 4:
                    //HK
                    $BaseHP=-210.3+24.165*$LVL+0.135*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 5:
                case 90:
                    //PA
                    $BaseHP=-604.5+32.22*$LVL+0.18*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 6:
                case 91:
                    //DA
                    $BaseHP=-604.5+32.22*$LVL+0.18*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 7:
                    //RO
                    $BaseHP=-170.5+22.375*$LVL+0.125*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 8:
                case 93:
                    //TH
                    $BaseHP=-477.1+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.55;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 9:
                case 92:
                    //HE
                    $BaseHP=-564.7+30.43*$LVL+0.17*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 10:
                    //HM
                    $BaseHP=85.7+15.215*$LVL+0.085*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic1MP[$LVL];
                    break;
                case 11:
                    //WI
                    $BaseHP=-73.5+22.375*$LVL+0.125*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 12:
                case 94:
                    //SOR
                    $BaseHP=-511.5+31.325*$LVL+0.175*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 13:
                case 95:
                    //NE
                    $BaseHP=-511.5+31.325*$LVL+0.175*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 14:
                case 96:
                    //WL
                    $BaseHP=-642.9+34.01*$LVL+0.19*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 15:
                    //CL
                    $BaseHP=-192.9+27.745*$LVL+0.155*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 16:
                case 97:
                    //BI
                    $BaseHP=-499.5+34.01*$LVL+0.19*($LVL*$LVL);
                    $CPMOD=0.9;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 17:
                case 98:
                    //PP
                    $BaseHP=-630.9+36.695*$LVL+0.205*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 18:
                    //EF
                    $BaseHP=76.4+12.53*$LVL+0.07*($LVL*$LVL);
                    $CPMOD=0.4;
                    $BaseMP=$fighter1MP[$LVL];
                    break;
                case 19:
                    //EK
                    $BaseHP=-242+26.85*$LVL+0.15*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 20:
                case 99:
                    //TK
                    $BaseHP=-680+35.8*$LVL+0.2*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 21:
                case 100:
                    //SW
                    $BaseHP=-767.6+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 22:
                    //EFS
                    $BaseHP=-202.2+25.06*$LVL+0.14*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 23:
                case 101:
                    //PW
                    $BaseHP=-552.6+32.22*$LVL+0.18*($LVL*$LVL);
                    $CPMOD=0.55;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 24:
                case 102:
                    //SR
                    $BaseHP=-640.2+34.01*$LVL+0.19*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 25:
                    //EM
                    $BaseHP=88.7+15.215*$LVL+0.085*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic1MP[$LVL];
                    break;
                case 26:
                    //EW
                    $BaseHP=-90.4+23.27*$LVL+0.13*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 27:
                case 103:
                    //SPS
                    $BaseHP=-572.2+33.115*$LVL+0.185*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 28:
                case 104:
                    //ELS
                    $BaseHP=-659.8+34.905*$LVL+0.195*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 29:
                    //EO
                    $BaseHP=-209.8+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 30:
                case 105:
                    //EE
                    $BaseHP=-647.8+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.9;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 31:
                    //DF
                    $BaseHP=80.5+13.425*$LVL+0.075*($LVL*$LVL);
                    $CPMOD=0.4;
                    $BaseMP=$fighter1MP[$LVL];
                    break;
                case 32:
                    //PK
                    $BaseHP=-257.8+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 33:
                case 106:
                    //SK
                    $BaseHP=-695.8+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 34:
                case 107:
                    //BD
                    $BaseHP=-827.2+40.275*$LVL+0.225*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 35:
                    //AS
                    $BaseHP=-218+26.85*$LVL+0.15*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 36:
                case 108:
                    //AW
                    $BaseHP=-568.4+34.01*$LVL+0.19*($LVL*$LVL);
                    $CPMOD=0.55;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 37:
                case 109:
                    //PR
                    $BaseHP=-656+35.8*$LVL+0.2*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 38:
                    //DM
                    $BaseHP=90.7+15.215*$LVL+0.085*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic1MP[$LVL];
                    break;
                case 39:
                    //DW
                    $BaseHP=-108.3+24.165*$LVL+0.135*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 40:
                case 110:
                    //SPH
                    $BaseHP=-546.3+33.115*$LVL+0.185*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 41:
                case 111:
                    //PS
                    $BaseHP=-656+35.8*$LVL+0.2*($LVL*$LVL);
                    $CPMOD=0.6;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 42:
                    //SO
                    $BaseHP=-227.7+29.535*$LVL+0.165*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 43:
                case 112:
                    //SHE
                    $BaseHP=-621.9+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.9;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 44:
                    //OF
                    $BaseHP=67.4+12.53*$LVL+0.07*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter1MP[$LVL];
                    break;
                case 45:
                    //OR
                    $BaseHP=-290.8+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 46:
                case 113:
                    //DE
                    $BaseHP=-816.4+39.38*$LVL+0.22*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 47:
                    //MO
                    $BaseHP=-251+26.85*$LVL+0.15*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 48:
                case 114:
                    //TY
                    $BaseHP=-776.6+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 49:
                    //OM
                    $BaseHP=79.7+15.215*$LVL+0.085*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic1MP[$LVL];
                    break;
                case 50:
                    //OS
                    $BaseHP=-218.8+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic2MP[$LVL];
                    break;
                case 51:
                case 115:
                    //OL
                    $BaseHP=-613+36.695*$LVL+0.205*($LVL*$LVL);
                    $CPMOD=0.9;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 52:
                case 116:
                    //WC
                    $BaseHP=-613+36.695*$LVL+0.205*($LVL*$LVL);
                    $CPMOD=0.5;
                    $BaseMP=$mystic3MP[$LVL];
                    break;
                case 53:
                    //DO
                    $BaseHP=67.4+12.53*$LVL+0.07*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter1MP[$LVL];
                    break;
                case 54:
                    //SC
                    $BaseHP=-290.8+28.64*$LVL+0.16*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 55:
                case 117:
                    //BH
                    $BaseHP=-816.4+39.38*$LVL+0.22*($LVL*$LVL);
                    $CPMOD=0.7;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
                case 56:
                    //AR
                    $BaseHP=-251+26.85*$LVL+0.15*($LVL*$LVL);
                    $CPMOD=0.8;
                    $BaseMP=$fighter2MP[$LVL];
                    break;
                case 57:
                case 118:
                    //WS
                    $BaseHP=-776.6+37.59*$LVL+0.21*($LVL*$LVL);
                    $CPMOD=0.8;
                    $BaseMP=$fighter3MP[$LVL];
                    break;
            }  
            
            $hp=issetor($BaseHP)*issetor($CONMOD)*issetor($BuffHP)+issetor($NECKLACEHP)+issetor($AddHP);
            $hp2=floor($hp);
    
            $BaseCP=issetor($BaseHP)*issetor($CPMOD);
            $cp=issetor($BaseCP)*issetor($CONMOD)*issetor($BuffCP)+issetor($AddCP);
            $cp2=floor($cp);
    
            $mp=issetor($BaseMP)*issetor($MENMOD)*issetor($BuffMP)+issetor($sumMP);
            $mp2=floor($mp);
    
            $patk=issetor($sumPATK)*issetor($STRMOD)*issetor($LVLMOD)*issetor($NECKLACEPATK)*issetor($MasteryPATK)*issetor($BuffPATK)+issetor($AddPATK);
            $patk2=floor($patk);
            
            $matk=issetor($sumMATK)*(issetor($INTMOD)*issetor($INTMOD))*(issetor($LVLMOD)*issetor($LVLMOD))*issetor($NECKLACEMATK)*issetor($BuffMATK)+issetor($AddMATK)+issetor($MasteryMATK);
            $matk2=floor($matk);
            //$sumPDEF=issetor($Cloak)+issetor($Helmet)+issetor($Upper)+issetor($Lower)+issetor($Glove)+issetor($Boot)+issetor($Und)+issetor($ShieldSigil);
            $pdef=issetor($sumPDEF)*issetor($LVLMOD)*issetor($BuffPDEF)+issetor($AddPDEF);
            $pdef2=floor($pdef);
                
            //$sumMDEF=issetor($Ring1)+issetor($Ring2)+issetor($Earring1)+issetor($Earring2)+issetor($Necklace);
            $mdef=issetor($sumMDEF)*issetor($LVLMOD)*issetor($MENMOD)*issetor($BuffMDEF)+issetor($AddMDEF);
            $mdef2=floor($mdef);
            
            $shieldpdef2=floor($sumSDEF);
            $shieldDefRate = $shieldDefRate*issetor($DEXMOD)*issetor($buffSRate);
            $shieldDefRate2 = floor($shieldDefRate);
                
            $Accuracy=(sqrt($DEX)*6)+issetor($LVL)+issetor($sumACC)+issetor($RINGOFQUEENACC)+issetor($RINGOFBAIUMACC)+issetor($RINGOFCOREACC)+issetor($OLYMPIADRINGACC)+issetor($BuffACC);
            if ($LVL>69){
                $Accuracy=$Accuracy+$LVL-69;
            }
            if ($LVL>77){
                $Accuracy=$Accuracy+$LVL-77;
            }
            $Accuracy2=floor($Accuracy);
    
            $Evasion=(sqrt($DEX)*6)+issetor($LVL)+issetor($sumEVA)+issetor($shieldEvasion)+issetor($BuffEVA);
            if ($LVL>69){
                $Evasion=$Evasion+$LVL-69;
            }
            if ($LVL>77){
                $Evasion=$Evasion+$LVL-77;
            }
            $Evasion2=floor($Evasion);
                
            $basecritical = issetor($sumCRIT)*issetor($DEXMOD);
            $finalcritical=issetor($basecritical)+issetor($AddCRIT)+issetor($subcritical);
            if ($finalcritical > 500){
                $finalcritical=500;
            }
            $finalcritical2=floor($finalcritical);
    
            $Speed=issetor($sumSPD)*issetor($DEXMOD)*issetor($BuffSPEED)+issetor($AddSPEED);
            $Speed2=floor($Speed);
    
            $atkspd=issetor($sumASPD)*issetor($DEXMOD)*issetor($RINGOFBAIUMASPD)*issetor($BuffASPD)+issetor($AddASPD);
            if ($atkspd>1500) {
                $atkspd=1500;
            }
            $atkspd2=floor($atkspd);
    
            $castingspd=issetor($sumCSPD)*issetor($WITMOD)*issetor($RINGOFBAIUMCAST)*issetor($BuffCAST)+issetor($AddCAST);
            $castingspd2=floor($castingspd);
    
    
            $weightlimit2=issetor($sumWEIGHT)*issetor($CONMOD);
    
            $basehpregen;
            $basempregen;
            $basecpregen;
            
            if ($LVL>0 && $LVL<11){
                $basehpregen=1.95+($LVL/20);
                $basempregen=0.9;
                $basecpregen=2;
            }
            if ($LVL>10){
                $basehpregen=1.4+(LVL/10);
            }
            if ($LVL>10 && $LVL<21){
                $basempregen=1.2;$basecpregen=2.5;
            }
            if ($LVL>20 && $LVL<31){
                $basempregen=1.5;$basecpregen=3.5;
            }
            if ($LVL>30 && $LVL<41){
                $basempregen=1.8;$basecpregen=4.5;
            }
            if ($LVL>40 && $LVL<51){
                $basempregen=2.1;$basecpregen=5.5;
            }
            if ($LVL>50 && $LVL<61){
                $basempregen=2.4;$basecpregen=6.5;
            }
            if ($LVL>60 && $LVL<71){
                $basempregen=2.7;$basecpregen=7.5;
            }
            if ($LVL>70 && $LVL<81){
                $basempregen=3;$basecpregen=8.5;
            }
            $hpregen=($basehpregen*$CONMOD*$LVLMOD*issetor($BuffHPR)*issetor($movemode)+issetor($AddHPR))*issetor($BuffHPR2);
            $hpregen2=floor($hpregen*10)/10;
            $cpregen=$basecpregen*$CONMOD*$LVLMOD*issetor($BuffCPR)*issetor($movemode)+issetor($AddCPR);
            $cpregen2=floor($cpregen*10)/10;
            $mpregen=($basempregen*$MENMOD*$LVLMOD*issetor($BuffMPR)*issetor($movemode)+issetor($AddMPR))*issetor($BuffMPR2)+issetor($AddMPR2);
            $mpregen2=floor($mpregen*10)/10;
            
            
            $hp2 = round($hp2);
            $cp2 = round($cp2);
            $mp2 = round($mp2);
            $Accuracy2 = round($Accuracy2);
            $finalcritical2 = round($finalcritical);
            $Evasion2 = round($Evasion2);
            $matk2 = round($matk2);
            $mdef2 = round($mdef2);
            $castingspd2 = round($castingspd2);
            $patk2 = round($patk2);
            $pdef2 = round($pdef2);
            $atkspd2 = round($atkspd2);
            $Speed = round($Speed);
            
            
            $sql = "UPDATE `characters` SET `level` = $LVL, `maxHP` = $hp2, `maxCP` = $cp2, `maxMP` = $mp2, `sDef` = $shieldpdef2, `sRate` = $shieldDefRate2,  acc = $Accuracy2, `crit` = $finalcritical2, `evasion` = $Evasion2, `mAtk` = $matk2, `mDef` = $mdef2, `mSpd` = $castingspd2, `pAtk` = $patk2, `pDef` = $pdef2, `pSpd` = $atkspd2, `runspd` = $Speed WHERE `obj_id` = $char_id;";
            
            $result2 = $this->query($sql);
            
            
            return;
        } 
        
    } 
?>