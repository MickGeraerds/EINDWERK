
////   Game   ////

function addChar() {
    if($('#addCharPanel').attr('style') == "display: inline-block") {
        $('#addCharPanel').attr('style','display: none');
    }
    else {
        $('#addCharPanel').attr('style','display: inline-block');
    }
    $('#charInfo').attr('style','display: none');
}

function createCharacter() {
    $name = $("#createCharName").val();
    $gender = $('#gender').prop('checked');
    if($gender == false) {
        $gender = 0;
    }
    else {
        $gender = 1;
    }
    $race = $("input[name='race']:checked").data('id');
    $class = $("input[name='class']:checked").val();
    
    $array = [$name, $gender, $race, $class];
    
    if($name == "") {
        alert("Please fillout your character name");
        return;
    }
    else {
        $data = {'action': 'createUser', 'name': $name, 'gender': $gender, 'race': $race, 'class': $class};
        ajaxCalls($data, "Character Created!", "html");
        $('#addCharPanel').attr('style','display: none');
    }
}

$('#addRace input').on('click', function(e){
    $('#addClass').attr('style', 'display: block');
    $choice = e.target.value;
    if($choice == "dwarf") {
        $('#mage').prop('disabled', true);
        $('#pickMage').attr('style', 'display: none');
    }
    else {
        $('#mage').prop('disabled', false);
        $('#pickMage').attr('style', 'display: inline-block');
        
    }
})

$('#addClass input').on('click', function(e){
    $('#submitCreate').attr('style', 'display: block');
})

function handleChar($i, $online, $activate = 0){
    
    $data = {'action': 'updateStats', 'char_id': $i};
    ajaxCalls($data, "updateStats", "html");
    
    if($('#charInfo').attr('style') == "display: inline-block" && $('#charInfo').attr('data-id') == $i && $activate == 0) {
        $('#charInfo').attr('style','display: none');
    }
    else {
        $('#charInfo').attr('style','display: inline-block');
        $('#charInfo').attr('data-id', $i);
    }
    if($online == 1) {
        $('#charActivator').addClass("active");
        $('#charActivator').attr('style','background-image: url(./images/ui/createRace.png);');
        $('#charActivator').html('Selected');
    }
    else {
        $('#charActivator').removeClass("active");
        $('#charActivator').attr('style','background-image: url(./images/ui/createRacePicked.png);'); 
        $('#charActivator').html('Select');       
    }
    
    $('#addCharPanel').attr('style','display: none');
    
    $data = {'action': 'equipmentUI', 'owner_id': $i};
    ajaxCalls($data, "renderEquipment", "json");
    $data = {'action': 'inventoryUI', 'owner_id': $i};
    ajaxCalls($data, "renderInventory", "json"); // continue here
}

function charActivator(e){
    $characterId = e.target.parentElement.parentElement.dataset.id;
    alert($characterId);
    $data = {'action': 'activateChar', 'char_id': $characterId};
    ajaxCalls($data, "activateChar", "html");
    renderCharList(); 
    handleChar($characterId, 1, 1);
    
}

function renderEquipment($data) {
    $('td').each( function() {
        $(this).attr("style", "");
    });
    
    $.each($data, function($i, $obj) {
        $location = $obj['location'];
        $icon = $obj['icon'];
        if($location == 7 &&  $('td[data-id="4"]').attr("style") == "background-image: url(./images/icons/"+$icon+".jpg)"){
            $('td[data-id='+$location+']').attr("style", "background-image: url(./images/icons/"+$icon+".jpg); opacity: 0.5"); 
        }
        else if($location == 5 &&  $('td[data-id="3"]').attr("style") == "background-image: url(./images/icons/"+$icon+".jpg)"){
            $('td[data-id='+$location+']').attr("style", "background-image: url(./images/icons/"+$icon+".jpg); opacity: 0.5"); 
        }
        else{
            $('td[data-id='+$location+']').attr("style", "background-image: url(./images/icons/"+$icon+".jpg)");            
        }
    })
}

function renderInventory($data) {
    $('#inv').empty();
    $numRows = Math.floor($data.length/6)+1;
    $tr = 0;
    for ($i=0; $i<$numRows; $i++) {
        $('#inv').append("<tr data-id="+$i+">"); 
    }
    $.each($data, function($i, $obj) {
        //alert(JSON.stringify($obj));
        $tr = Math.floor($i/6);
        $icon = $obj['icon'];
        $owner = $obj['object_id'];
        $('#inv tr[data-id='+$tr+']').append('<td data-obj="'+$owner+'" title="'+$obj['count']+'"style="background-image: url(./images/icons/'+$icon+'.jpg")');
    });
    $('#invContainer').customScrollbar();
}

function renderCharList() {
    $data = {'action': 'renderCharacters'};
    ajaxCalls($data, "renderCharList", "json");
}


function fixViewPort(){
    $('.viewport').css("width", "100%");
    $('.viewport').css("height", "100%");
};

//$testData = {'action': 'test', 'obj_id': 90};
//ajaxCalls($testData, "test", "html");

renderCharList(); 
setTimeout(fixViewPort, 1000);
