<?php
    require_once('dbHandler.php');

    class itemHandler extends dbHandler {
        public $account_id;
        
        public function __construct() {
            $this->account_id = $_SESSION["login_id"];
        }
        public function checkChar($data) {
            $query = "SELECT `Obj_Id` FROM `characters` WHERE `account_id` = '" . $this->account_id . "' and `Obj_Id` = " . $data . " ;";
            $result = $this -> select($query);
            if(!empty($result)){
                return 1;
            }
            else{
                return 0;
            }            
            
        }
        public function itemIdentifier($item) {
            $sqlArmor = "SELECT * FROM `armor` WHERE item_id = " . $item . ";";
            $result = $this -> select($sqlArmor);
            if(empty($result) == true) {
                $sqlWeapon = "SELECT * FROM `weapon` WHERE item_id = " . $item . ";";
                $result = $this -> select($sqlWeapon); 
                if(empty($result) == true) {
                    $sqlEtc = "SELECT * FROM `etcitem` WHERE `item_id` = " . $item . ";";
                    $result = $this -> select($sqlEtc);
                    $data = array($result, 'etc');
                    return ($data);
                }
                else {
                    $data = array($result, 'weapon');
                    return ($data);
                }
                
            }
            else {
                    $data = array($result, 'armor');
                    return ($data);
            }
        }
        
        public function equipmentUIHandler($data) {
            if ($this->checkChar($data['char_id']) == 0){
                exit("Wrong Character Id!");
            }
            $accountId = $_SESSION["login_id"];
            $charId = $data['char_id'];
            //$itemId; //item_id
            //$loc; // paperdoll
            //$loc_data; // shirt = 0 head = 2 handkerchief = 3 rhand = 4 chest = 5 lhand = 6 gloves = 7 pants = 8 boots = 9 Rearring = 10, Learring = 11, necklace = 12, rring = 13 lring = 14
            $renderArray = array();
            
            $query = "SELECT * FROM `items` WHERE account_id = " . $accountId . " and owner_id = " . $charId .";";
            $result = $this -> select($query);
            foreach($result as $item) {
                if($item['loc'] == "paperdoll") {
                    $newItem = $this -> itemIdentifier($item['item_id']);
                    $picture = $newItem[0][0]["icon"];
                    switch($item['loc_data']) {
                        case 0:
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 9:
                        case 10:
                        case 11:
                        case 12:
                        case 13:
                        case 14:
                            $preEncode = array('location' => $item['loc_data'], 'icon' => $newItem[0][0]["icon"]);
                            array_push($renderArray, $preEncode);
                            break;
                        
                        case 15:
                            //fullarmor
                            $preEncode = array('location' => 4, 'icon' => $newItem[0][0]["icon"]);
                            $preEncode2 = array('location' => 7, 'icon' => $newItem[0][0]["icon"]);
                            array_push($renderArray, $preEncode);
                            array_push($renderArray, $preEncode2);
                            break;
                        
                        case 16:
                            //lrhand
                            $preEncode = array('location' => 3, 'icon' => $newItem[0][0]["icon"]);
                            $preEncode2 = array('location' => 5, 'icon' => $newItem[0][0]["icon"]);
                            array_push($renderArray, $preEncode);
                            array_push($renderArray, $preEncode2);
                            break;
                                              
                    }
                }
            }
            return $renderArray;
        }
        public function inventoryUIHandler($data) {
            if ($this->checkChar($data['char_id']) == 0){
                exit("Wrong Character Id!");
            }
            $sql = "SELECT * FROM `items` where `owner_id` = ".$data['char_id']." and `loc` = 'inv';";
            $result = $this->select($sql);
            $renderArray = array();
            
            foreach($result as $item) {
                    $newItem = $this -> itemIdentifier($item['item_id']);
                    $picture = $newItem[0][0]["icon"];
                    array_push($renderArray, array('object_id' => $item["object_id"], 'icon' => $newItem[0][0]["icon"], 'count' => $item['count']));
                
            }
            return $renderArray;
        }
        /*
            $loc_data; 
            hair = 0
            head = 1
            mask = 2

            rhand = 3
            chest = 4
            lhand = 5

            gloves = 6
            legs = 7
            feet = 8

            underwear = 9
            neck = 10

            rear,lear
            rear = 11
            lear = 12
            
            rfinger,lfinger
            rring = 13
            lring = 14

            fullarmor = 15
            lrhand = 16
            */
            
        
        public function addItemHandler($data, $equip) {
            
            
            $accountId = $_SESSION["login_id"];
            $ownerId = $data[0];
            $dup = 0;
            $result;
            $loc;
            $items = [];
            $count = 1;
             
            
            
            foreach($data[1] as $item) {
                if(is_array($item)){
                    $count = $item[1];
                    $item = $item[0];
                            
                }
                if($item != 0){
                //need $data owner id, itemid, count default = 1
                $itemIdentified = $this -> itemIdentifier($item);
                //!!!if($equip == 1 && $item != $data[1][3]) {
                    if($itemIdentified[1] == "weapon" || $itemIdentified[1] == "armor") {
                        $loc_data = $itemIdentified[0][0]['bodypart'];
                        $loc = "inv";
                        switch($loc_data) {
                            case "hair":
                                $loc_data = 0;
                                break;
                            case "head":
                                $loc_data = 1;
                                break;
                            case "mask":
                                $loc_data = 2;
                                break;
                            case "rhand":
                                $loc_data = 3;
                                break;
                            case "chest":
                                $loc_data = 4;
                                break;
                            case "lhand":
                                $loc_data = 5;
                                break;
                            case "gloves":
                                $loc_data = 6;
                                break;
                            case "legs":
                                $loc_data = 7;
                                break;
                            case "feet":
                                $loc_data = 8;
                                break;
                            case "underwear":
                                $loc_data = 9;
                                break;
                            case "neck":
                                $loc_data = 10;
                                break;
                            case "rear,lear":
                                $sql = "select * from `items` where `loc_data` = 11 and owner_id = $ownerId";
                                $checkSlot = $this -> select($sql);
                                if(empty($checkSlot)) {
                                    $loc_data = 11;    
                                }
                                else {
                                    $loc_data = 12;
                                }
                                break;
                            case "rfinger,lfinger":
                                $sql = "select * from `items` where `loc_data` = 13 and owner_id = $ownerId";
                                $checkSlot = $this -> select($sql);
                                if(empty($checkSlot)) {
                                    $loc_data = 13;    
                                }
                                else {
                                    $loc_data = 14;
                                }
                                break;
                            case "fullarmor":
                                $loc_data = 15;
                                break;
                            case "lrhand":
                                $loc_data = 16;
                                break;
                        }
                    
                        if($equip == 1) {
                            $sql = 'select * from `items` where `loc` = "paperdoll" and `loc_data` =' . $loc_data . ' and `owner_id` = ' . $ownerId;
                            $checkSlot = $this -> select($sql);
                            $loc = "paperdoll";
                            if(!empty($checkSlot[0]['loc'])) {
                                $checkSlotId = $checkSlot[0]['object_id'];
                                $sql = "UPDATE items set `loc` = 'inv' where `object_id` = $checkSlotId";
                                $this -> query($sql);
                            }
                        }
                    }
                    
                    $sql = "SELECT * from `items` where `owner_id` = $ownerId and `item_id` = $item";
                    if($itemIdentified[1] == "etc") {
                        $sql = "SELECT * from `items` where `owner_id` = $ownerId and `item_id` = $item";
                        $result = $this->select($sql);
                        if(!empty($result)) {
                            $curCount = $result[0]['count'] + $count;
                            $sql = "UPDATE `items` set `count` = $curCount where `owner_id` = $ownerId and `item_id` = $item";
                            $this->query($sql);
                            $dup = 1;
                        }
                        else {
                            $loc = "inv"; 
                            $loc_data = "-1";
                        }
                                
                    }
                    
                    
                //!!}
                //$count; // count if stackable and isset;
                //$loc; //paperdoll or inv 
                    if($dup != 1) {
                        $sql = "INSERT INTO `items` (`account_id`, `owner_id`, `item_id`, `count`, `loc`, `loc_data`) VALUES ($accountId, $ownerId, $item, $count, '$loc', $loc_data)";
                        $this -> query($sql);
                    }
                    $sql = "SELECT * from `items` where `owner_id` = $ownerId and `item_id` = $item";
                        $result = $this->select($sql);
                    }
            
                    
                    array_push($items, ['name' => $itemIdentified[0][0]['name'], 'count' => $count]);
            
            
    
            }
            
            return $items;
        }
        
    }

?>