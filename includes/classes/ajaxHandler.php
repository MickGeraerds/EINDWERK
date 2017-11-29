<?php  
    require_once('combatHandler.php');
    require_once('charHandler.php');
    require_once('charPanelHandler.php');
    require_once('itemHandler.php');

    session_start();
    $combatHandler = new combatHandler();
    $charHandler = new charHandler();
    $charPanelHandler = new charPanelHandler();
    $itemHandler = new itemHandler();


    if($_POST['action'] == 'createUser') {
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $race = $_POST['race'];
        $classType = $_POST['class'];
        $classArray = array();
        $class;
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
        switch ($race) {
            case 0:
                $classArray = array(0, 10);
                break;
            case 1:
                $classArray = array(18, 25);
                break;
            case 2:
                $classArray = array(31, 38);
                break;
            case 3:
                $classArray = array(44, 49);
                break;
            case 4:
                $classArray = array(53);
                break;
        }
        switch ($classType) {
            case 0:
                $class = $classArray[0];
                break;
            case 1:
                $class = $classArray[1];
                break;
        }
        
        
        
    $data = array('name' => $name, 'sex' => $gender, 'race' => $race, 'class' => $class);

    $result = $charHandler->createChar($data);
        
    }



    if($_POST['action'] == 'equipmentUI') {
        $charId = $_POST['owner_id'];
        $data = array('char_id' => $charId);
                                           
        $result = $itemHandler->equipmentUIHandler($data);
        //var_dump($result);
        echo json_encode($result);
    }

    if($_POST['action'] == 'inventoryUI') {
        $charId = $_POST['owner_id'];
        $data = array('char_id' => $charId);
        
        $result = $itemHandler->inventoryUIHandler($data);
        
        echo json_encode($result);
    }

    if($_POST['action'] == 'renderCharacters') {
        
        $result = $charPanelHandler->renderCharacters();
        
        echo json_encode($result);
        
    }
    if($_POST['action'] == 'updateStats') {
        $charId = $_POST['char_id'];
        $data = array('char_id' => $charId);
        
        $result = $charHandler->updateStats($data);
    }
    if($_POST['action'] == 'getCombatInfo') {
        $result = $combatHandler->getCombatInfo($_POST['levelRange']);
        echo json_encode($result);
        //var_dump($result);
    }
    if($_POST['action'] == 'activateChar') {
        $charId = $_POST['char_id'];
        $data = array('char_id' => $charId);
        
        $result = $charHandler->setActiveChar($data);
    }
    if($_POST['action'] == 'combatWon') {
        $bars = $_POST['bars'];
        $mobId = $_POST['mobId'];
        $pId = $_POST['pId'];
        $results = $combatHandler->combatWon($bars, $mobId, $pId); //reword mobId to session
        if(!empty($results)) {
            echo json_encode($results[0]);
        }
        else {
            $results = ["loot" => "null"];
            echo json_encode($results);
        }
    }
    if($_POST['action'] == 'test') {
        $result = $charHandler->getLevel(10000, "both");
        var_dump($result);
    }


?>