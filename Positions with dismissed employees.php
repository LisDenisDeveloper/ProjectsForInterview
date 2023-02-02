<?php

if (CModule::IncludeModule("IBLOCK")){

    $sotrudniki = CUser::GetList($by = "ID",
        $order = "asc",
        $filter = array ("ID", "ACTIVE" => "y", "LOGIN"),
        $arParams = array("SELECT" => array("ID", "LOGIN"))
    );
    while ($arUser = $sotrudniki->Fetch()) {
        $sotr[] = $arUser["ID"];
    }

    $dolgnosti = CIBlockElement::GetList("",
        array("IBLOCK_ID" => "137"),
        array("PROPERTY_1197", "PROPERTY_1198", "ID", "IBLOCK_ID")
    );
    while($dol = $dolgnosti->Fetch()){
        var_dump($dol["PROPERTY_1197"]);

        if ((!in_array($dol["PROPERTY_1197"], $sotr)) and (!in_array($dol["PROPERTY_1198"], $sotr)))
        {
            $i = $i + 1;

            //echo "</br>";
            //echo $dol["ID"];



        }
    }
    //echo "</br>";
    //echo $i;
}








