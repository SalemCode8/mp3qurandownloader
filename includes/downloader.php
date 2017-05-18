<?php
/**
 * Created by PhpStorm.
 * User: SaMo
 * Date: 5/14/2017
 * Time: 6:12 PM
 */

namespace mp3quran;


class downloader
{
//    Shaikhs List sorting:
    const BYID = 0;
    const BYNAME = 1;
//    server where shaikhs data comes from :
    private $_server = 'http://mp3quran.net//api//_arabic.json';
    // Errors :
    private $_errors = false;
    // shaikh private server
    private $_shaikhServer;
    // all data
    public $getAll;
    // selected shaikh data
    public $shaikh;
    // rawaya (if shaikh has multi rawayas this will be array)
    public $rawaya;
    public $rawayaNum;
    public $count;
    public $letter;
    public $shurahs;
    public function __set($name, $value)
    {
        if($name == 'server'){
            $this->_server = $value;
            $this->__getAll();
        }
    }
    public function __construct()
    {
        $this->__getAll();
    }
    private function __getAll(){
        $this->getAll = json_decode(json_encode(json_decode(file_get_contents($this->_server))),true)['reciters'];
    }
    public static function convertTOSurahs($number){
        $shurahsArray = array(
            'الفاتحة',
            'البقرة',
            'آل عمران',
            'النساء',
            'المائدة',
            'الأنعام',
            'الأعراف',
            'الأنفال',
            'التوبة',
            'يونس',
            'هود',
            'يوسف',
            'الرعد',
            'إبراهيم',
            'الحجر',
            'النحل',
            'الإسراء',
            'الكهف',
            'مريم',
            'طه',
            'الأنبياء',
            'الحج',
            'المؤمنون',
            'النور',
            'الفرقان',
            'الشعراء',
            'النمل',
            'القصص',
            'العنكبوت',
            'الروم',
            'لقمان',
            'السجدة',
            'الأحزاب',
            'سبأ',
            'فاطر',
            'يس',
            'الصافات',
            'ص',
            'الزمر',
            'غافر',
            'فصلت',
            'الشورى',
            'الزخرف',
            'الدخان',
            'الجاثية',
            'الاحقاف',
            'محمد',
            'الفتح',
            'الحجرات',
            'ق',
            'الذاريات',
            'الطور',
            'النجم',
            'القمر',
            'الرحمن',
            'الواقعة',
            'الحديد',
            'المجادلة',
            'الحشر',
            'الممتحنة',
            'الصف',
            'الجمعة',
            'المنافقون',
            'التغابن',
            'الطلاق',
            'التحريم',
            'الملك',
            'القلم',
            'الحاقة',
            'المعارج',
            'نوح',
            'الجن',
            'المزمل',
            'المدثر',
            'القيامة',
            'الإنسان',
            'المرسلات',
            'النبأ',
            'النازعات',
            'عبس',
            'التكوير',
            'الإنفطار',
            'المطففين',
            'الإنشقاق',
            'البروج',
            'الطارق',
            'الأعلى',
            'الغاشية',
            'الفجر',
            'البلد',
            'الشمس',
            'الليل',
            'الضحى',
            'الشرح',
            'التين',
            'العلق',
            'القدر',
            'البينة',
            'الزلزلة',
            'العاديات',
            'القارعة',
            'التكاثر',
            'العصر',
            'الهمزة',
            'الفيل',
            'قريش',
            'الماعون',
            'الكوثر',
            'الكافرون',
            'النصر',
            'المسد',
            'الإخلاص',
            'الفلق',
            'الناس'
        );
        return $shurahsArray[$number];
    }
    public function getSurahsList(){
        $all = array();
        for ($i = 0;$i <= 113;$i++) {
            $all[$i+1] = self::convertTOSurahs($i);
        }
        return $all;
    }
    public function getShaikhsList($sort=self::BYNAME){

        for($i = 0;$i < \count($this->getAll); $i++){
            $output[$this->getAll[$i]['id']] = $this->getAll[$i]['name'];
        }
        if($sort == 0){
            ksort($output);
        }elseif ($sort == 1){
            asort($output);

        }
            return $output;

    }
    public function getRawayasList(){
        if(!isset($this->shaikh)){return $this->_error('لا يمكن استعمال هذه الدالة قبل تعيين القارئ المحدد');};
        $output = [];
        if($this->rawayaNum > 1){
            for($i = 0;$i < $this->rawayaNum;$i++){
                $output["rawaya{$i}"] = $this->rawaya["rawaya{$i}"]['reading'];
            }
            return $output;
        }else{
            return $this->rawaya;
        }
    }
    private function _getId($name){
        for($i=0;$i < \count($this->getAll);$i++) {
            $all[$this->getAll[$i]['name']] = $this->getAll[$i]['id'];
        }
        if(in_array($name,$all)){
        return intval($all[$name]);
        }else{
            return 0;
        }
    }
    public function getShaikh($entry){
        $entry = (!is_integer($entry)) ? $this->_getId($entry) : $entry;
        if(!$entry){
            $this->_errors[] = 'إسم القارئ المدخل غير موجود';
        }
        for ($i = 0;$i < \count($this->getAll);$i++){
            if($this->getAll[$i]['id'] == $entry){
                $this->shaikh = $this->getAll[$i]['name'];
                $this->_shaikhServer = $this->getAll[$i]['Server'];
                $this->letter = $this->getAll[$i]['letter'];
                $this->__checkReading($this->shaikh);
            }
        }
    }
    private function __checkReading($shaikh){
        $readings = [];
        $counts = [];
        $shurahs = [];
        for($a = 0;$a < \count($this->getAll);$a++){
            if($this->getAll[$a]['name'] == $shaikh){
                $readings[] = $this->getAll[$a]['rewaya'];
                $counts[]   = $this->getAll[$a]['count'];
                $shurahs[]  = $this->getAll[$a]['suras'];
                $Servers[] = $this->getAll[$a]['Server'];
            }
        }
        $this->rawayaNum = \count($readings);
        if (\count($readings) == 1){
            $this->rawaya  = $readings[0];
            $this->count   = $counts[0];
            $this->_shaikhServer = $Servers[0];
            $shurahs = explode(',',$shurahs[0]);
            foreach($shurahs as $num => $name){
                $this->shurahs[] = downloader::convertTOSurahs($num);
            }
        }elseif (\count($readings) > 1){
            for ($b=0;$b < \count($readings);$b++){
                $this->count                = $this->_error('Cannot Use Count This Reader have more than one read instead use {rawaya} variable of downloader scope');
                $this->shurahs              = $this->_error('Cannot Use shurahs This Reader have more than one read instead use {rawaya} variable of downloader scope');
                $this->_shaikhServer        = $this->_error('Cannot Use Servers This Reader have more than one read instead use {rawaya} variable of downloader scope');
                $shurahs[$b] = explode(',',$shurahs[$b]);
                foreach ($shurahs[$b] as $key => $value) {
                    $number = $value;
                    $name = downloader::convertTOSurahs($key);
                    $shurahs[$b][$key] = array('s'=> $name,'n' => $number);
                }

                $this->rawaya["rawaya{$b}"] = array("reading"=>$readings[$b],"shurahsNum" => \count($shurahs[$b]),"count"=>$counts[$b],"shurahs"=>$shurahs[$b],'Server'=>$Servers[$b]);
            }
        }
    }
    public function individual($surah=0,$path="./",$absolute=false,$rewayaNum=1){
        if(!isset($this->shaikh)){return $this->_error('لا يمكن استعمال هذه الدالة قبل تعيين القارئ المحدد');};
        if($surah == 0 || $surah > 114){
            return 0;
        }
        $surah = str_pad((is_integer($surah) ? $surah : $surah),'3','0',STR_PAD_LEFT);
        $path = ($absolute) ? "file:///" . $path : $path;
        if($this->rawayaNum > 1){
            $fullPath = $this->rawaya["rawaya{$rewayaNum}"]['Server']."/". $surah . ".mp3";
        }else{
            $fullPath = $this->_shaikhServer ."/". $surah . ".mp3";

        }
        $fileName = ((substr($path, -1) == '/') ? $path :$path . "/") . $surah . ".mp3";
        if($file = file_get_contents($fullPath)){
            if(file_put_contents($fileName,$file)){return 1;}else{return 0;}
        }
    }
    public function range($startSurah=0,$endSurah=1,$path="./",$absolute=false,$rewayaNum=1){
        if(!isset($this->shaikh)){return $this->_error('لا يمكن استعمال هذه الدالة قبل تعيين القارئ المحدد');};
        if($startSurah==0||$endSurah==1){return 0;}
        $num = 1;
        for($surah = $startSurah;$surah <= $endSurah;$surah++){
            if($surah == 0 || $surah > 114){
                continue;
            }
            $surah = str_pad((is_integer($surah) ? $surah : $surah),'3','0',STR_PAD_LEFT);
            $path = ($absolute) ? "file:///" . $path : $path;
            if($this->rawayaNum > 1){
                $fullPath = $this->rawaya["rawaya{$rewayaNum}"]['Server']."/". $surah . ".mp3";
            }else{
                $fullPath = $this->_shaikhServer ."/". $surah . ".mp3";

            }
            $fileName = ((substr($path, -1) == '/') ? $path :$path . "/") . $surah . ".mp3";
            if($file = file_get_contents($fullPath)){
                if(file_put_contents($fileName,$file)){$back["surah{$num}"]=1;}else{$back["surah{$num}"]=0;}
            }
            $num++;
        }
        return $back;
    }
    public function selection($selection, $customDelimiter = null,$isRegExpDelimiter=false, $path="./", $isAbsolutePath=false, $rewayaNum=1){
        if(!isset($this->shaikh)){return $this->_error('لا يمكن استعمال هذه الدالة قبل تعيين القارئ المحدد');};
        if(!is_array($selection)){
            if(($customDelimiter == null)){
                $surahs =  preg_split('/(?:\,|\,\s|\s\,|\s\,\s|\-|\s\-|\-\s|\s\-\s)/',$selection);
            }else{
                if($isRegExpDelimiter){
                    $surahs = preg_split($customDelimiter,$selection) ;
                }else{
                    $surahs =  explode($customDelimiter,$selection);
                }
            }
        }else{
            $surahs = $selection;
        }
        $num = 1;
        $back= [];
        foreach($surahs as $surah){
            $surah = intval($surah);
            if($surah == 0 || $surah > 114){
                continue;
            }
            $surah = str_pad((is_integer($surah) ? $surah : $surah),'3','0',STR_PAD_LEFT);
            $path = ($isAbsolutePath) ? "file:///" . $path : $path;
            if($this->rawayaNum > 1){
                $fullPath = $this->rawaya["rawaya{$rewayaNum}"]['Server']."/". $surah . ".mp3";
            }else{
                $fullPath = $this->_shaikhServer ."/". $surah . ".mp3";

            }
            $fileName = ((substr($path, -1) == '/') ? $path :$path . "/") . $surah . ".mp3";
            if($file = file_get_contents($fullPath)){
                if(file_put_contents($fileName,$file)){$back["surah{$num}"]=1;}else{$back["surah{$num}"]=0;}
            }
            $num++;
        }
        return $back;
    }
    public function getErrors(){
        if($this->_errors){

            foreach ($this->_errors as $error) {
                $output = $this->_error($error);
            }
            return $output;
        }else{
            return false;
        }

    }
    private function _error($msg){
        $output ="<div>";
        $output .= "<h3 style='border:5px solid #FFFFD5;background: #FFFFD5;color: #f00; padding: 10px;display: inline-block'>";
        $output .= $msg;
        $output .= "</h3>";
        $output .="</div>";
        return $output;
    }
}
