<?php

class Redirecter{

    public static function redirect($url){
        header('Location: ' . $url);
        exit();
    }
}
