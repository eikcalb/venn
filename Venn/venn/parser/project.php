<?php
namespace Venn\parser;

class Project {
    public static function createProject($data){
        $proj=[];
        foreach ($data as $key => $value) {
            switch ($key){
                case "name": $proj[$key]=$value; break;
                case "id": $proj[$key]=$value; break;
                case "version": $proj[$key]=$value; break;
                case "budget": $proj[$key]=$value; break;
                case "employer": $proj[$key]=$value; break;
                case "employee": $proj[$key]=$value; break;
                case "init_time": $proj[$key]=$value; break;
            }
        }
    }
}
