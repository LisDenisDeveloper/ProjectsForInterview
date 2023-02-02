<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Выгрузка задач");

?>Выбор периода для выгрузки задач
<br>
<a href="TasksScript.php?mounth=1" class="btn">Месяц</a>
<br>
<a href="TasksScript.php?week=1" class="btn">Неделя</a>
<br>
<a href="TasksScript.php?day=1" class="btn">Сутки</a>

<?php
if ($_GET['mounth']) {
    $todaydate = date('d.m.Y');
    $dateforupdate = date_create_from_format('d.m.Y', $todaydate);
    date_sub($dateforupdate, date_interval_create_from_date_string('30 days'));
    $update = date_format($dateforupdate, 'd.m.Y');
    $period = $update;
}
if ($_GET['week']) {
    $todaydate = date('d.m.Y');
    $dateforupdate = date_create_from_format('d.m.Y', $todaydate);
    date_sub($dateforupdate, date_interval_create_from_date_string('7 days'));
    $update = date_format($dateforupdate, 'd.m.Y');
    $period = $update;
}
if ($_GET['day']) {
    $todaydate = date('d.m.Y');
    $dateforupdate = date_create_from_format('d.m.Y', $todaydate);
    $update = date_format($dateforupdate, 'd.m.Y');
    $period = $update;
}

if (!is_null($period)){

if (CModule::IncludeModule("IBLOCK")) {
    $ids2 = array();
    $duplicate = CIBlockElement::GetList("ID",
        array("IBLOCK_ID" => "304"),
        array("ID", "PROPERTY_4736")
    );
    while ($dup = $duplicate->Fetch()) {
        $ids2[$dup["PROPERTY_4736_VALUE"]] = $dup["ID"];
    }
    //print_r("</br>" . count($ids2));
    $tids = array();
    if (CModule::IncludeModule("tasks")) {
        $groups = array(289, 634, 339, 338, 337, 357);

        $status = array(
            "Задача почти просрочена" => "-3",
            "Новая задача (не просмотрена)" => "-2",
            "Задача просрочена" => "-1",
            "Новая задача. (Не используется)" => "1",
            "Задача принята ответственным. (Не используется)" => "2",
            "Задача выполняется" => "3",
            "Условно завершена (ждет контроля постановщиком)" => "4",
            "Задача завершена" => "5",
            "Задача отложена" => "6",
            "Задача отклонена ответственным. (Не используется)" => "7");


        foreach ($groups as $group) {
            $arGroup = CSocNetGroup::GetByID($group);
            $tasks = CTasks::GetList("",
                array("GROUP_ID" => $group, ">=CHANGED_DATE" => $period),
                array("TITLE", "CREATED_BY", "RESPONSIBLE_ID", "CREATED_DATE", "CHANGED_DATE", "CLOSED_DATE", "REAL_STATUS", "DATE_START",
                    "ACCOMPLICES", "AUDITORS", "GROUP_ID")
            );

            //$co = 0;
            while ($ob = $tasks->Fetch()) {
                //$co = $co + 1;
                $otask = CTaskItem::getInstance($ob["ID"], 3065);
                $otaskd = $otask->getData();
                $AUDITORS_old = $otaskd['AUDITORS'];
                $ACCOMPLICES_old = $otaskd['ACCOMPLICES'];

                $tids[] = $ob["ID"];
                $eid = 0;

                if (array_key_exists($ob["ID"], $ids2)) {
                    $eid = $ids2[$ob["ID"]];
                }

                if (empty($ob["CLOSED_DATE"])) {
                    $dney = "Задача еще не завершена";
                } else {
                    $startDate = new DateTime($ob["CREATED_DATE"]);
                    $endDate = new DateTime($ob["CLOSED_DATE"]);
                    $interval = $startDate->diff($endDate);
                    $dney = $interval->format("Полных дней: %a");
                }

                if ($eid == 0) {

                    $realstatus = $ob["REAL_STATUS"];
                    $statuskey = array_search($realstatus, $status);

                    $el = new CIBlockElement;
                    $PROP = array();

                    $PROP[4727] = $ob["CREATED_BY"];//Постановщик
                    $PROP[4728] = $ob["RESPONSIBLE_ID"];//Ответственный
                    $PROP[4747] = $statuskey;//Статус задачи
                    $PROP[4726] = $ob["CLOSED_DATE"];//Дата закрытия
                    $PROP[4736] = $ob["ID"];//ID задачи
                    $PROP[4746] = $dney;//Продолжительность выполнения задачи
                    $PROP[4733] = $ACCOMPLICES_old;//Соисполнители
                    $PROP[4734] = $AUDITORS_old;//Наблюдатели
                    $PROP[4735] = $arGroup["NAME"];//Название группы
                    $PROP[4737] = '<a href = "https://b24.ska.su/company/personal/user/1892/tasks/task/view/' . $ob["ID"] . '/"> Задача </a>'; //Ссылка на задачу

                    $arLoadProductArray = array(
                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                        "IBLOCK_ID" => 304,
                        "PROPERTY_VALUES" => $PROP,
                        "NAME" => $ob["TITLE"],
                        "ACTIVE" => "Y",            // активен
                        "DATE_CREATE" => $ob["CREATED_DATE"]
                    );

                    $PRODUCT_ID = $el->Add($arLoadProductArray);
                } else {

                    $realstatus = $ob["REAL_STATUS"];
                    $statuskey = array_search($realstatus, $status);

                    $el = new CIBlockElement;
                    $PROP = array();

                    $PROP[4727] = $ob["CREATED_BY"];//Постановщик
                    $PROP[4728] = $ob["RESPONSIBLE_ID"];//Ответственный
                    $PROP[4747] = $statuskey;//Статус задачи
                    $PROP[4726] = $ob["CLOSED_DATE"];//Дата закрытия
                    $PROP[4736] = $ob["ID"];//ID задачи
                    $PROP[4746] = $int;//Продолжительность выполнения задачи
                    $PROP[4733] = $ACCOMPLICES_old;//Соисполнители
                    $PROP[4734] = $AUDITORS_old;//Наблюдатели
                    $PROP[4735] = $arGroup["NAME"];//Название группы
                    $PROP[4737] = '<a href = "https://b24.ska.su/company/personal/user/1892/tasks/task/view/' . $ob["ID"] . '/"> Задача </a>'; //Ссылка на задачу

                    $arLoadProductArray = array(
                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                        "IBLOCK_ID" => 304,
                        "PROPERTY_VALUES" => $PROP,
                        "NAME" => $ob["TITLE"],
                        "ACTIVE" => "Y",            // активен
                        "DATE_CREATE" => $ob["CREATED_DATE"]
                    );
                    $PRODUCT_ID = $eid;
                    $update = $el->Update($PRODUCT_ID, $arLoadProductArray);

                }
            }
            //print_r("</br>" . $co . ' - ' . $group);
        }
    }
}

print_r("</br>"."</br>"."Задачи успешно выгружены");
} else {
	print_r("</br>"."</br>"."Необходимо выбрать период выгрузки задач");
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
