<?php

namespace Administrator\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Where;
use Zend\Math\Rand;
class UserTable extends AbstractTableGateway
{
    protected $table = 'user';
    protected $user_linker ='user_role';
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new HydratingResultSet();
        $this->resultSetPrototype->setObjectPrototype(new User($this->adapter));
        $this->initialize();
        
    }
	
    /** 
     * Add User Information in our database in registeration type as well as insert role entry in role table
     * @user :user Information object
     * @return type true/false
     * 
     * */
    public function saveUser($user)
    {
        
        $data = array(
             'first_name' => $user->first_name,
             'last_name'  => $user->last_name,
             'email'=>$user->email,
             'sec_email'=>$user->sec_email,
             'password'=>$user->password,
             'mobile'=>$user->mobile,
             'address'=>$user->address,
             'address2'=>$user->address2,
             'country'=>$user->country,
             'states'=>$user->states,
             'city'=>$user->city,
             'status'=>0,
             'steps'=>1,
         );
        
        $res =  $this->insert($data);
        $this->lastInsertValue = $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        
        $sql = new Sql($this->adapter);
        $select =   $sql->insert()
                        ->into($this->user_linker)
                        ->columns(array('user_id','role_id'))
                        ->values(array('user_id'=>$this->lastInsertValue,'role_id'=> $user->role));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $this->lastInsertValue;
    }
    
    /**
     * Get Single user Information
     * @where array take conditions
     * @columns array columns
     * @author developed by Trs Software Solutions
     * @return array
     **/
    public function getUser($where = array(), $columns = array())
    {
        try {
            $sql = new Sql($this->adapter);
            $select = $sql->select()->from(array(
                'user' => $this->table
            ));
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            
            $select->join(array('userRole' =>$this->user_linker), 'userRole.user_id = user.user_id', array('role_id'), 'LEFT');
            $select->join(array('role' => 'role'), 'userRole.role_id = role.rid', array('role_name'), 'LEFT');
            
            //var_dump($select->getSqlString());die;	
            $statement = $sql->prepareStatementForSqlObject($select);
            $users = $statement->execute()->current();
           
            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
    public function getState($user_id)
	{
		 $sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
						  ->columns(array('state'))
						  ->where(array('user_id'=>$user_id));
		$statement = $sql->prepareStatementForSqlObject($select);
		$state = $statement->execute()->current();
		return $state;
	}
    /**
     * Change Password On backend and front end when user is logined
     * @user_id Current User Id
     * @password Encrypted User Password
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function changePassword($user_id,$password)
    {
        
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);
        $password = $bcrypt->create($password);
        $data = array('password'=> $password,);
        return  $this->update($data, array('user_id' => $user_id));
    }
	
	  /**
     * Change Password On backend and front end when user is logined
     * @user_id Current User Id
     * @password Encrypted User Password
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function changeRole($user_id,$role)
    {
        
        $data = array('role'=> $role);
        return  $this->update($data, array('user_id' => $user_id));
    }
    /**
     * Delete User Action Process here
     * @user_id  User Id
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function deleteUser($user_id)
    {
        $res =$this->delete(array('user_id'=>$user_id));
        return $res;
    }
    
    /**
     * Change user Status like active/inactive 
     * @user_id  User Id
     * @status get Status Data
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function updateStatus($user_id,$status)
    {
        $data = array('state'=>$status);
        $res =$this->update($data,array('user_id'=>$user_id));
        return $res;  
    }
    
    /**
     * Fetch All user Infromation action process here
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function fetchAll()
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0 AND user.role != 4');
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
    
    public function fetchAllActive()
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0 AND user.state != 0');
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
    
    public function fetchAllDeactive()
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0 AND user.state = 0 AND user.role != 4');
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
	
	
	public function fetchByRole($user_id,$role)
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0')
							->where("$role = $user_id")
                            ->where('user.role != 4');
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
    
    public function fetchByRoleActive($user_id,$role)
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0 AND user.state != 0')
							->where("$role = $user_id");
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
    
    public function fetchByRoleDeactive($user_id,$role)
    {
        
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>$this->table))
                            ->join(array('role'=>'user_role'),'role.role_id=user.role',array('role_name'))
							->where('user.deleted = 0 AND user.state = 0')
							->where("$role = $user_id");
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($statement->execute());
        $resultSet->buffer();
       
        return $resultSet;
    }
    
     /**
     * Searh Users Records Action Process here
     * @author developed by Trs software solutions
     * @return Object
     **/
    public function searchRecords($string)
    {
        $sql = new Sql($this->adapter);
        $mainSelect =   $sql->select()
                            ->from(array('user' =>'user'))
                            ->join(array('urole'=>$this->table), 'user.user_id = urole.user_id')
                            ->join(array('role'=>'role'),'role.rid=urole.role_id');
        
        $where = new  Where();
        $where->and->nest->like('user.first_name',"%$string%")
                                 ->or->like('user.last_name',"%$string%")
                                 ->or->like('user.email',"%$string%")
                                  ->or->like('role.role_name',"%$string%");
        $mainSelect->where($where);
         
        $statement = $sql->prepareStatementForSqlObject($mainSelect);
        $resultset = $this->resultSetPrototype->initialize($statement->execute())->toArray();
        return $resultset;
    }

    public function updateToken($token,$email)
    {
        
        $data = array('password_token'=>$token,'token_expiry'=>time());
        $res =$this->update($data,array('email'=>$email));
        return $res;
    }
    public function updatePassword($password,$email)
    {
        
        $data = array('password'=>$password,'password_token'=>'','token_expiry'=>'');
        $res =$this->update($data,array('email'=>$email));
        return $res;
    }
    /**
     * Vaidate Token exist or not  in our user table in database
     * @token  rendom string
     * @return  user Email ID/False;
     * 
     * */
    public function verifyToken($token)
    {
        $res = $this->select(array('password_token'=>$token));
        return $res->current();
        
       
    }
	
	public function updateUserInfomation($user_id,Users $object)
	{
	       $data =array(
									'first_name'=>$object->first_name,
									'last_name'=>$object->last_name,
									'gender'=>$object->gender,
									'dob'=>$object->dob,
									'phone'=>$object->phone,
									'address'=>$object->address,
									'country'=>$object->country,
									'city'=>$object->city,
									'pincode'=>$object->pincode
								);
			$res = $this->update($data,array('user_id'=>$user_id));
			return $res;
	
	}
	/**
     * Update Image Attribute data of perticular User
     * 
     * @author Pawan Kumar <pkb.pawan@gmail.com >
     * @return void
     **/
     public function updateUser($data,$user_id)
     {
        return $this->update($data,array('user_id'=>$user_id));
     }
	

    
        
    public function checkEmail($email)
    {
        $results = $this->select(array('email'=>$email));
		return $results->current();
    }
	
	 /**
     * Get Single user Information
     * @where array take conditions
     * @columns array columns
     * @author developed by Trs Software Solutions
     * @return array
     **/
    public function getRoles()
    {
        try {
            $sql = new Sql($this->adapter);
            $select = $sql->select()->from(array(
                'role' => 'user_role'
            ));
            
            //var_dump($select->getSqlString());die;	
            $statement = $sql->prepareStatementForSqlObject($select);
            $roles = $statement->execute();
           
            return $roles;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
	
	public function fetchByname($role_name)
    {
		$sql = new Sql($this->adapter);
		$subselect2 =  $sql->select()
                            ->from('user_role')
       	                    ->where(array('role_name'=>$role_name));
		$statement = $sql->prepareStatementForSqlObject($subselect2);
       // $results = $statement->execute();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$results = $resultSet->toArray();
		//print_r($results);die;
		if($results)
		{
			$role_id = $results[0]['role_id'];
			$select =  $sql->select()
                            ->from($this->table)
       	                    ->where(array('role'=>$role_id));
			//print_r($select->getSqlString());die;
			$statement = $sql->prepareStatementForSqlObject($select);
			$resultSet = new ResultSet();
			$resultSet->initialize($statement->execute());
			$results = $resultSet->toArray();
			$ret = $results;
		}
		else{
			$ret = 'null';
		}
       return $ret;
    }
	public function getEmail($email)
	{
		$sql = new Sql($this->adapter);
		$select =   $sql->select()
                            ->from($this->table)
							->where(array('email'=>$email));
		//print_r($select->getSqlString());die;
		
        $statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	public function saveClient($user)
	{		

		$bcrypt = new Bcrypt();

		$bcrypt->setCost(14);

		$password = $bcrypt->create($user->password);
			$data = array(
					 'username'        => $user->username,
					 'firstname'       => $user->firstname,
					 'lastname'        => $user->lastname,
					 'email'	       => $user->email,
					 'mobile'	       => $user->mobile,
					 'password'	       => $password,
					 'role'		       => $user->role,
					 'agent'	       => $user->agent,
					 'branchmanager'   => $user->branchmanager,					 
					 'add_multiple_email'   => $user->add_multiple_email					 
			);
		//print_r($data);
		 $res = $this->insert($data);
		 $this->lastInsertValue = $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
		 

			return $this->lastInsertValue;
	}
    
    public function get_property_meta($property_id, $key, $single=false){
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                    ->from('propertymeta')
                    ->where(
                        array( 'property_id'=> $property_id, 'meta_key'=> $key, 'dsr'=>'')
                    );
        if($single){
            $select->group ( array ("meta_key") );
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        
        if($single) return $statement->execute()->current();
        
		$result = $statement->execute();
		return iterator_to_array($result);
    }
    
	/**
	 *  
	 *  @return - array of data
	 *  
	 *  @details - no 
	 *  
	 *  @date modified - 13 july 2017  
	 *  @date modified - 4:13 PM 16 January, 2018 by PJ
	 */
	public function fetchallClient()
	{
		$querystring= "SELECT  p.*, u.* from property p LEFT JOIN user u on p.user_id = u.user_id order by p.property_id desc";
		
		$statement = $this->adapter->query($querystring); 
		$result = $statement->execute();
		
		return iterator_to_array($result);
	}
	
	
	public function fetchallClient_1(){
		$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where   pm.meta_key = 'address' order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$result = $statement->execute();
		
		return iterator_to_array($result);
		
	}
	
	
		
	
	public function fetchallClient_2(){
		$querystring= "SELECT  p.*, pm.meta_value as address,pm2.meta_value as client_name ,pm3.meta_value as client_email ,pm4.meta_value as year_build, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
		LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
		LEFT JOIN	propertymeta pm3 on pm.property_id = pm3.property_id 
		LEFT JOIN	propertymeta pm4 on pm.property_id = pm4.property_id 
		where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' and(pm3.meta_key='client_email' and (pm4.meta_key='year_build' ))) order by	pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$result = $statement->execute();
		
		return iterator_to_array($result);
		
	}
	
			
	
	public function fetchallClient_3(){
		$querystring= "SELECT  p.*, pm.meta_value as address,pm2.meta_value as client_name , pm.property_id FROM user p 
		LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
		LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
		where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' ) order by	pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$result = $statement->execute();
		
		return iterator_to_array($result);
		
	}
	
			
	
	public function fetchallClient_4(){
		$querystring= "SELECT  p.*, pm.meta_value as address,pm2.meta_value as client_name ,pm3.meta_value as client_email ,pm4.meta_value as year_build, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
		LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
		LEFT JOIN	propertymeta pm3 on pm.property_id = pm3.property_id 
		LEFT JOIN	propertymeta pm4 on pm.property_id = pm4.property_id 
		where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' and(pm3.meta_key='client_email' )) order by	pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$result = $statement->execute();
		
		return iterator_to_array($result);
		
	}
	
	
	
	
	public function fetchallClient1()
	{		
		//$sql = new Sql($this->adapter);
		 $querystring= "SELECT  p.*, pm.meta_value as address,pm2.meta_value as client_name ,pm3.meta_value as client_email ,pm4.meta_value as year_build, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
		LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
		LEFT JOIN	propertymeta pm3 on pm.property_id = pm3.property_id 
		LEFT JOIN	propertymeta pm4 on pm.property_id = pm4.property_id 
		where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' and(pm3.meta_key='client_email' and (pm4.meta_key='year_build' ))) order by	pm.id desc"; 
		
		
		
		/* $querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where   pm.meta_key = 'address' order by pm.id desc"; */
		//$querystring1= "SELECT  p.*, pm.meta_value as client_name, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where   pm.meta_key = 'client_name' order by pm.id desc";
		$querystring=$querystring." ".$querystring1;
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		
		return $resultSet->toArray();
	}
	public function fetchallClientbyStatus($status='all')
	{
		//$sql = new Sql($this->adapter);
		$querystring='';
		if($status=='all'){
			$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where   pm.meta_key = 'address' order by pm.id desc";
		}else{
			$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id where 	pm.meta_key = 'address' and (pm2.meta_key  like 'status' and pm2.meta_value like '{$status}') order by	pm.id desc";
			$statement = $this->adapter->query($querystring); 
			$resultSet = new ResultSet();
			$resultSet->initialize($statement->execute());
			
			$querystring1= "SELECT  p.*,pm.property_id, pm.meta_value as address,pm2.meta_value as client_name ,pm3.meta_value as client_email ,pm4.meta_value as year_build, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
			LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
			LEFT JOIN	propertymeta pm3 on pm.property_id = pm3.property_id 
			LEFT JOIN	propertymeta pm4 on pm.property_id = pm4.property_id 
			where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' and(pm3.meta_key='client_email' and (pm4.meta_key='year_build' and (pm2.meta_key  like 'status' and pm2.meta_value like '{$status}') ))) order by	pm.id desc";
			
			$statement1 = $this->adapter->query($querystring1); 
			$resultSet1 = new ResultSet();
			$resultSet1->initialize($statement1->execute());
			
			$final=array();
			
			$final=array_merge($resultSet1->toArray(),$resultSet->toArray());
			
				$temp_array = array();
				
				foreach ($final as &$v) {
				
					if (!isset($temp_array[$v['property_id']]))
				
					$temp_array[$v['property_id']] =& $v;
				
				}
				
				$final = array_values($temp_array);
			}
		
		
		
		return $final;
	}
	public function fetchallClientbyStatusaa($status='all')
	{
		//$sql = new Sql($this->adapter);
		$querystring='';
		if($status=='all'){
			$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where   pm.meta_key = 'address' order by pm.id desc";
		}else{
			$querystring= "SELECT  p.*, pm.meta_value as address,pm2.meta_value as client_name ,pm3.meta_value as client_email ,pm4.meta_value as year_build, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id 
		LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id 
		LEFT JOIN	propertymeta pm3 on pm.property_id = pm3.property_id 
		LEFT JOIN	propertymeta pm4 on pm.property_id = pm4.property_id 
		where 	pm.meta_key = 'address' and (pm2.meta_key='client_name' and(pm3.meta_key='client_email' and (pm4.meta_key='year_build' ))) order by	pm.id desc"; 
		}
		
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	
	public function getUsers($keywords)
	{
		
		$querystring = "SELECT u.user_id, u.firstname, u.lastname, u.email, u.state, ur.role_name from user u join user_role ur on u.role = ur.role_id WHERE  (u.username LIKE '%" . $keywords . "%' OR u.email LIKE '%" . $keywords  ."%' OR u.firstname LIKE '%" . $keywords  ."%' OR u.lastname LIKE '%" . $keywords  ."%' OR CONCAT(u.firstname,' ',u.lastname) LIKE '%" . $keywords  ."%') AND u.role != 4";
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
    
    public function getUsersByRole($keywords)
	{
		
		$querystring = "SELECT u.user_id, u.firstname, u.lastname, u.email, u.state, ur.role_name from user u join user_role ur on u.role = ur.role_id WHERE  u.role = $keywords AND u.role != 4";
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	public function fetchallUnverifiedClient()
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p LEFT JOIN propertymeta pm on p.user_id = pm.client_id LEFT JOIN propertymeta pm1 on p.user_id = pm1.client_id  where pm.meta_key = 'address' and (pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value != 'varified' and pm1.property_id = pm.property_id ) group by pm.property_id order by pm.id desc";
		// $querystring= "SELECT  p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id  where   pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value!='varified' group by property_id order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
    
    public function fetchallVerifiedClient()
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p LEFT JOIN propertymeta pm on p.user_id = pm.client_id LEFT JOIN propertymeta pm1 on p.user_id = pm1.client_id  where pm.meta_key = 'address' and (pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value = 'varified' and pm1.property_id = pm.property_id ) group by pm.property_id order by pm.id desc";
        //echo $querystring; die;
		// $querystring= "SELECT  p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id  where   pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value!='varified' group by property_id order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	public function fetchallUnrcomments()
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT u.*,prm.meta_value as address, p.comment, p.ID as comment_id, p.user_id from  property_docs_comments p 
		left join  user u on p.user_id = u.user_id 
		left join  property pr on p.property_id = pr.property_id 
		left join  propertymeta prm on p.property_id = prm.property_id 
		where p.resolved=0 and  prm.meta_key='address' order by p.user_id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	public function fetchUnverifiedClientByRole($user_id,$role)
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id where pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value != 'varified' and pm.property_id = pm1.property_id and (p.$role = $user_id or pm.client_id=$user_id)  group by property_id order by pm.id desc";
		// $querystring= "SELECT  p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id  where  pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value!='varified' and (p.$role = $user_id or pm.client_id=$user_id)  group by property_id order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
    
    public function fetchVerifiedClientByRole($user_id,$role)
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id where pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value = 'varified' and pm.property_id = pm1.property_id and (p.$role = $user_id or pm.client_id=$user_id)  group by property_id order by pm.id desc";
		// $querystring= "SELECT  p.email,p.user_id,p.firstname,p.lastname, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id join propertymeta pm1 on p.user_id = pm1.client_id  where  pm.meta_key = 'address' and pm1.dsr='closing-tab' and pm1.meta_key='varification' and pm1.meta_value!='varified' and (p.$role = $user_id or pm.client_id=$user_id)  group by property_id order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	
	 public function fetchallmanager()
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
						  ->columns(array('user_id', 'firstname', 'lastname'))
						  ->where(array('role'=>3))
						  ->order('firstname');
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	 
	
	public function fetchallAgent($status)
	{	
		
		$sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
						  ->columns(array('user_id', 'firstname', 'lastname'))
						  ->where(array('branchmanager'=> $status))
						  ->order('firstname');
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	public function fetchClientByRole($user_id,$role)
	{
		//$sql = new Sql($this->adapter);
		$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where  pm.meta_key = 'address' and (p.$role = $user_id or pm.client_id=$user_id) order by pm.id desc";
		
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		//print_r($resultSet->toArray()); die;
		return $resultSet->toArray();
	}
	
	public function fetchClientByRoleAndStatus($user_id,$role,$status='all')
	{
		
		//$sql = new Sql($this->adapter);
		$querystring='';
		if($status=='all'){
			$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id from user p join propertymeta pm on p.user_id = pm.client_id  where  pm.meta_key = 'address' and (p.$role = $user_id or pm.client_id=$user_id) order by pm.id desc";
		}else{
			$querystring= "SELECT  p.*, pm.meta_value as address, pm.property_id FROM	user p LEFT JOIN	propertymeta pm on p.user_id = pm.client_id LEFT JOIN	propertymeta pm2 on pm.property_id = pm2.property_id where 	pm.meta_key = 'address' and (p.$role = $user_id or pm.client_id=$user_id) and (pm2.meta_key  like 'status' and pm2.meta_value like '{$status}') order by pm.id desc";
		}
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	
	
	public function fetchagents($id)
	{  //SELECT * FROM `user` WHERE `branchmanager`= 51
		$querystring= "SELECT * FROM user WHERE branchmanager= $id";
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	public function fetchClientsByAgentId($id)
	{  //SELECT * FROM `user` WHERE `branchmanager`= 51
		$querystring= "SELECT * FROM user WHERE agent = $id";
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	
	
	
	public function get_role($id)
	{
		//echo $id;die;
		$sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
					  ->columns(array('user_id'))
					  ->join('user_role','user.role = user_role.role_id',array('role_name'))
					  ->where(array('user.user_id'=>$id));
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		
		foreach($resultSet as $k):
				$role = $k->role_name;
		endforeach;
		return $role;
	}
    
    public function get_role_id($id)
	{
		//echo $id;die;
		$sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
					  ->columns(array('user_id'))
					  ->join('user_role','user.role = user_role.role_id',array('role_id','role_name'))
					  ->where(array('user.user_id'=>$id));
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		
		foreach($resultSet as $k):
				$role = $k->role_id;
		endforeach;
		return $role;
	}
    
	public function agentCommission($id, $current_role)
	{
		$sql = new Sql($this->adapter);
		if($current_role=='administrator')
		{
			$select = $sql->select()->from($this->table)
						  ->columns(array('user_id', 'email', 'firstname', 'lastname'))
						  ->where(array('role'=>2));
		}
		if($current_role=='branchmanager')
		{
			$select = $sql->select()->from($this->table)
						  ->columns(array('user_id', 'email', 'firstname', 'lastname'))
						->where(array('role'=>2, 'branchmanager'=>$id));
					 
		}
		
		if($current_role=='agent')
		{
			$select = $sql->select()->from($this->table)
						  ->columns(array('user_id', 'email', 'firstname', 'lastname'))
						  ->where(array('role'=>2, 'user_id'=>$id));
		}
		//print_r($select->getSqlString());die;
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->toArray();
	}
	public function getUsername($id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select()->from($this->table)
					  ->columns(array('firstname','lastname'))
					  ->where(array('user_id'=>$id));
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		
		foreach($resultSet as $k):
				$name = $k->firstname." ".$k->lastname;
		endforeach;
		return $name;
	}
	
	public function user_profile($id)
	{
		// $querystring ="SELECT * FROM user WHERE user_id = $id";
		// $statement = $this->adapter->query($querystring); 
		// $results =$statement->execute();		
		// return $results;
		$sql = new Sql($this->adapter);
		$subselect2 =   $sql->select()
                            ->from($this->table)
       	                    ->where(array('user_id'=>$id));
		$statement = $sql->prepareStatementForSqlObject($subselect2);
        $results = $statement->execute()->current();

		return $results;
	}
	
	public function getAgentEmail($id)
	{
		$email ='';
		$querystring= "SELECT u2.email FROM user u1 join user u2 on u1.agent = u2.user_id where u1.user_id=$id";
		$statement = $this->adapter->query($querystring); 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$rs =  $resultSet->toArray();
		if(count($rs) > 0):
		foreach($rs as $k):
				$email = $k['email'];
		endforeach;
		endif;
		return $email;
	}
	
	public function getPropUser($id){
		$querystring= "SELECT user_id FROM property where property_id=$id";
		$statement = $this->adapter->query($querystring); 
		$res = $statement->execute()->current();
		return $res['user_id'];
	}
	 // public function fetchUserrole($id)
    // {
        
       // $sql = new Sql($this->adapter);
		// $select = $sql->select()->from($this->table)
					  // ->columns(array('role'))
					  // ->where(array('user_id'=>$id));
		// $statement = $sql->prepareStatementForSqlObject($select);
		// $result = $statement->execute()->current();
		// return $result;
    // }
	
	
	public function getEmailCheck($user_id,$email)
	{
		$querystring= "SELECT user_id FROM user where email='$email' and user_id != $user_id";
		$statement = $this->adapter->query($querystring); 
		$res = $statement->execute()->current();
		return $res;
		
	}
	


	public function getmlm($user_id)
	{
		$select = "
		SELECT p0.user_id, p0.firstname, p0.branchmanager, 0 AS Level
		FROM user p0
		WHERE p0.user_id =$user_id
		UNION ALL
		SELECT p1.user_id, p1.firstname, p1.branchmanager, 1 AS Level
		FROM user p1
		JOIN (SELECT p0.user_id, p0.branchmanager, 0 AS Level
			  FROM user p0
			  WHERE p0.user_id =$user_id) p0
		  ON p1.branchmanager = p0.user_id
		UNION ALL
		SELECT p2.user_id, p2.firstname, p2.branchmanager, 2 AS Level
		FROM user p2
		JOIN (SELECT p1.user_id, p1.branchmanager, 1 AS Level
			  FROM user p1
			  JOIN (SELECT p0.user_id, p0.branchmanager, 0 AS Level
			  FROM user p0
			  WHERE p0.user_id =$user_id) p0
				ON p1.branchmanager = p0.user_id) p1
		  ON p2.branchmanager = p1.user_id
		UNION ALL
		SELECT p3.user_id, p3.firstname, p3.branchmanager, 3 AS Level
		FROM user p3
		JOIN  (SELECT p2.user_id, p2.branchmanager, 2 AS Level
			   FROM user p2
			   JOIN (SELECT p1.user_id, p1.branchmanager, 1 AS Level
					 FROM user p1
					 JOIN (SELECT p0.user_id, p0.branchmanager, 0 AS Level
							FROM user p0
							WHERE p0.user_id =$user_id) p0
					  ON p1.branchmanager = p0.user_id) p1
				ON p2.branchmanager = p1.user_id) p2
		  ON p3.branchmanager = p2.user_id
		UNION ALL
		SELECT p4.user_id, p4.firstname, p4.branchmanager, 4 AS Level
		FROM user p4
		JOIN(SELECT p3.user_id, p3.branchmanager, 3 AS Level
		FROM user p3
		JOIN  (SELECT p2.user_id, p2.branchmanager, 2 AS Level
			   FROM user p2
			   JOIN (SELECT p1.user_id, p1.branchmanager, 1 AS Level
					 FROM user p1
					 JOIN (SELECT p0.user_id, p0.branchmanager, 0 AS Level
							FROM user p0
							WHERE p0.user_id =$user_id) p0
					  ON p1.branchmanager = p0.user_id) p1
				ON p2.branchmanager = p1.user_id) p2
		  ON p3.branchmanager = p2.user_id) p3
		   ON p4.branchmanager = p3.user_id;
		  ";
		$statement = $this->adapter->query($select);
		$res = $statement->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
     	$res = $resultSet->initialize($res);

        return $res;
		
	}
	 
}
