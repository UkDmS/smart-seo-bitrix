<?
if($_GET['p']==1){

    Cmodule::IncludeModule('iblock');
    $currentPageUrl = $APPLICATION->GetCurPage();
    // формируем адрес текущей страницы
    $link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["SERVER_NAME"].$currentPageUrl;
    // разбиваем в массив
    $linkArray = explode("/",$link);
    // кодируем в json, потом понадобится для каноникала
    $linkUnsortString = json_encode($linkArray);
    // разбиты массив сортируем для последужщей кодировки
    sort($linkArray);
    // идентификатор шнфоблока
    $IBLOCK_ID = 40;
    $arSelect = Array("ID");
    $arFilter = Array(
                        "IBLOCK_ID"     =>  $IBLOCK_ID,
                        "ACTIVE_DATE"   =>  "Y",
                        "ACTIVE"        =>  "Y",
                    );
    // получаем id элементов
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    // перебираем элементы инфоблока
    while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
		$db_props = CIBlockElement::GetProperty($IBLOCK_ID , $arFields['ID'], "sort", "asc", array());
        // перебираем свойства элемента инфоблока
        // храним
        //[CANONICAL] канонический адрес
        // [UrlPage] адрес страницы
        //[TitlePage] тайтл
        //[KeyPage] ключевые слова
        //[DescriptionPage] описнаие
        //[h1Page] h1
        //[SeoTextPage] сео текст
        while($ar_props = $db_props->Fetch()){
            // если свойство "адрес страницы", то получаем значение
            // разбиваем его в массив
            // кодируем в json
            // сортируем
            // сортировка нужна для  сравнения адресов, где параметры фильрации могут идти в различном порядке
            if($ar_props["NAME"]=="UrlPage"){
                $fieldArray = explode("/",$ar_props["VALUE"]);
                $fieldUnsortString = json_encode($fieldArray);
                sort($fieldArray);
            }
            // теперь сравниваем преобразованный путь страницы с адресами из инфоблоков
            // если есть соответствие
            if(json_encode($fieldArray)===json_encode($linkArray)){
                // для каноникала дополнительно сравниваем закодированные адреса
                // если параметры одни и те, это мы проверили выше,
                // то еще проверяем, чтобы был и тот же порядок
                // если одинаково, то ничего не делаем
                // если параметры одни и те же, но порядок отличается, то нужно ставить каноникал
                // в качестве каноникала идет элемент $arr["CANONICAL"], равный значению свойства UrlPage
                if($ar_props["NAME"]=="UrlPage"){
                    ($linkUnsortString===$fieldUnsortString) ?  : $arr["CANONICAL"] = $ar_props["VALUE"];
                }
                // заполняем массив с элементами сео
                // в случае, если в настройках выбран тип поля текст, то берем $ar_props['VALUE']['TEXT']
                (isset($ar_props['VALUE']['TEXT'])) ? $arr[$ar_props['CODE']]=$ar_props['VALUE']['TEXT'] : $arr[$ar_props['CODE']]=$ar_props['VALUE'];
            }
        }
    }
    $cp = $this->__component; // объект компонента
    if (is_object($cp)){
        // устанавливаем сео значения
        if($arr["TitlePage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"] = $arr["TitlePage"];
        }
        if($arr["DescriptionPage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] = $arr["DescriptionPage"];
        }
        if($arr["KeyPage"]){
            $cp->arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"] = $arr["KeyPage"];
        }
        // добавляем в хэд каноникал
        if($arr["CANONICAL"]){
         $APPLICATION->AddHeadString('<link rel="canonical" href="'.$arr["CANONICAL"].'">');
        }
    }
}
?>


<!---- ------>


<?
// каноникал появится в области, определенной как SeoTextPage в файле section.php
// $APPLICATION->ShowViewContent('SeoTextPage');
if($_GET['p']==1){
        if($arr["SeoTextPage"]){
            $this->SetViewTarget('SeoTextPage');
            echo $arr["SeoTextPage"];
            $this->EndViewTarget();
        }
}
?>