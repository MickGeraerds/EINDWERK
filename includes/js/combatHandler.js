var $pMaxHp;
var $pMaxMp;
var $pMaxCp;
var $mMaxHp;
var $mMaxMp; 
var $pCurHp;
var $pCurMp;
var $pCurCp = 100;
var $mCurHp;
var $mCurMp;
var $mobId;
var $pId;

function getCombatInfo() {
    $data = {'action': 'getCombatInfo', 'levelRange': 1};
    ajaxCalls($data, "getCombatInfo", "json");
}

var $i1 = 0;
var $i2 = 0;

function setStaticBars($pName, $pLevel, $eName, $eLevel, $curXp, $minXp, $maxXp, $eXp) {
    if(!$pName) {
        $('#pBar').attr("style", "display:none;");
    }
    $totalLevelXp = $maxXp-$minXp;
    $currentLevelProgress = $curXp-$minXp;
    
    $('#pBar').attr("style", "display:block;");
    $('#eBar').attr("style", "display:block;");
    
    $('#pActiveExp').attr("style", "background-size:" + $currentLevelProgress/$maxXp*100 + '% 100%');
    $('#pActiveExp').text($currentLevelProgress + "/" + $totalLevelXp);
    
    $('#eActiveExp').attr("style", "background-size: 100% 100%");
    $('#eActiveExp').text($eXp);
    
    $('#pName').html($pName);
    $('#eName').html($eName);
    
    $('#pLvl').html($pLevel);
    $('#eLvl').html($eLevel);
    
}

function setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, $eCurHp, $eMaxHp, $eCurMp, $eMaxMp) {
        $('#pActiveHealth').attr("style", "background-size:" + $pCurHp/$pMaxHp*100 + '% 100%');
        $('#pActiveHealth').text($pCurHp + "/" + $pMaxHp);
        $('#eActiveHealth').attr("style", "background-size:" + $eCurHp/$eMaxHp*100 + '% 100%');
        $('#eActiveHealth').text($eCurHp + "/" + $eMaxHp);
        $('#pActiveMana').attr("style", "background-size:" + $pCurMp/$pMaxMp*100 + '% 100%');
        $('#pActiveMana').text($pCurMp + "/" + $pMaxMp);
        $('#eActiveMana').attr("style", "background-size:" + $eCurMp/$eMaxMp*100 + '% 100%');
        $('#eActiveMana').text($eCurMp + "/" + $eMaxMp);
    
}
function chanceCalculator($chance) {
    $trigger = Math.floor((Math.random() * 100) + 1);
    return($trigger <= $chance);   
}
function damageCalculator($minDamage, $maxDamage) {
    $damage = Math.floor((Math.random() * $maxDamage) + $minDamage);
    return $damage;
}
function finishCombat($winner) {
    if($pCurHp < $pMaxHp){
        regenerate($winner);
    }
    else{
        if($winner == "player"){
            cleanText();
            $bars = [$pCurHp, $pCurMp, $pCurCp];
            $data = {'action': 'combatWon', 'bars': $bars, 'mobId': $mobId, 'pId': $pId}; //make this.
            ajaxCalls($data, "combatWon", "json");
        
            $data = {'action': 'inventoryUI', 'owner_id': $pId};
            ajaxCalls($data, "renderInventory", "json");
        
    //set cur hp db
    //add drops and exp
    //
    //
            $('#menuPanel').prepend('<div> You Won </div>');
        
        }
        else {
        //
            $('#menuPanel').prepend('<div> You Lost </div>');
        }
        getCombatInfo();
    }
    
    
}
function showLoot(results) {
    $.each(results, function($i, item) {
        if(item['name'] != undefined) {
            $('#menuPanel').prepend('<div style="color: #e8a00f;"> You obtained '+item['count']+' '+item['name']+' </div>');
        }
    });
}
function regenerate($winner){
                setTimeout(function() {
                    $regen = Math.floor($pMaxHp/10);
                        $pCurHp = $pCurHp + parseInt($regen);
                        setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, $mCurHp, $mMaxHp, $mCurMp, $mMaxMp);
                        if($pCurHp >= $pMaxHp) {
                            $pCurHp = $pMaxHp;
                            setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, $mCurHp, $mMaxHp, $mCurMp, $mMaxMp);
                            finishCombat($winner);
                        }
                        else{
                            regenerate($winner);
                        }
                }, 1000);
}
function startCombat($data) {
    //alert(JSON.stringify($data));
    //alert($data.length);
    if($('#combatEnable').is(':checked')) {
        $.each($data, function($i, $char){
            $.each($char, function($i, $mob){
                if($mob !== null && typeof $mob == "object" && $mob['minXp'] == null){
                setStaticBars($char['pName'], $char['expWindow']['currentLevel'], $mob['mName'], $mob['mLevel'], $char['curExp'], $char['expWindow']['minXp'], $char['expWindow']['maxXp'], $mob['mXp']);
                
                setBars($char['pCurHp'], $char['pMaxHp'], $char['pCurMp'], $char['pMaxMp'], $mob['mHp'], $mob['mHp'], $mob['mMp'], $mob['mMp']);
                $mobId = $mob['mobId'];
                $pId = $char['pId'];
                    
                //combatDelay
                //chanceCalculator($mob['pHitChance']*100);
                //pBlockRate
                //pDamageWindow[1-4]
                //playerCritChance
                //mHitChance
                //mBlockRate
                //mDamageWindow[1-4]
                //mobCritChance
                //mHitsPerSecond
                //mHp
                //mMp
                //pCurHp
                //pCurMp
                //pHitsPerSecond
                //
                $mob['pHitChance'];
                $mob['pBlockRate'];
                $mob['pDamageWindow'];
                $mob['playerCritChance'];
                $mob['mHitChance'];
                $mob['mBlockRate'];
                $mob['mDamageWindow'];
                $mob['mobCritChance'];
                $mob['mHitsPerSecond'];
                $mob['mHp'];
                $mob['mMp'];
                $char['pCurHp'];
                $char['pCurMp'];
                $char['pHitsPerSecond'];
                $pCurHp = $char['pCurHp'];
                $mCurHp = $mob['mHp'];
                $pCurMp = $char['pCurMp'];
                $mCurMp = $mob['mMp'];
                $pMaxHp = $char['pMaxHp'];
                $mMaxHp = $mob['mHp'];
                $pMaxMp = $char['pMaxMp'];
                $mMaxMp = $mob['mMp'];
                
                $pCurHp = parseInt($pCurHp);
                $pMaxHp = parseInt($pMaxHp);
                
                //alert($pCurHp < $pMaxHp);
                       
                
                pAttackLoop($mob['mName'], $mob['pHitChance'], $mob['pDamageWindow'], $mob['playerCritChance'], $mob['mBlockRate'], $char['pCurMp'], $char['pHitsPerSecond']);
                mAttackLoop($mob['mName'], $mob['mHitChance'], $mob['mDamageWindow'], $mob['mobCritChance'], $mob['pBlockRate'], $mob['mMp'], $mob['mHitsPerSecond']);
                  
                
            }
            });
        });
    }
}

function pAttackLoop($mName, $pHitChance, $pDamageWindow, $pCritChance, $mBlockRate, $pCurMp, $pHitsPerSecond) {
    var $message = "";
    
    setTimeout(function() {
        if($pCurHp > 0 && $mCurHp > 0) {
            if(chanceCalculator($pHitChance)) {
                if(!chanceCalculator($mBlockRate)) {
                    $damage = damageCalculator($pDamageWindow[0], $pDamageWindow[1]);
                }
                else {
                    $damage = damageCalculator($pDamageWindow[2], $pDamageWindow[3]);
                    $message = $mName + " blocked You're Attack \n" + $message;
                }
                if(chanceCalculator($pCritChance)){
                    $damage = $damage*2;
                    $message = "Citical Hit! \n" + $message;
                }
                $mCurHp = $mCurHp-$damage;
                $message =  $message + "You deal " + $damage + " damage \n";
            }
            else {
                $message =  $mName + " dodged your attack";
            }
            if($mCurHp < 1) {
                $mCurHp = 0;
            }
            $('#menuPanel').prepend('<div> ' + $message + '</div>');
            setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, $mCurHp, $mMaxHp, $mCurMp, $mMaxMp);
            pAttackLoop($mName, $pHitChance, $pDamageWindow, $pCritChance, $mBlockRate, $pCurMp, $pHitsPerSecond);
        }
        else if($mCurHp < 1){
            setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, 0, $mMaxHp, $mCurMp, $mMaxMp);
            setTimeout(finishCombat("player"), 1000);
        }
            
    }, $pHitsPerSecond*2000);
}
function mAttackLoop($mName, $mHitChance, $mDamageWindow, $mCritChance, $pBlockRate, $mCurMp, $mHitsPerSecond) {
    var $message = "";
    //alert($mHitChance + " " + $mDamageWindow + " " + $mCritChance + " " + $pBlockRate + " " + $mCurMp + " " + $mHitsPerSecond)
    
    setTimeout(function() {
        if($pCurHp > 0 && $mCurHp > 0) {
            if(chanceCalculator($mHitChance)) {
                if(!chanceCalculator($pBlockRate)) {
                    $damage = damageCalculator($mDamageWindow[0], $mDamageWindow[1]);
                }
                else {
                    $damage = damageCalculator($mDamageWindow[2], $mDamageWindow[3]);
                    $message = "You blocked " + $mName + "'s Attack \n" + $message;
                }
                if(chanceCalculator($mCritChance)){
                    $damage = $damage*2;
                    $message = $mName + "'s attack was a citical hit! \n" + $message;
                }
                $pCurHp = $pCurHp-$damage;
                $message =  $message + $mName + " deal's " + $damage + " damage \n";
            }
            else {
                $message =  "You dodged " + $mName + "'s attack \n";
            }
            if($pCurHp < 1) {
                $pCurHp = 0;
            }
            $('#menuPanel').prepend('<div> ' + $message + '</div>');
            setBars($pCurHp, $pMaxHp, $pCurMp, $pMaxMp, $mCurHp, $mMaxHp, $mCurMp, $mMaxMp);
            mAttackLoop($mName, $mHitChance, $mDamageWindow, $mCritChance, $pBlockRate, $mCurMp, $mHitsPerSecond);
        }
        else if($pCurHp < 1) {
            setBars(0, $pMaxHp, $pCurMp, $pMaxMp, $mCurHp, $mMaxHp, $mCurMp, $mMaxMp);
            finishCombat("mob");
        }
        
        
        
            
    }, $mHitsPerSecond*2000);
}

function cleanText(){
    $divs = $('#menuPanel div').toArray();
    $divs = $divs.slice(10);
    if(!$.isEmptyObject($divs)) {
        $.each($divs, function($i, $obj){
            $obj.remove();
        });
    }
    //$.each(('#menuPanel div:nth-child($i)
    //$('#menuPanel div:nth-child(50)') {
        
    //}
}
getCombatInfo();


//$testData = {'action': 'test'};
//ajaxCalls($testData, "test", "html");