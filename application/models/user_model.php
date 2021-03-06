<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {
    public $id = 0;
    public $username = '';
    private $password = '';
    public $fullname = '';
    public $active = 1;
    public $email = '';
    public $address = '';
    public $phone = '';
    public $date_create = '';//datetime
    public $date_modify = '';//datetime
    public $special = 0;//0:manager, 1: customer
    //private
    private $_tbn='user';
    private $is_hashed = false;
    //external
    private $group_obj = null;
        private $group_obj_ready = false;//for lazy loading
    public function __construct()
    {
        parent::__construct();
    }
    public function get_group_name()
    {
        if(self::get_group_obj()!=null)
        {
            return self::get_group_obj()->name;
        }
        return 'unknown';
    }
    public function get_type_en()
    {
        if(self::is_manager())
        {
            return 'Manager';
        }else if(self::is_customer())
        {
            return 'Customer';
        }
        return 'unknown';
    }
    public function get_type_vi()
    {
        if(self::is_manager())
        {
            return 'Nh�n vi�n';
        }else if(self::is_customer())
        {
            return 'Kh�ch h�ng';
        }
        return 'unknown';
    }
    public function is_customer()
    {
        if($this->special==1)
        {
            return true;
        }
        return false;
    }
    public function is_manager()
    {
        if($this->special==0)
        {
            return true;
        }
        return false;
    } 
    public function load()
    {
        //init new lazy state
        $this->group_obj_ready=false;
        
        $this->db->where('id',$this->id);
        $query = $this->db->get($this->_tbn);
        foreach($query->result() as $row)
        {
            $this->id=$row->id;
            $this->username=$row->username;
            
            $this->password=$row->password;
                //set hashed state
                $this->is_hashed = true;
                
            $this->fullname=$row->fullname;
            $this->active=$row->active;
            $this->email = $row->email;
            $this->address = $row->address;
            $this->phone = $row->phone;
            $this->date_create = $row->date_create;
            $this->date_modify = $row->date_modify;
            $this->special = $row->special;
            return true;
        }
        return false;
    }
    public function set_password($raw_or_hashed_password='')
    {
        //check
        if(self::is_exist())
        {
            $tmp = new User_model;
            $tmp->id=$this->id;
            $tmp->load();
            if($raw_or_hashed_password==$tmp->password)
            {
                $this->password = $raw_or_hashed_password;
                $this->is_hashed=true;
            }
            else
            {
                $this->password = $raw_or_hashed_password;
                $this->is_hashed=false;
            }
        }
        else
        {
            $this->password = $raw_or_hashed_password;
            $this->is_hashed=false;
        }
    }
    public function get_password()
    {
        return $this->password;
    }
    public function get_group_obj()
    {
        if($this->group_obj_ready==true)
        {
            return $this->group_obj;
        }
        $this->group_obj_ready=true;
        //load external user_obj
            $this->db->select('group_id');
            $this->db->from($this->_tbn);
            $this->db->where('id',$this->id);
            $query = $this->db->get();
            foreach($query->result() as $row)
            {
                $tmp = new Group_model;
                $tmp->id = $row->group_id;
                $tmp->load(); 
                //assign
                $this->group_obj = $tmp;
            }
        return $this->group_obj;
    }
    public function set_group_obj($obj=null)
    {
        if($obj!=null && !$obj->is_exist())
        {
            $this->group_obj = null;
        }
        else
        {
            $this->group_obj = $obj;
        }
        return $this->group_obj_ready=true;
    }
    public function get_by_id($id=0)
    {
        $obj=new User_model;
        $obj->id=$id;
        $obj->load();
        return $obj;
    }
    public function get_by_username($username='')
    {
        $obj=new User_model;
        $obj->username=$username;
        $obj->load_by_username();
        return $obj;
    }
    public function is_exist($id=-1)
    {
        $this->db->select('id');
        $this->db->where('id',$id==-1?$this->id:$id);
        return $this->db->count_all_results($this->_tbn)>0?true:false;
    }
    public function load_by_username()
    {
        $this->db->select('id');
        $this->db->where('username',$this->username);
        $query = $this->db->get($this->_tbn);
        foreach($query->result() as $row)
        {
            $this->id = $row->id;
            self::load();
            return true;
        }
        return false;
    }
    /**
     * User_model::login()
     * G�n this->id v� $this->password = [RAW_PASS] tru?c
     * @return
     */
    public function login()
    {
        //hashpass if not yet
        //echo 'before:'.$this->password;
        self::hash_pass();
        //echo 'after:'.$this->password;
        $this->db->select('id');
        $this->db->from($this->_tbn);
        $this->db->where('id',''.$this->id);
        $this->db->where('password',''.$this->password);
        $this->db->where('active',1);
        $count = $this->db->count_all_results();
        return $count>0?true:false;
    }
    /**
     * User_model::login()
     * G�n this->username v� $this->password = [RAW_PASS] tru?c
     * @return
     */
    public function login_by_username()
    {
        //hashpass if not yet
        //echo 'before:'.$this->password;
        self::hash_pass();
        //echo 'after:'.$this->password;
        $this->db->select('username');
        $this->db->from($this->_tbn);
        $this->db->where('username',''.$this->username);
        $this->db->where('active',1);
        $this->db->where('password',''.$this->password);
        $count = $this->db->count_all_results();
        return $count>0?true:false;
    }
    public function delete($id=-1, $id_t=-1)
    {
        //xet ton tai
        if(!self::is_exist($id) || !self::is_exist($id_t))
        {
            return false;
        }
        //chuyen quyen so huu cho moi thanh phan
            //Chuy?n cho order: order_user_id, order_customer_user_id
            $sql = 'UPDATE `category` SET `order_user_id`='.$id_t.' WHERE `order_user_id`='.$id;
            $this->db->simple_query($sql);
            
            $sql = 'UPDATE `category` SET `order_customer_user_id`='.$id_t.' WHERE `order_customer_user_id`='.$id;
            $this->db->simple_query($sql);
                       
            //Chuy?n cho Post: user_id
            $sql = 'UPDATE `post` SET `user_id`='.$id_t.' WHERE `user_id`='.$id;
            $this->db->simple_query($sql);
        //x�a user c?n x�a di
            $this->db->where('id', $id);
            $this->db->delete($this->_tbn);
        //finish
        return true;
    }
    public function add()
    {
        //insert new record
        $this->db->set('active', 1); 
        $this->db->insert($this->_tbn);
        //get max id
        $this->id=self::get_max_id();
        //auto datetime
        $this->date_create = date('Y-m-d H:i:s');
        //alway hash password before add
        self::hash_pass();
        //call update
        return self::update();
    }
    private function hash_pass()
    {
        if(!$this->is_hashed)
        {
            $this->password = $this->encrypt->sha1($this->password);
            $this->is_hashed=true;
        }
    }
    public function get_max_id()
    {
        //get max id
        $this->db->select_max('id');
        $query = $this->db->get($this->_tbn);
        foreach($query->result() as $row)
        {
            return $row->id;
        }
        return 0;
    }
    public function update()
    {
        //call lazy load before update
        self::get_group_obj();
        //hash password if not yet
        self::hash_pass();
        $array = array(
            'username' => $this->username,
            'fullname' => $this->fullname,
            'password' => $this->password,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'active' => $this->active,
            'date_create' => $this->date_create,
            'date_modify' => date('Y-m-d H:i:s'),
            'special' => $this->special
        );
        $this->db->where('id',$this->id);
        $this->db->update($this->_tbn,$array);
        //update external
        if($this->group_obj_ready==true && self::is_manager()==true)
        {
            $array = array(
                'group_id' => $this->get_group_obj()==null?0:$this->get_group_obj()->id
            );
            $this->db->where('id',$this->id);
            $this->db->update($this->_tbn,$array);
        }
        return true;
    }
    public function search($id=-1,$username='',$fullname='',$email='',$active=-1,$group_id=-1, $special=-1, $start_point = 0, $count=-1)
    {
        $this->db->from($this->_tbn);
        $this->db->select('id');
        if($id>-1)
        {
            $this->db->where('id',$id);   
        }
        $this->db->like('username',$username);
        $this->db->like('fullname',$username);
        $this->db->like('email',$username);
        if($active>-1)
        {
            $this->db->where('active',$active);   
        }
        if($special>-1)
        {
            $this->db->where('special',$special); 
        }
        if($group_id>-1)
        {
            $this->db->where('group_id',$group_id);   
        }
        //limit
        if($start_point>=0 && $count>-1)
        {
            $this->db->limit($count,$start_point);
        }
        $query = $this->db->get();
        $re = array();
        foreach($query->result() as $row)
        {
            $tmp=new User_model;
            $tmp->id=$row->id;
            $tmp->load();
            array_push($re,$tmp);
        }
        return $re;
    }
    public function search_count($id=-1,$username='',$fullname='',$email='',$active=-1,$group_id=-1, $special=-1)
    {
        return sizeof(self::search($id,$username,$fullname,$email,$active,$group_id,$special));
    }
    public function can_use_username()
    {
        $this->db->select('id');
        $this->db->where('id !=',$this->id);
        $this->db->where('username',$this->username);
        return $this->db->count_all_results($this->_tbn)>0?false:true;
    }
    public function can_use_email()
    {
        $this->db->select('id');
        $this->db->where('id !=',$this->id);
        $this->db->where('email',$this->email);
        return $this->db->count_all_results($this->_tbn)>0?false:true;
    }
    public function validate($password='',$password2='')
    {
        $re=array();
        //check username
        if(!self::can_use_username())
        {
            array_push($re,'username_exist_fail');
        }
        if($this->username=='')
        {
            array_push($re,'username_fail');
        }
        if($this->fullname=='')
        {
            array_push($re,'fullname_fail');
        }
        if(!$this->form_validate->is_email($this->email))
        {
            array_push($re,'email_fail');
        }
        if(!self::can_use_email())
        {
            array_push($re,'email_exist_fail');
        }
        
        //check password
        if($password!=$password2 || $password=='')
        {
            array_push($re,'password_fail');
        }
        return $re;
    }
}
?>