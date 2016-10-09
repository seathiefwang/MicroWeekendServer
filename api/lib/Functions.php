<?php

/**
 * createUUID
 * @param params    params
 */
function createUUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
        return $uuid;
    }
}

function createNickname($db, $length = 8){
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
    $nickname = "";
    $finded = true;
    while ($finded) {
        for ($i = 0; $i < $length; $i ++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            $nickname .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            //$nickname .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $finded = $db->query("select nickname from users where nickname='$nickname'");
        if ($finded) $nickname = "";
    }
    return $nickname; 
}
