<?php
class UserApi extends Api
{
    public function login() {
        $uname = $this->input('user_name');
        $password = $this->input('password');
        
        if (!$this->db->query("select user_name from users where user_name='$uname'")) {
            $this->error(10012);
        }
        if (!$this->db->query("select user_name from users where user_name='$uname' and password='$password'")) {
            $this->error(10011);
        }
        $uuid = createUUID();
        $this->db->query("update users set uuid='$uuid' where user_name='$uname'");
        $this->success($uuid);
    }
    
    public function register() {
        $uname = $this->input('user_name');
        $password = $this->input('password');
        $nname = createNickname($this->db);
        
        if ($this->db->query("select user_name from users where user_name='$uname'")) {
            $this->error(10013);
        }
        
        if (!$this->db->query("insert into users(user_name,password,nickname,create_time) values('$uname','$password','$nname', NOW())")) {
            $this->error(20010);
        }
        $this->success();
    }
    
    public function setRealname() {
        $uname = $this->input('user_name');
        $rname = $this->input('realname');
        if ($this->db->query("update users set realname='$rname' where user_name='$uname'")) {
            $this->success();
        } else {
            $this->error(10012);
        }              
    }
    
    public function setNickname() {
        $uname = $this->input('user_name');
        $nname = $this->input('nickname');
        if ($this->db->query("select nickname from users where nickname='$nname'")) {
            $this->error(10014);
        }
        if ($this->db->query("update users set nickname='$nname' where user_name='$uname'")) {
            $this->success();
        } else {
            $this->error(10012);
        }
    }
    
    public function setDisplayPic() {
        require_once LIB_PATH.'/UploadFile.php';
        $uf = new UploadFile($this->db);
        $uf->is_displaypic = 1;
        if ($uf->upload()) {
            $uname = $this->input('user_name');
            $this->db->query("update users set display_pic='$uf->msg' where user_name='$uname'");
            $this->success($uf->msg);
        } else {
            $this->error(0, $uf->msg);
        }
    }
    
    public function setGender() {
        $uname = $this->input('user_name');
        $gender = $this->input('user_gender');
        
        if ($this->db->query("update users set user_gender='$gender' where user_name='$uname'")) {
            $this->success();
        } else {
            $this->error(10012);
        }
    }
    
    public function getUserInfo() {
        $uname = $this->input('user_name');
        
        $row = $this->db->get_row("select * from users where user_name='$uname'");
        
        if ($row) {
            $res['user_id'] = $row->user_id;
            $res['user_name'] = $row->user_name;
            $res['realname'] = $row->realname;
            $res['nickname'] = $row->nickname;
            $res['user_gender'] = $row->user_gender;
            if ($row->display_pic > 0)
                $res['display_pic'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$this->db->get_var("select pic_path from picture where pic_id='$row->display_pic'");
            else 
                $res['display_pic'] = '';
            $this->success(array('msg'=>$res));
        }
        $this->error(10012);
    }
    
    
}