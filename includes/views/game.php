<head>
    <link rel="stylesheet" href="styles/jquery.custom-scrollbar.css">
    <link rel="stylesheet" href="styles/game.css">
</head>
   <section id="game" class="clearfix">
    <div id="charPanel" class="default-skin" tabindex="-1">
    </div>
    <div id="addCharPanel" style="display: none">
           <h3>Create Character</h3>
            <form id="addCharForm">
                <div id="addName">
                   <label for="createCharName">Name: </label>
                    <input id="createCharName" type="text" name="charName">
                    <label class="switch">
                      <input id="gender" type="checkbox" name="gender" unchecked>
                      <div class="slider round"></div>
                    </label>
                </div>
                <div id="addRace">
                   <input type="radio" name="race" data-id="0" class="hidden" id="raceHuman" value="human">
                   <label class="block" for="raceHuman">Human</label>
                   <div class="raceSlot">
                   <input type="radio" name="race" data-id="1"  class="hidden" id="raceElf" value="elf">
                   <label for="raceElf">Elf</label>
                   <input type="radio" name="race" data-id="2"  class="hidden" id="raceDarkElf" value="darkelf">
                   <label for="raceDarkElf">DarkElf</label>
                   </div>
                   <div class="raceSlot">
                   <input type="radio" name="race" data-id="3"  class="hidden" id="raceOrc" value="orc">
                   <label for="raceOrc">Orc</label>
                   <input type="radio" name="race" data-id="4"  class="hidden" id="raceDwarf" value="dwarf">
                   <label for="raceDwarf">Dwarf</label>
                   </div>
                </div>
                <div id="addClass" style="display: none">
                   <input type="radio" name="class" class="hidden" id="fighter" value="0">
                   <label for="fighter"><span>Fighter</span><br/><div id="createFighter"></div></label>
                   <input type="radio" name="class" class="hidden" id="mage" value="1">
                   <label id="pickMage" for="mage"><span>Mage</span><br/><div id="createMage"></div></label>
                </div>
                <div id="submitCreate" style="display: none">
                    <input type="button" onclick="createCharacter()" value="create">
                </div>
            </form>
        <?php
        ?>
    </div>
    <div id="charInfo" style="display: none">
        <div id="activateCharacter">
            <div id="charActivator" onClick="charActivator(event)"></div>
        </div>
        <div id="invEquipmentContainer">
            
            <table id="invEquipment">
                <tr>
                    <td id="hat" class="slot1" data-id="0"></td><td id="helm" class="slot2" data-id="1"></td><td id="mask" class="slot3" data-id="2"></td><td id="tattoo" class="slot4" data-id="9"></td><td id="necklace" class="slot5" data-id="10"></td>
                </tr>
                <tr><td id="rHand" class="slot1" data-id="3"></td><td id="chest" class="slot2" data-id="4"></td><td id="lHand" class="slot3" data-id="5"></td><td id="rEarring" class="slot4" data-id="11"></td><td id="lEarring" class="slot5" data-id="12"></td>
                </tr>
                <tr><td id="gloves" class="slot1" data-id="6"></td><td id="pants" class="slot2" data-id="7"></td><td id="boots" class="slot3" data-id="8"></td><td id="rRing" class="slot4" data-id="13"></td><td id="lRing" class="slot5" data-id="14"></td>
                </tr>
            </table>
        </div>
        <div id="invContainer" class="default-skin" tabindex="-1">
            <table id="inv" class="viewport">
                
            </table>
        </div>
    </div>
    <div id="combatPanel">
        <div id="eBar" style="display:none">
            <span id="eName">Name</span><span id="eLvl"></span>
            <div id="eActiveHealth"></div>
            <div id="eActiveMana"></div>
            <div id="eActiveExp"></div>
        </div>
        <div id="pBar" style="display:none">
            <span id="pLvl">20</span><span id="pName">Name</span>
            <div id="pActiveHealth"></div>
            <div id="pActiveMana"></div>
            <div id="pActiveExp"></div>
        </div>
    </div>
    <div id="mobPanel">
        <label for="combatEnable" onclick="getCombatInfo()">Enable Combat </label>
        <input type="checkbox" onclick="getCombatInfo()" name="combatEnable" id="combatEnable" checked="checked">
    </div>
    <div id="menuPanel"></div>
</section>