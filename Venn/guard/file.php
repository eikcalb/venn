<?php
namespace guard;

class File{
    public static function fileCheck($filename,...$name){
        if($filename === false){return false;}
        $file=[];
        $file['name']= $filename;
        $file["read"] = is_readable($filename)?true:false;
        $file["write"]= is_writable($filename)?true:false;
        $file['file']= is_file($filename);
        $file['dir']= is_dir($filename);
        if ($file['dir']) {            
            $file['dir']=true;
            foreach ($name as $b){
                $files = $this->fileCheck($filename.$b);
                $file['_'.$b] = $files;               
            }
            
        }
        return $file;
    }
    
    public static function createLocalDir(&$dirname,$mode=0777){
        if(isset($dirname)&&!is_dir($dirname)){
            str_replace("\\", "/", $dirname);
            $letter= substr($dirname,0,1);
            $start=$letter=="/"?substr($dirname,1):$dirname;
            $endletter=substr($dirname,-1);
            $dirname=$endletter=="/"?$start:$start."/";
            mkdir($dirname,$mode,true) or die('dir fail.');            
        }
        return $dirname;
    }
}