<?php
class StatusApi extends Api 
{
    public function timeLine() {
        $page = $this->input('page');
        $count = $this->input('count');
        
        $page = $page ? intval($page) : 0;
        $count = $count ? intval($count) : 10;
        
        $offset = $page * $count;        
          
        $recs = $this->db->get_results("select * from content order by content_id DESC LIMIT $offset,$count ");
        
        foreach ($recs as $key=>$rec) {
            $recs[$key]->content_id = intval($rec->content_id);
            $recs[$key]->user_id = intval($rec->user_id);
            
            $recs[$key]->latitude = doubleval($rec->latitude);
            $recs[$key]->longitude = doubleval($rec->longitude);
            
            $urow = $this->db->get_row("select nickname, display_pic from users where user_id='$rec->user_id'");
            $recs[$key]->nickname = $urow->nickname;
            
            $precs = $this->db->get_results("select pic_path, is_displaypic from picture where pic_id='$rec->index_pic' or pic_id='$urow->display_pic'");
            foreach ($precs as $prec)
            if ($prec->is_displaypic) {
                $recs[$key]->display_pic = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
            } else
                $recs[$key]->pic_path = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
        }
        
        $this->success(array('msg'=>$recs));
    }
    
    public function getSingleMk() {
        $content_id = $this->input('content_id');
        
        $rec = $this->db->get_row("select * from content where content_id='$content_id'");
        if (!$rec)
            $this->error(0);
        $this->success(array('msg'=>$rec));
    }
    
    public function sendMk() {
        include_once LIB_PATH.'/UploadFile.php';
        $uf = new UploadFile($this->db);
        $uped = $uf->upload();
        if (!$uped) {
            $this->error(0, $uf->msg);
        }
        $index_pic = $uf->msg;
        
        $user_name = $this->input('user_name');
        $content_title = $this->input('content_title');
        $content_body = $this->input('content_body');
        $content_time = $this->input('content_time');
        
        $content_address = $this->input('content_address');
        $latitude = doubleval($this->input('latitude'));
        $longitude = doubleval($this->input('longitude'));   
        
        $charge_type = $this->input('charge_type');
        $charge = intval($this->input('charge'));
        
        $row = $this->db->get_row("select user_id,user_auth from users where user_name='$user_name'");
        if (!isset($row)) {
            $this->error(10012);
        }
        
        if ($charge_type!='a' && $charge_type!='f' && $charge_type!='p') {
            $this->error(0);
        }
        
        if (!$row->user_auth && $charge_type=='p') {
            $this->error(0);
        }
        
        if (!$this->db->query("insert into content(user_id,content_title,content_body,content_time,content_address,latitude,longitude,charge_type,charge,index_pic,create_time) 
            values('$row->user_id','$content_title','$content_body','$content_time','$content_address','$latitude','$longitude','$charge_type','$charge','$index_pic',NOW())")) {
            $this->error(20010);
        }
        $this->success("success");
    }
    
    public function upload() {
        require_once LIB_PATH.'/UploadFile.php';
        $uf = new UploadFile($this->db);
        if ($uf->upload()) {
            $this->success($uf->msg);
        } else {
            $this->error(0, $uf->msg);
        }
    }

    public function createOrder() {
        $content_id = intval($this->input('content_id'));
        $user_name = $this->input('user_name');
        
        if (!$this->db->query("select content_id from content where content_id='$content_id'")) {
            $this->error(0);
        }
        
        if (!($user_id = $this->db->get_var("select user_id from users where user_name='$user_name'"))) {
            $this->error(10012);
        }
        
        if (!$this->db->query("insert into mkorder(user_id,content_id,pay_status,create_time)
            values('$user_id','$content_id','1',NOW())")) {
            $this->error(20010);
        }
        $this->success("success");
    }
    
    public function getSended() {
        $user_name = $this->input('user_name');
        $page = $this->input('page');
        $count = $this->input('count');
        
        $page = $page ? intval($page) : 0;
        $count = $count ? intval($count) : 10;
        
        $offset = $page * $count;
        $pageTotal = 0;
        $total = 0;
        
        $urow = $this->db->get_row("select user_id, nickname, display_pic from users where user_name='$user_name'");
        
        $recs = $this->db->get_results("select * from content where user_id='$urow->user_id' order by content_id DESC LIMIT $offset,$count ");
        
        $total = (int)$this->db->get_var("select count(*) from content where user_id='$urow->user_id' order by content_id");
        $pageTotal = ceil($total/$count);
        
        foreach ($recs as $key=>$rec) {
            $recs[$key]->content_id = intval($rec->content_id);
            $recs[$key]->user_id = intval($rec->user_id);
        
            $recs[$key]->latitude = doubleval($rec->latitude);
            $recs[$key]->longitude = doubleval($rec->longitude);
        
            $recs[$key]->nickname = $urow->nickname;
        
            $precs = $this->db->get_results("select pic_path, is_displaypic from picture where pic_id='$rec->index_pic' or pic_id='$urow->display_pic'");
            foreach ($precs as $prec)
                if ($prec->is_displaypic) {
                    $recs[$key]->display_pic = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
                } else
                    $recs[$key]->pic_path = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
        }
        
        $this->success(array('msg'=>$recs, 'total'=>$total, 'pageTotal'=>$pageTotal));
    }
    
    public function getJoined() {
        $user_name = $this->input('user_name');
        $page = $this->input('page');
        $count = $this->input('count');
        
        $page = $page ? intval($page) : 0;
        $count = $count ? intval($count) : 10;
        
        $offset = $page * $count;
        $pageTotal = 0;
        $total = 0;
        
        $urow = $this->db->get_row("select user_id, nickname, display_pic from users where user_name='$user_name'");
        
        $recs = $this->db->get_results("select * from order where user_id='$urow->user_id' order by order_id DESC LIMIT $offset,$count ");
        
        $total = (int)$this->db->get_var("select count(*) from order where user_id='$urow->user_id' and status='1' and pay_status='1' and is_del='0' order by order_id");
        $pageTotal = ceil($total/$count);
        
        foreach ($recs as $key=>$rec) {
            $recs[$key]->content_id = intval($rec->content_id);
            $recs[$key]->user_id = intval($rec->user_id);
            
            $row = $this->db->get_row("select * from content where content_id='$rec->content_id'");
        
            $recs[$key]->content_title = $row->content_title;
            $recs[$key]->content_body = $row->content_body;
            $recs[$key]->content_time = $row->content_time;
            $recs[$key]->content_address = $row->content_address;
            $recs[$key]->latitude = doubleval($row->latitude);
            $recs[$key]->longitude = doubleval($row->longitude);
            $recs[$key]->charge_type = $row->charge_type;
            $recs[$key]->charge = $row->charge;
            $recs[$key]->create_time = $row->create_time;           
        
            $recs[$key]->nickname = $urow->nickname;
        
            $precs = $this->db->get_results("select pic_path, is_displaypic from picture where pic_id='$row->index_pic' or pic_id='$urow->display_pic'");
            foreach ($precs as $prec)
                if ($prec->is_displaypic) {
                    $recs[$key]->display_pic = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
                } else
                    $recs[$key]->pic_path = 'http://'.$_SERVER['HTTP_HOST'].'/'.$prec->pic_path;
        }
        
        $this->success(array('msg'=>$recs, 'total'=>$total, 'pageTotal'=>$pageTotal));
    }
    
}