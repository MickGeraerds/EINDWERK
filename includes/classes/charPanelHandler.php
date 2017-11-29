<?php
    require_once('charHandler.php');
    
class charPanelHandler extends charHandler {

    public function renderCharacters() {
        
        $renderHTML = array();
        
        $sql = "SELECT * FROM `characters` WHERE `account_id` = " . $this->account_id . ";";
        $result = $this -> select($sql);
        foreach($result as $i) {
            $str = "<button class='characterContainer' onClick='handleChar({$i['obj_Id']},{$i['online']})'><img class='imgClass' src='images/classes/{$i['classid']}.png' alt=''><span>{$i['char_name']} ({$i['level']})</span></button>";
            array_push($renderHTML, $str);
            
        };
        
        
        
        
        array_push($renderHTML, '<button id="addCharContainer" onClick="addChar()"><img id="addChar" src="images/ui/addChar.png" alt=""><span> Add Char </span></button>');
        
        return($renderHTML);
        
    }
}
?>