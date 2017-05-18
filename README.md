# MP3 Quran Downloader
## Simple Class using to download Holy Quran to your device

### Features :
+ Have More than one reader
+ Some of readers have more than on read(rawaya)
+ will have updates
+ Can Download: 
    * individual Surah
    * Range of Surahs
    * Selection of Surahs



### How To use it :
# you have to type:
    
    use quranmp3/downloader;
1- you have to instance the class like this 
    
    $downloader = new downloader();
    
2- select reader
    
    you can write reader name or id
    $downloader->getShaikh( 120);
    or
    $downloader->getShaikh( 'محمود خليل الحصري');
    
if you want to know ids and names of available Shaikhs use this method:

    $downloader->getShaikhsList($sort);
can type in place of $sort 
    
    BYID : to sort List by Shaikhs ID
    BYNAME : to sort List by Shaikhs Name

3- you select read(rawaya) if the reader have more than one 
    you can make sure be use 
    
    $downloader->rawayaNum;
this will return number of rawayas as integer
if the reader have more than one and you want to select one
    first you want to make sure of you select using this
    
    $downloader->getRawayasList();

this will return array of all reader rawayas with names

4- after you select a rawaya now you can download any Surah you Want using one of those

    $downloader->individual($surah,[$path="./",$absolute=false,$rewayaNum=1]);
        - $surah : surah number you want to download it
    $downloader->range($startSurah=0,$endSurah=1,[$path="./",$absolute=false,$rewayaNum=1]);
        - $startSurah : the number of surah will start from it
        = $endSurah : the number of surah will end at it
    $downloader->selection($selection, [$customDelimiter = null,$isRegExpDelimiter=false, $path="./", $isAbsolutePath=false, $rewayaNum=1])
        - $selection : can be array or String with one of this Delimiters (,)(, )( ,)( , )(-)( -)(- )( - )
        - $customDelimiter : if you want to use String as selection but want to but your Delimiter
        - $isRegExpDelimiter : if you want to use RegExp as your custom Delimiter make it true
    - $path : where you want to download the surah you choose (by default in the same folder)
    - $absolute : make it true if you want to write path like this(C:\Users\User\Desktop) By Default False
    - $rewayaNum : the chosen rawaya (if shaikh have more then 1 rawaya)
    
    
 2017 &copy; Copyright [Salem Code](http://unisah.net/)