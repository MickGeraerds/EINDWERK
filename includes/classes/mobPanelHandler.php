<?php
class mobPanelHandler extends dbHandler {
    
    public function showMe() {   
        /* Class icons */
        $query = "SELECT `id` FROM `class_list`";
        
        $array = array();
        
        $result = $this -> select($query);
        
        foreach($result as $class) {
            array_push($array, '<span> <img src="images/classes/'.$class["id"].'.png" alt=""></span>');
        }
        return $array; 
        
        
        
        /* Skill Icons */
        //$query = "CALL classSkills(118, 80)";;
        
        
        /* Armor Sets
        $item_id = 5783;
        $query = "SELECT * FROM `armorsets` where `chest` = $item_id or `legs` = $item_id or `head` = $item_id or `gloves` = $item_id or `feet` = $item_id or `shield` = $item_id";
        
        
        
        $result = $this -> select($query);
        
        $chest = $result[0]["chest"];
        $legs = $result[0]['legs'];
        $head = $result[0]['head'];
        $gloves = $result[0]['gloves'];
        $feet = $result[0]['feet'];
        $shield = $result[0]['shield'];
        
        $query = "SELECT * FROM `armor` where item_id = $chest or item_id = $legs or item_id = $head or item_id = $gloves or item_id = $feet or item_id = $shield";
        */
        
        /* return Icons from icon tables (not for classes)
        $result = $this -> select($query);
        
        $array = array();
        
        foreach($result as $class) {
            
            
            
            $img = $class["icon"];
            
            array_push($array, '<img src="images/icons/'.$img.'.jpg" alt="">');
            
        }
        return $array; 
        */
        
        
    }
    
}