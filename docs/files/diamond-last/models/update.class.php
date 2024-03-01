<?php

abstract class Update {
    protected static $update_human_name;
    protected static $update_name;
    protected static $update_id;
    protected static $update_date;
    protected static $class_name;

    protected $path_to_files;
    protected $path_to_update_files;

    public final function __construct($path_to_files, $path_to_update_files){
        $this->path_to_files = $path_to_files;
        $this->path_to_update_files = $path_to_update_files;
    }

    final public static function getHumanName(){
        return static::$update_human_name;
    }

    final public static function getName(){
        return static::$update_name;
    }

    final public static function getClassName(){
        return static::$class_name;
    }

    final public static function getId(){
        return static::$update_id;
    }

    final public static function getDate(){
        return static::$update_date;
    }

    final public function checkChmod(){
        if (substr(sprintf('%o', fileperms($this->path_to_update_files)), -4) != "0777" && substr(sprintf('%o', fileperms($this->path_to_update_files)), -4) != "0666"){
            return false;
        }
        return true;
    }

    abstract public function setDB($db);

    abstract public function setFiles();

    abstract public function checkIfValid();
}