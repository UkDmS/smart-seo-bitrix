<?
if($_GET['p']==1){

    Cmodule::IncludeModule('iblock');
    $currentPageUrl = $APPLICATION->GetCurPage();
    $link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["SERVER_NAME"].$currentPageUrl;
    $linkArray = explode("/",$link);
    $linkUnsortString = json_encode($linkArray);
    sort($linkArray);
    $IBLOCK_ID = 40;
    $arSelect = Array("ID");
    $arFilter = Array(
                        "IBLOCK_ID"     =>  $IBLOCK_ID,
                        "ACTIVE_DATE"   =>  "Y",
                        "ACTIVE"        =>  "Y",
                    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
		$db_props = CIBlockElement::GetProperty($IBLOCK_ID , $arFields['ID'], "sort", "asc", array());
        while($ar_props = $db_props->Fetch()){
            if($ar_props["NAME"]=="UrlPage"){
                $fieldArray = explode("/",$ar_props["VALUE"]);
                $fieldUnsortString = json_encode($fieldArray);
                sort($fieldArray);
            }
            if(json_encode($fieldArray)===json_encode($linkArray)){
                if($ar_props["NAME"]=="UrlPage"){
                    ($linkUnsortString===$fieldUnsortString) ?  : $arr["CANONICAL"] = $ar_props["VALUE"];
                }
                (isset($ar_props['VALUE']['TEXT'])) ? $arr[$ar_props['CODE']]=$ar_props['VALUE']['TEXT'] : $arr[$ar_props['CODE']]=$ar_props['VALUE'];
            }
        }
    }
   // print_r($arr);
    $cp = $this->__component; // объект компонента
    if (is_object($cp))
    {
        if($arr["TitlePage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"] = $arr["TitlePage"];
        }
        if($arr["DescriptionPage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] = $arr["DescriptionPage"];
        }
        if($arr["KeyPage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"] = $arr["KeyPage"];
        }
        if($arr["CANONICAL"]){
         $APPLICATION->AddHeadString('<link rel="canonical" href="'.$arr["CANONICAL"].'">');
        }
    }


/*$arSelect = Array("*");
$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "NAME"=>"https://poshlyachka.ru/catalog/vse-dlya-seksa/filter/proizvoditel-is-216/apply/");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement()){
    $arFields = $ob->GetFields();
   print_r($arFields);
}*/

}
?>


<!---- ------>


<?
if($_GET['p']==1){
        if($arr["SeoTextPage"]){
            $this->SetViewTarget('SeoTextPage');
            echo $arr["SeoTextPage"];
            $this->EndViewTarget();
        }
}
?>