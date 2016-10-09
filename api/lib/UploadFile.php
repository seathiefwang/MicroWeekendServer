<?php

class UploadFile 
{
    public $db;
    public $msg;
    public $is_displaypic;
    public function __construct($Db) {
        if ($Db == null) {
            $this->db = Db::init();
        } else {
            $this->db = $Db;
        }
        $this->is_displaypic = 0;
    }
    public function upload() {
        return $this->dealFile();
    }
    public function dealFile() {
        if ($_FILES["pic"]["error"] > 0) {
            $this->msg = $_FILES["pic"]["error"];
            return false;
        }
        if (($_FILES["pic"]["type"] == "image/gif")
            || ($_FILES["pic"]["type"] == "image/jpeg")
            || ($_FILES["pic"]["type"] == "image/jpg")
            || ($_FILES["pic"]["type"] == "image/png")) {
            if ($_FILES["pic"]["size"] < 10000000) {
                return $this->save();
            } else {
                $this->msg = "File size is error";
                return false;
            }           
        } else {
            $this->msg = "File type is error";
            return false;
        }
            
    }
    public function save() {
        $filename = $_FILES["pic"]["name"];
        $extension = $this->getExt($_FILES["pic"]["type"]);
        $savepath = 'data/upload/'.date('Y/md/H/');
        //$currenttime = date('y-m-d h:i:s',time());
        
        $savename = uniqid().substr(str_shuffle('0123456789abcdef'), rand(0, 9), 7).'.'.$extension;
        $savefile = $savepath.$savename;
        
        mkdir($savepath, 0777, true);
        
        if (!$this->db->query("insert into picture(pic_name,pic_path,create_time,is_displaypic) values('$filename','$savefile',NOW(),'$this->is_displaypic')")) {
            $this->msg = 'insert to db is error of pic';
            return false;
        }
        
        move_uploaded_file($_FILES["pic"]["tmp_name"],$savefile);
        $this->msg = $this->db->get_var("select pic_id from picture where pic_path='$savefile'");
        return true;
        
    }
            
    private function getExt($filename) {
        $pathinfo = explode('/', $filename);

        return $pathinfo[1];
    }
}