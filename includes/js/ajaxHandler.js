function ajaxCalls($data, $callBackFunc, $dataType) {
    $.ajax({
        type: "POST",
        url: "includes/classes/ajaxHandler.php",
        data: $data,
        dataType: $dataType,
        success: function(results) {
            if($callBackFunc == 
    "renderEquipment") {
                renderEquipment(results);
            }
            else if($callBackFunc == "test") {
                alert(results);
            }
            else if($callBackFunc == 
    "renderInventory") {
                //alert(JSON.stringify(results));
                renderInventory(results);
            }
            else if($callBackFunc == 
    "updateStats") {

            }
            else if($callBackFunc == 
    "renderCharList") {
                $('div#charPanel').empty();
                $.each(results, function(i, obj) {
                    $('div#charPanel').append(obj); 
                });
                $("#charPanel").customScrollbar();
            }
            else if($callBackFunc == 
    "Character Created!"){
                if(!$.trim(results)) {
                    alert($callBackFunc);
                }
                else {
                    alert(results);
                }
                renderCharList();
            }
            else if($callBackFunc == 
    "getCombatInfo"){
                startCombat(results);
                //alert(results);
            }
            else if($callBackFunc == "activateChar") {
                return;
            }
            else if($callBackFunc == "combatWon") {
                showLoot(results);
            }
           },
        error: function(error){
            $what = JSON.stringify(error);
            alert($what);
            alert(error['responseText']);
        }
    });
}