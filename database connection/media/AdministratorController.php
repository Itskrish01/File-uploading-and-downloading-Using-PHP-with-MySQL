<?php
namespace Administrator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Administrator\Form\AdminMailForm;
use Administrator\Model\AdminMail;
use Administrator\Form\AdduserForm;
use Administrator\Model\Adduser;
use Application\Model\UserPassword;
use Application\Model\ContractorTable;
use Zend\Math\Rand;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Application\Form\SearchForm;



class AdministratorController extends AbstractActionController
{
    
    protected $userTable;
    protected $paypalTable;
    protected $adminMailTable;
    protected $taxonomyTable;
    protected $fieldsTable;
    protected $roleTable;
    protected $userRoleTable;
    protected $authService;
    protected $planTable;
    protected $pagesTable;
    protected $transactionTable;
    protected $menuTable;
    protected $propertymetaTable;
    protected $announcementTable;
    // protected $contractorTable;
    protected $artTable;
		
		
		
		
    public function indexAction()
    {  	$status1="active"; 
		$status2="pending"; 
		$status3="closed"; 
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$user_role = $this->get_current_role($id);
		
		if ($user_role == 'client' || $user_role == 'broker') {
            return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'option'));
        }
		
        $users = ($user_role == 'administrator' ) ? $this->getUserTable()->fetchAll() : $this->getUserTable()->fetchByRole($id,$user_role);
        $activeUsers = ($user_role == 'administrator' ) ? $this->getUserTable()->fetchAllActive() : $this->getUserTable()->fetchByRoleActive($id,$user_role);
        $deactiveUsers = ($user_role == 'administrator' ) ? $this->getUserTable()->fetchAllDeactive() : $this->getUserTable()->fetchByRoleDeactive($id,$user_role);
		
		$records = ($user_role == 'administrator') ? $this->getPropertyMetaTable()->getFiles() : $this->getPropertyMetaTable()->getFilesByRole($id,$user_role);
		
	/* 	$unreviewArr = array();
		foreach($records as $rec):

			$file = explode('|',$rec['meta_value']);
			
			$rev = $this->getPropertyMetaTable()->newcheckReviewFiles($rec['property_id'],$rec['client_id'],$rec['dsr']);
			
			$newarr = explode('|',$rev['meta_value']);
			
			$result=array_diff($file,$newarr);
			if(!empty($result))
			{
				foreach($result as $val){
					$propAddress = $this->getPropertyMetaTable()->get_property_address($rec['property_id']);
					$city = $this->getPropertyMetaTable()->get_property_meta($rec['property_id'],'city',$rec['dsr']);
					$unreviewArr[] = $val."|".$rec['dsr']."|".$rec['property_id']."|".$rec['client_id']."|".$rec['user_name']."|".$propAddress."|".$city; 
				}
			}

		endforeach; */
		$newUnreview = $this->getPropertyMetaTable()->getFromUnreview('unreviewdoc');
		$unreviewArr = json_decode($newUnreview['val']);
		$agen_unreviwe_doc = array();
		// $allClients = $this->getUserTable()->fetchClientsByAgentId($id);
		
		
		if($user_role == 'agent')
		{
			$underClients = $this->getUserTable()->fetchClientsByAgentId($id);
			$underClientsids = array_map(create_function('$arr', 'return $arr["user_id"];'), $underClients);
			$underClientsids[] = $id;
			
			$underClientsids = array_diff($underClientsids, array($id)); //Removing Self uploaded Documents
			
			foreach($unreviewArr as $unreview){	
				$user_id = explode("|",$unreview)[3];
				$doc_upload_user_role = $this->get_current_role($user_id);
				if(in_array($user_id,$underClientsids)){
					
					$agen_unreviwe_doc[] = $unreview;
				}
			}
			$unreviewArr = $agen_unreviwe_doc;
		}
		
		//$results=($user_role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$user_role);
		
		$unverifiedresults=($user_role == 'administrator') ? $this->getUserTable()->fetchallUnverifiedClient() : $this->getUserTable()->fetchUnverifiedClientByRole($id,$user_role);
		$results_active=($role == 'administrator') ? $this->getUserTable()->fetchallClientbyStatus($status1) : $this->getUserTable()->fetchClientByRole($id,$role,$status1);
		$results_pending=($role == 'administrator') ? $this->getUserTable()->fetchallClientbyStatus($status2) : $this->getUserTable()->fetchClientByRole($id,$role,$status2);
		$results_closed=($role == 'administrator') ? $this->getUserTable()->fetchallClientbyStatus($status3) : $this->getUserTable()->fetchClientByRole($id,$role,$status3);
		$unr_comments= ($role == 'administrator') ? $this->getUserTable()->fetchallUnrcomments() : $this->getUserTable()->fetchUnverifiedClientByRole($id,$role);
        
		
        
        $urole = $this->getUserTable()->get_role_id($id);
        $announcements = $this->getAnnouncementTable()->getAnnouncementsForMe($id, $urole, $user_role);
        $myViewedAnnouncement = $this->getAnnouncementTable()->viewedByUser($id);
        $myNotification = json_decode($myViewedAnnouncement[0]['read_notification_id']);
        
        //echo "<pre>"; print_r($myNotification); die;
		$this->layout('layout/admin');
		
        return array(
			'unr_comments'=>$unr_comments,
			'results_active' => count($results_active),
			'results_pending' => count($results_pending),
			'results_closed' => count($results_closed),
			'users' => count($users),
            'activeUsers'=>count($activeUsers),
            'deactiveUsers'=>count($deactiveUsers),
			'properties' => count($records),
			'unreviewedfiles' => count($unreviewArr),
			'unverifiedresults' => count( $unverifiedresults),
			'announcements' => $announcements,
            'myNotification'=>$myNotification,
            'user_role' => $user_role,
        ); 
    }
    
	
	 public function mlmAction()
    {  	
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$user_role = $this->get_current_role($id);
		
		$data = $this->getUserTable()->getmlm($id);
		$this->layout('layout/admin');
	
        $level1 = array();
		$level2 = array();
		$level3 = array();
		$level4 = array();
        foreach($data as $dta){
            if($dta['Level'] == 0){
                $username = $dta['firstname'];
            }
            else if($dta['Level'] == 1){
                $level1[] = $dta;
            }
            else if($dta['Level'] == 2){
                $level2[] = $dta;
            }
            else if($dta['Level'] == 3){
                $level3[] = $dta;
            }
            else if($dta['Level'] == 4){
                $level4[] = $dta;
            }
        }
        return array(
            'username' => $username,
			'data' => $level1,
			'data2' => $level2,
			'data3' => $level3,
			'data4' => $level4,
        );  
    }
    
    public function migrateMetaRecordAction()
	{
		$request = $this->getRequest();

		if($request->isPost())
		{
			$this->getUserTable()->executeMigrate();
			die('done');
		}
		die;
	}
	
    /**
     * **********************************************************************************
     *                  Announcement SECTION
     * **********************************************************************************
     **/
    
    public function announcementAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $user_role = $this->get_current_role($id);
        
        $announcements = $this->getAnnouncementTable()->fetchAll();
        $roles = $this->getUserTable()->getRoles();
		$request = $this->getRequest();
		foreach($roles as $role):
			$rolesArr[] = $role;
		endforeach;
        
        $page =1;
	    $perPage = 100;
	    $announcementPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($announcements));
	    $announcementPaginator->setCurrentPageNumber($page);
        $announcementPaginator->setItemCountPerPage($perPage);	
	    
        $this->layout('layout/admin');
        return array(
            'announcements'    => $announcementPaginator,
			'count' => count($announcements),
			'records_number' => $perPage,
            'roles'=>$rolesArr,
			'user_role'=>$user_role
            
        );	
    }
	
	
	/**
	 *  
	 *  @return - Return_Description
	 *  
	 *  @details - Details
	 *  
	 *  
	 */
	public function reminderAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
		
        $user_role = $this->get_current_role($user_id);
		
		$all_fetch_data = $this->getRemindersTable()->fetchAll();
		
		// print_r($data);
		
		$request = $this->getRequest();
		
		$flash_set = false;
		
		if($request->isPost())
		{
			$data = $request->getPost();
			
			$array_data_insert = array( 'subject'=> $data['subject'], 'message'=> $data['template'],'user_created'=> $user_id,'user_modified'=> $user_id, 'global' => 1 ); 
			 // print_r($data);
			$return_data = $this->getRemindersTable()->insert_template($array_data_insert );
 
			if($return_data > 0){
				$_SESSION['reminder_flash_insert'] = "true";
			}
			
			return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'reminder'));
			
		}
 
        $this->layout('layout/admin');
		
        return array(
            'all_fetch_data'    => $all_fetch_data,
 
            
        );	
    }	
	
	/**
	 *  
	 *  @return - Return_Description
	 *  
	 *  @details - Details
	 *  
	 *  
	 */
	public function userReminderTemplatesAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
		
        $user_role = $this->get_current_role($user_id);
		
		$all_fetch_data = $this->getRemindersTable()->fetchAll($user_id);
		
		// print_r($data);
		
		$request = $this->getRequest();
		
		$flash_set = false;
		
		if($request->isPost())
		{
			$data = $request->getPost();
			
			$array_data_insert = array( 'subject'=> $data['subject'], 'message'=> $data['template'],'user_created'=> $user_id,'user_modified'=> $user_id ); 
			 // print_r($data);
			$return_data = $this->getRemindersTable()->insert_template($array_data_insert );
 
			if($return_data > 0){
				$_SESSION['reminder_flash_insert'] = "true";
			}
			
			return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'user-reminder-templates'));
			
		}
 
        $this->layout('layout/admin');
		
        return array(
            'all_fetch_data'    => $all_fetch_data,
 
            
        );	
    }

	/**
	 *  
	 *  @return - Return_Description
	 *  
	 *  @details - Details
	 *  
	 *  
	 */
	public function setReminderAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
		
        $user_role = $this->get_current_role($user_id);
		
		$fetchAll_user_reminders = $this->getRemindersTable()->fetchAll_user_reminders($user_id);
		
		$all_fetch_active_templates = $this->getRemindersTable()->fetchAll_specific_templates(array('active'=>1, 'global' => 1));
		$all_fetch_active_templates_custom = $this->getRemindersTable()->fetchAll_specific_templates(array('active'=>1, 'user_created' => $user_id, 'global' => 0));
 
		$request = $this->getRequest();
		
		$flash_set = false;
		
		if($request->isPost())
		{
			$data = $request->getPost();
 
			$array_data_insert = array( 'template_id'=> $data['template_id'], 'custom_message'=> $data['template'], 'subject'=> $data['subject'], 'reminder_date'=> $data['reminder_date'], 'user_id'=> $user_id ); 
			
			$return_data = $this->getRemindersTable()->insert_reminder($array_data_insert );
 
			if($return_data > 0){
				$_SESSION['set_reminder_flash'] = "true";
			}
			
			return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'set-reminder'));
			
		}
 
        $this->layout('layout/admin');
		
        return array(
     
            'fetchAll_user_reminders'    => $fetchAll_user_reminders,
            'all_fetch_active_templates'    => $all_fetch_active_templates,
            'all_fetch_active_templates_custom'    => $all_fetch_active_templates_custom,
 
        );	
    }
	
	
	public function reminderTemplateActDeactAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
 
		$request = $this->getRequest();
 
		if($request->isPost())
		{
			$data = $request->getPost();
 
			if($this->getRemindersTable()->reminder_template_activate_deactive( $data['id'] )){
				
				echo 1;
				
			}else{
				
				echo 0;
				
			}
			
		}
		
		die;
    }	
	
	
	public function getpropdetailsAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
 
		$request = $this->getRequest();
 
		if($request->isPost())
		{
			$data = $request->getPost();
 
			$prop_meta = $this->getPropertyMetaTable()->get_prop_meta_details( $data['id'] ); 
	 
			if(!empty($prop_meta) && is_array($prop_meta)){
				
				$getting_all_keys = array_map(function($element){return $element['meta_key'];}, $prop_meta);
				
				$getting_all_values = array_map(function($element){return $element['meta_value'];}, $prop_meta);
 
				$key_value_data = array("status" => 200, 'data' => (array_combine($getting_all_keys, $getting_all_values)));
			
			}
			else{
				$key_value_data = array("status" => 500, 'data' => NULL);
			}
			
			echo json_encode($key_value_data);
			
		}
		
		die;
    }
		
		
	public function deleteReminderTemplateAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
 
		$request = $this->getRequest();
 
		if($request->isPost())
		{
			$data = $request->getPost();
 
			if($this->getRemindersTable()->delete_reminder_template( $data['id'] )){
				
				echo 1;
				
			}else{
				
				echo 0;
				
			}
			
		}
		
		die;
    }
		
		
	public function deleteReminderAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
 
		$request = $this->getRequest();
 
		if($request->isPost())
		{
			$data = $request->getPost();
 
			if($this->getRemindersTable()->delete_reminder( $data['id'] )){
				
				echo 1;
				
			}else{
				
				echo 0;
				
			}
			
		}
		
		die;
    }
	
	/**
	 *  
	 *  @return - Return_Description
	 *  
	 *  @details - Details
	 *  
	 *  
	 */
	public function sendremindermailAction()
    {   
		
		$return_data = $this->getRemindersTable()->get_reminders( );
		
		// print_r($return_data);
		
		foreach($return_data as $data){
			
			$user_data = $this->getUserTable()->user_profile($data['user_id']);
			
			$sender_mail_id = "no-reply@nrgadmin.com";
			$reciever_mail_id = $user_data['email'];
			
			$headers  = "From: NRG Email Reminder < $sender_mail_id >\n";
			$headers .= "Cc: NRG Email Reminder < $sender_mail_id >\n"; 
			$headers .= "X-Sender: NRG Email Reminder < $sender_mail_id >\n";
			$headers .= 'X-Mailer: PHP/' . phpversion();
			$headers .= "X-Priority: 1\n"; // Urgent message!
			$headers .= "Return-Path: $sender_mail_id\n"; // Return path for errors
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=iso-8859-1\n";
			
			mail($reciever_mail_id, $data['subject'], $data['custom_message'], $headers, '-fno-reply@nrgadmin.com' );
			
			$this->getRemindersTable()->set_reminder_sent( $data['id'] );
			
		}
		
		
		die;
	}
	
    
    public function saveAnnouncementAction()
    {
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $request = $this->getRequest();
		   
        if ($request->isPost()) 
        {
                $announcementdata = $request->getPost();
                $this->getAnnouncementTable()->saveAnnouncement($id, $announcementdata);
                $this->flashMessenger()->addSuccessMessage('Announcement Added');
                return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'announcement'));
        }
		return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'announcement'));	
    }
    
    public function deleteAnnouncementAction()
    {
        $announcement_id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$announcement_id &&!is_int($announcement_id)) return $this->redirect()->toRoute('administrator', array('action' => 'announcement'));
        
        
        try {
            
            //current user login id
            $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
            $announcementdata =  $this->getAnnouncementTable()->deleteAnnouncement($announcement_id);
          
            $this->flashMessenger()->addSuccessMessage('Announcement Deleted Successfully !');
            return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'announcement'));
            exit; 
         
        }catch (\Exception $ex){
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'announcement'
            ));
        }
    }
    
    public function announcementStatusAction()
     {
        $announcement_id = (int) $this->params()->fromRoute('id', 0);
      
        if (!$announcement_id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'announcement'
            ));
        }
        try {
            $status = $this->getAnnouncementTable()->getStatus($announcement_id);
            $newstatus =($status['status'] == 1 )? 0: 1;
            $this->getAnnouncementTable()->updateStatus($announcement_id,$newstatus);
            return true;
        }
		
        catch (\Exception $ex) {
			
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'announcement'
            ));
        }
      
     }
     
     public function viewAnnouncementAction()
     {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user_role = $this->get_current_role($id);
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $notify_id = $request->getPost('notify_id');
            $notification = $this->getAnnouncementTable()->getAnnouncement($notify_id);
            $remove_notification = $this->getAnnouncementTable()->removeAnnouncementFromUser($id,$notify_id);
            echo json_encode($notification[0]); die;
        }
        die;
     }
    
    /**
     * **********************************************************************************
     *                  USER MANAGEMENT SECTION
     * **********************************************************************************
     **/
    
     /**
      * User manager display all active and inactive user listing action process here
      * @users User data Object
      * @author developed by Trs Software Solutions
      * @return array
      **/
     public function usermanagerAction()
     {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user_role = $this->get_current_role($id);
		
		if ($user_role == 'client' || $user_role == 'broker') {
            return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'option'));
        }
		
        $users = ($user_role == 'administrator' ) ? $this->getUserTable()->fetchAll() : $this->getUserTable()->fetchByRole($id,$user_role);
		$roles = $this->getUserTable()->getRoles();
		$request = $this->getRequest();
		foreach($roles as $role):
			$rolesArr[] = $role;
		endforeach;
		//echo "<pre>"; print_r($users); die;
		$page =1;
	    $perPage = 15;
	    $userPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($users));
	    $userPaginator->setCurrentPageNumber($page);
        $userPaginator->setItemCountPerPage($perPage);	
	    
        $this->layout('layout/admin');
        return array(
            'users'    => $userPaginator,
			'count' => count($users),
			'records_number' => $perPage,
            'roles'=>$rolesArr,
			'user_role'=>$user_role
            
        );	
     }
	 
	 
	 public function pagesAction()
     {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user_role = $this->get_current_role($id);
		
		/*if ($user_role == 'client' || $user_role == 'broker') {
            return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'option'));
        }*/
		
       // $users = ($user_role == 'administrator' ) ? $this->getUserTable()->fetchAll() : $this->getUserTable()->fetchByRole($id,$user_role);
		$pages = $this->getPagesTable()->getPages();
		
		$request = $this->getRequest();
		
		
		/* $page =1;
	    $perPage = 15;
	    $userPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($pages));
	    $userPaginator->setCurrentPageNumber($page);
        $userPaginator->setItemCountPerPage($perPage);	 */
	    
        $this->layout('layout/admin');
        return array(
            'pages'    => $pages,
			'count' => count($pages)
            
        );	
     }
	 
	 public function addPageAction()
     {
		 
		 //$config = '/uploads/'; // Relative to domain name
		//$configpath = $_SERVER['DOCUMENT_ROOT'] . $config;
		//print_r($configpath); die;
		
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user_role = $this->get_current_role($id);
		
		$pages = $this->getPagesTable()->getPages();
		
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$data = $request->getPost();
			$dataArray = array('title' => $data['title'], 'page_content' => $data['page_content'], 'permalink' => $data['permalink'], 'url' => ' http://www.nrgadmin.com/page/'.$data['permalink']);
			//echo "<pre>"; print_r($dataArray); die;
			unset($data['submit']);
			$updatepage = $this->getPagesTable()->addpage($dataArray);
			return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'pages'));
			
		}
	
	    
        $this->layout('layout/admin');
        return array(
            'pages'    => $pages,
			'count' => count($pages)
            
        );	
     }
     
     /**
      * User manager display all active and inactive user listing pagination action process here
      * @users User data Object
      * @author developed by Trs Software Solutions
      * @return array
      **/
	 public  function userPaginatorAction()
	 {
	   
        $users = $this->getUserTable()->fetchAll();
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $userPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($users));
        $userPaginator->setCurrentPageNumber($page);
        $userPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/user-paginator')
           ->setVariables(array(
                'users'    => $userPaginator,
			    'count' => count($records),
			    'records_number' => $perPage,
		    ));
           
        return $view;
	 }
     
     /**
      * User manager display all active and inactive user listing pagination layout action process here
      * @users User data Object
      * @author developed by Trs Software Solutions
      * @return array
      **/
	 public function userPaginatorLayoutAction()
	 {
	    $users = $this->getUserTable()->fetchAll();
       
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $userPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($users));
        $userPaginator->setCurrentPageNumber($page);
        $userPaginator->setItemCountPerPage($perPage);	
	   
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/user-paginator-layout')
           ->setVariables(array(
                'users'    => $userPaginator,
			    'count' => count($records),
			    'records_number' => $perPage,
		    ));
           
        return $view;
	 }
	 
	public function viewUserAction()
	 {
		$request = $this->getRequest();
        
		if($request->isPost())
		{
			$user_id = $request->getPost('user_id');
			$results = $this->getUserTable()->user_profile($user_id);
			//print_r($results);die;
			// $returnData = array(
				// 'email'         =>    $results['email'],
				// 'firstname'     =>    $results['firstname'],
				// 'lastname'      =>    $results['lastname'],
				// 'mobile'        =>    $results['mobile'],
				// 'role'          =>    $results['role'],
				// 'agent'         =>    $results['agent'],
				// 'branchmanager' =>    $results['branchmanager']
			// );
			
			$roles = $this->getUserTable()->getRoles();
			foreach($roles as $role):
				$rolesArr[] = $role;
			endforeach;
			
			$branchamanagers = $this->getUserTable()->fetchByname('branchmanager');
			
			$agents = $this->getUserTable()->fetchByname('agent');
			
			$view = new ViewModel();
			$view->setTerminal(true)
			   ->setVariables(array(
					'user_id'         => $user_id,
					'results'         => $results,
					'branchamanagers' => $branchamanagers,
					'agents'          => $agents,
					'roles'          => $rolesArr
				));
			   
			return $view;
		}
	 }
	 
	 public function editpagesAction()
	 {
		 
		$request = $this->getRequest();
		
        $page_id=$_REQUEST['p_id'];
		
		//$pagedetails = $this->getPagesTable()->fetch($page_id);
		//echo "<pre>"; print_r($pagedetails); die;
		$this->layout('layout/admin');

		if($request->isPost())
		{
			$data = $request->getPost();
			
			//echo "<pre>"; print_r($data); die;
			$dataArray = array('title' => $data['title'], 'permalink' => $data['permalink'], 'page_content' => $data['page_content'], 'url' => ' http://www.nrgadmin.com/page/'.$data['permalink']);
			$updatepage = $this->getPagesTable()->updatepage($data['id'],$dataArray);
		}
		$pagedetails = $this->getPagesTable()->fetchbyid($page_id);
		return array('pagedetail'=>$pagedetails);
			
		
	 }
	 
	 public function deletepageAction()
	 {
		 ///$request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
		if(!empty($id))
		{
			
			$results = $this->getPagesTable()->deletecurrentpage($id);
			  
			
		}
		return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'pages'));
		die;
	 }
	 
	 
	 public function checkEmailAction()
	 {
		$request = $this->getRequest();
        
		if($request->isPost())
		{
			$user_id = $request->getPost('user_id');
			$email   = trim($request->getPost('email'));
			
			$check = $this->getUserTable()->getEmailCheck($user_id,$email);
			if(isset($check['user_id'])){
				echo "404";
				die;
			}else{
				echo "200";
				die;
			}			
		}
		die;
	 }
	 
	 public function updateUserAction()
	 {
		$request = $this->getRequest();
        
		if($request->isPost())
		{
			//print"<pre>";print_r($request->getPost());die;
			
			$user_id = $request->getPost('user_id');
			$data = array(
				'firstname'      =>   trim($request->getPost('firstname')),
				'lastname'       =>   trim($request->getPost('lastname')),
				'email'          =>   trim($request->getPost('email')),
				'mobile'         =>   trim($request->getPost('mobile')),
				'role'           =>   trim($request->getPost('role')),
				'branchmanager'  =>   trim($request->getPost('branchmanager')),
				'agent'          =>   trim($request->getPost('agent')),
			);
			
			$this->getUserTable()->updateUser($data,$user_id);
			
			$this->flashMessenger()->addSuccessMessage('User Details Update Successfully !');
			return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
		}
		return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
	 }
     
     /**
      * View single user profile action process here
      * @id user id
      * @author developed by Trs Software Solutions
      * @return array
      **/
	public function viewprofileAction()
	{
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		//$user_role = $this->get_current_role($id);		
		$results = $this->getUserTable()->user_profile($id);

		 $this->layout('layout/admin');
		return array('user'=>$results);
				 
	}
	
     public function profileAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$id &&!is_int($id)) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        } 
        
        try {
          $user =  $this->getUserTable()->getUser($id); 
         }catch (\Exception $ex) {
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'category'
            ));
        }
        
        $this->layout('layout/admin');
        return array('user'=>$user);
     }
     
     /**
      * Delete user Profile Action Process here
      * @user_id user id
      * @author developed by Trs Software Solutions
      * @return void
      **/
     
     public function deleteuserAction()
     {
       $user_id = (int) $this->params()->fromRoute('id', 0);
        
         if (!$user_id &&!is_int($user_id)) return $this->redirect()->toRoute('administrator', array('action' => 'usermanager'));
        
        
        try {
            
          //current user login id
          $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
          
          if($current_user_id ==$user_id):
             
             $this->flashMessenger()->addErrorMessage('Current User Profile can not be deleted !');
             return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
             exit;
          endif;
            
          $user =  $this->getUserTable()->deleteUser($user_id);
          
          $this->flashMessenger()->addSuccessMessage('User Profile Deleted Successfully !');
          return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
          exit; 
         
         }catch (\Exception $ex){
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
     }
	 
	 /*  public function deletepageAction()
     {
       $user_id = (int) $this->params()->fromRoute('id', 0);
        
         if (!$user_id &&!is_int($user_id)) return $this->redirect()->toRoute('administrator', array('action' => 'usermanager'));
        
        
        try {
            
          //current user login id
          $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
          
          if($current_user_id ==$user_id):
             
             $this->flashMessenger()->addErrorMessage('Current User Profile can not be deleted !');
             return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
             exit;
          endif;
            
          $user =  $this->getUserTable()->deleteUser($user_id);
          
          $this->flashMessenger()->addSuccessMessage('User Profile Deleted Successfully !');
          return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
          exit; 
         
         }catch (\Exception $ex){
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
     } */
	 
	 
    /**
     * Change user password forcefully action process here
     * @user_id user_id
     * @current_user_id Logined user ID
     * @author  developer by Trs Software solutions
     * @return void
     **/
    public function changePasswordAction()
	{
	    $user_id = (int) $this->params()->fromRoute('id', 0);
        //$id = $this->zfcUserAuthentication()->getIdentity()->getId();  //edit this
		$user_role = $this->get_current_role($user_id);	       //edit this
        if (!$user_id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
	   $password = $this->getRequest()->getPost('password');
	   if ($user_role == 'administrator' || $user_role == 'branchmanager') {     //edit this
	   if(!empty($password))
	   {
	     
		 $this->getUserTable()->changePassword($user_id,trim($password));
	     $this->flashMessenger()->addSuccessMessage('Change Password Successfully');
         return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
	   }
	   }  //edit
	   $this->flashMessenger()->addErrorMessage('Internal Error Parameters Missing');
       return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
	 
	}
     
     /**
     * Change user Role forcefully action process here
     * @user_id user_id
     * @current_user_id Logined user ID
     * @author  developer by Trs Software solutions
     * @return void
     **/
     public function changeRoleAction()
     {
        $user_id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$user_id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
	   $role = $this->getRequest()->getPost('role_id');
	   if(!empty($role))
	   {
	     
		 $this->getUserTable()->changeRole($user_id,trim($role));
	     $this->flashMessenger()->addSuccessMessage('Change Role Successfully');
         return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
	   }
	   $this->flashMessenger()->addErrorMessage('Internal Error Parameters Missing');
       return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'usermanager'));
     } 
    
     /**
      * Change user Status like active/deactive action process here
      * @user_id Get User Id
      * @author developed by Trs Software Solutions
      * @return void
      **/
     public function userstatusAction()
     {
        $user_id = (int) $this->params()->fromRoute('id', 0);
      
        if (!$user_id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
        try {
            $state =$this->getUserTable()->getState($user_id);
            $status =($state['state']==1)? 0: 1;
            $this->getUserTable()->updateStatus($user_id,$status);
            return true;
        }
		
        catch (\Exception $ex) {
			
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
      
     }
     
      /**
      * *****************************************************************************************
      *                         ROLE MANAGEMENT SECTION
      *******************************************************************************************
      **/
       /**
       * Add New Role and Manage Role
       * 
       * @author developed by Trs Software Solutions
       * @return array
       **/
       public function roleAction()
     {
        $request = $this->getRequest();
        
        if($request->isPost())
        {
            $rolename = $request->getPost('role_name');
            $rolename = strtolower(trim($rolename));
            if(empty($rolename)){
               $this->flashMessenger()->addErrorMessage('Role field can not be empty');
               return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
            }
            //role name is exist or not
           $validateRole =  $this->getRoleTable()->existRole($rolename);
          
           if(count($validateRole)>0 && !empty($validateRole))
           {
              $this->flashMessenger()->addErrorMessage('Role name already exist !');
	          return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
           }
           
           $this->getRoleTable()->saveRole($rolename); 
           $this->flashMessenger()->addSuccessMessage('Add Role Successfully !');
           return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
        }
        $getAllRole = $this->getRoleTable()->fetchAll();
        $this->layout('layout/admin');
        $view = new viewModel();
        $view->setVariable('role',$getAllRole);
        return $view;
     }
     
     /**
       * Update existing Roles
       * @roleid get Role Id
       * @author developed by Trs Software Solutions
       * @return void
       **/
     public function updateRoleAction()
     {
        $roleid = (int) $this->params()->fromRoute('id', 0);
        
        if (!$roleid &&!is_int($roleid)) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'role'
            ));
        }
        $request = $this->getRequest();
        if($request->isPost())
        {
            $rolename = $request->getPost('role_name');
            $rolename = trim(strtolower($rolename));
            if(empty($rolename)){
               $this->flashMessenger()->addErrorMessage('Role field can not be empty');
               return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
            }
            //role name is exist or not
           $validateRole =  $this->getRoleTable()->existRole($rolename);
           
           if(!empty($validateRole))
           {
              $this->flashMessenger()->addErrorMessage('Role name already exist !');
	          return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
           }
           
           $this->getRoleTable()->updateRole($rolename,$roleid); 
           $this->flashMessenger()->addSuccessMessage('Update Role Successfully !');
           return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));
        }  
     }
     
      /**
       * Delete existing Roles
       * @roleid get Role Id
       * @author developed by Trs Software Solutions
       * @return void
       **/
     public function deleteRoleAction()
     {
        $roleid = (int) $this->params()->fromRoute('id', 0);
        
        if (!$roleid && !is_int($roleid)) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'role'
            ));
        }
        
        try {
            $this->getRoleTable()->deleteRole($roleid); 
            $this->flashMessenger()->addSuccessMessage('Delete Role Successfully !');
            return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'role'));    
        }
        catch (\Exception $ex) {
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'role'
            ));
        }
      
     }
     
      /**
       * Active / Deactive Roles action Process here
       * @roleid get Role Id
       * @author developed by Trs Software Solutions
       * @return void
       **/
     public function changeRoleStatusAction()
     {
        $role_id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$role_id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'role'
            ));
        }
        
        try {
            $state =$this->getRoleTable()->getRole($role_id);
           
            $status =($state['status']=='0')? '1': '0';
            $this->getRoleTable()->changeStatus($status,$role_id);
            echo true;
            exit;     
        }
        catch (\Exception $ex) {
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'role'
            ));
        }
     }
     
     /**
       * Update User Role Permission Action Process Here
       * @author developed by Trs Software Solutions
       * @return void
       **/
     public function userRolePermissionAction()
     {
        $resultset = $this->getUserRoleTable()->fetchAll();
        $page =1;
	    $perPage =10;
	    $userRolePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultset));
	    $userRolePaginator->setCurrentPageNumber($page);
        $userRolePaginator->setItemCountPerPage($perPage);	
	    
        $this->layout('layout/admin');
        return array(
            'paginator'    => $userRolePaginator,
			'count' => count($resultset),
			'records_number' => $perPage,
            
        );
        
     }
     public function searchUserRolePermissionAction()
     {
        $request = $this->getRequest();
        if($request->isPost()){
            $searchString = $request->getPost('search','');
            $resultSet = $this->getUserRoleTable()->searchRecords($searchString);
            
            $page =1;
            $perPage =10;
            $searchPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultSet));
            $searchPaginator->setCurrentPageNumber($page);
            $searchPaginator->setItemCountPerPage($perPage);	
	    }
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/search-user-role-permission')
           ->setVariables(array(
                'paginator'    => $searchPaginator,
			    'count'        => count($resultSet),
			    'records_number' => $perPage,
                'search'=>$searchString
		    ));
           
        return $view;
     }
     
     
     /**
      * *****************************************************************************************
      *                         ADMIN MAIL SECTION
      *******************************************************************************************
      **/
      
       public function mailboxAction(){
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $form = new AdminMailForm($dbAdapter);
        
        $request =$this->getRequest();
        
        if($request->isPost()):
       
           $adminmail = new AdminMail();
           $form->setInputFilter($adminmail->getInputFilter());
           $form->setData($request->getPost());
           
           if($form->isValid()):
            
             $adminmail->exchangeArray($form->getData());
             
             $this->removeSlashes($adminmail);
              $draft =$request->getPost('draft');
             if(isset($draft)):
               $adminmail->status ='draft';
             else:
              $adminmail->status ='sent';
             endif;
             //mail process goes here to send all mails
             
             
             $adminmail->sender= 'administrator@gmail.com';
             $this->getAdminMailTable()->saveMails($adminmail);
             $this->flashMessenger()->addSuccessMessage('Email Sent successfully');
	         return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'mailbox'));
             exit;
           endif;
         endif;
         
         // get All recieved Mail
        $revieve = $this->getAdminMailTable()->recieveMail();
        
        $recieveArr =array();
        $this->objToArray($revieve,$recieveArr);
      
        $page =1;
    	$perPage = 15;
    	$recievePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($recieveArr));
    	$recievePaginator->setCurrentPageNumber($page);
    	$recievePaginator->setItemCountPerPage($perPage);	
	
        $this->layout('layout/admin');
        return array(
            'paginator'    => $recievePaginator,
			'records_number' => $perPage,			
            'form'	=> $form
        );   
       
      }
      public function viewMailAction()
      {
         // get All recieved Mail
        $revieve = $this->getAdminMailTable()->recieveMail();
        
        $recieveArr =array();
        $this->objToArray($revieve,$recieveArr);
        
        $page =1;
    	$perPage = 15;
    	$recievePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($recieveArr));
    	$recievePaginator->setCurrentPageNumber($page);
    	$recievePaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/viewmail')
           ->setVariables(array(
               'paginator' => $recievePaginator,
               'count' => $revieve->count(),
               'records_number' => $perPage,
		    ));
           
        return $view;
        
      }
      public function sentMailAction()
      {
        // get All sent Mail
        $sent = $this->getAdminMailTable()->sentMail();
        
        $sentArr =array();
        
        $this->objToArray($sent,$sentArr);
        
        $page =1;
    	$perPage = 15;
    	$sentPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($sentArr));
    	$sentPaginator->setCurrentPageNumber($page);
    	$sentPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/sentmail')
           ->setVariables(array(
               'paginator' => $sentPaginator,
               'count' => $sent->count(),
               'records_number' => $perPage,
		    ));
           
        return $view;
        
      }
       public function draftMailAction()
       {
         
         // get All draft Mail
        $draft = $this->getAdminMailTable()->draftMail();
        
        $draftArr =array();
        $this->objToArray($draft,$draftArr);
        
        $page =1;
    	$perPage = 15;
    	$draftPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($draftArr));
    	$draftPaginator->setCurrentPageNumber($page);
    	$draftPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/draftmail')
           ->setVariables(array(
               'paginator' => $draftPaginator,
               'count' => $draft->count(),
               'records_number' => $perPage,
		    ));
           
        return $view;
         
       }
       public function checkMailAction()
       {
          $request = $this->getRequest();
          $mail_id = $request->getPost('id');
          if (!$mail_id):
            die('Internal Error !');    
          endif;
         $this->getAdminMailTable()->updateViewMail($mail_id);
         $mailresult =$this->getAdminMailTable()->getMail($mail_id);
         //call ajax model to render this mail
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/checkmail')
           ->setVariables(array(
               'mail' => $mailresult,
		    ));
           
        return $view;  
       }
       
       public function viewDraftMailAction()
       {
          $request = $this->getRequest();
          $mail_id = $request->getPost('id');
          if (!$mail_id):
            die('Internal Error !');    
          endif;
         
         $mailresult =$this->getAdminMailTable()->getMail($mail_id);
         
         $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
         $form = new AdminMailForm($dbAdapter);
         
        
         
         //call ajax model to render this mail
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/send-draft-mail')
           ->setVariables(array(
               'mail' => $mailresult,
               'form' =>$form, 
		    ));
           
        return $view; 
       }
       
       
       public function sendDraftMailAction()
       {
          
            $mail_id = (int) $this->params()->fromRoute('id', 0);
            if (!$mail_id) {
                return $this->redirect()->toRoute('administrator', array(
                    'action' => 'mailbox'
                ));
            }
          
          
          $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
          $form = new AdminMailForm($dbAdapter);
        
          $request =$this->getRequest();
        
        if($request->isPost()):
       
           $adminmail = new AdminMail();
           $form->setInputFilter($adminmail->getInputFilter());
           $form->setData($request->getPost());
           
           if($form->isValid()):
            
             $adminmail->exchangeArray($form->getData());
             
             $this->removeSlashes($adminmail);
             
             //mail process goes here to send all mails
             
             $adminmail->status ='sent';
             $adminmail->sender= 'administrator@gmail.com';
             $this->getAdminMailTable()->updateMails($adminmail,$mail_id);
             $this->flashMessenger()->addSuccessMessage('Email Sent successfully');
	         return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'mailbox'));
             exit;
           endif;
         endif;
       }
       
       
       public function replyAction()
      {
        $request = $this->getRequest();
       
       if($request->isPost()):
           
           $reciever = $request->getPost('reciever');
           $parent_id =$request->getPost('parent_id');
           $subject =$request->getPost('subject');
           $body = $request->getPost('body');
         
          //get data
          $data =array('sender'=>'admin@gmail.com','reciever'=>$reciever,'parent_id'=>$parent_id,'subject'=>$subject,'body'=>$body,'status'=>'reply');
          $this->getAdminMailTable()->saveReplyMails($data);
          
          $this->flashMessenger()->addSuccessMessage('Reply Mail sent Successfully !');
          return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'mailbox'));
          exit; 
       
       endif; 
       
        
      }
      
      
    public function mailPaginatorAction()
    {
       $action =  (int)$this->getRequest()->getPost('action');
       $resultset = $this->getPaginatorRequest($action);
       
       $paginatorArr =array();
       $this->objToArray($resultset,$paginatorArr);
       
	   $page =  (int)$this->getRequest()->getPost('page', 0);
	   $perPage = (int)$this->getRequest()->getPost('per_page', 0);
	
	
	   $Paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($paginatorArr));
	   $Paginator->setCurrentPageNumber($page);
	   $Paginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/mail-paginator')
           ->setVariables(array(
                'paginator'    => $Paginator,
                'count' => $resultset->count(),
                'records_number' => $perPage,
                'type'=>$action
		    ));
           
        return $view;  
		
    }	
	
	public function mailPaginatorLayoutAction()
    {
	
        $action =  (int)$this->getRequest()->getPost('action');
        $resultset = $this->getPaginatorRequest($action);
        
        $paginatorArr =array();
        $this->objToArray($resultset,$paginatorArr);
       
    	$page =  (int)$this->getRequest()->getPost('page', 0);
    	$perPage = (int)$this->getRequest()->getPost('per_page', 0);
    	
    	$Paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($paginatorArr));
    	$Paginator->setCurrentPageNumber($page);
    	$Paginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/mail-paginator-layout')
           ->setVariables(array(
                'paginator'    => $Paginator,
                'count' => $resultset->count(),
                'records_number' => $perPage,
                'type'=>$action
		    ));
           
        return $view;  	
    }
	
	public function unrevieweddocAction()
	{
		       
        // ini_set("display_error",true);
		//set_time_limit(300);
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
        if (!$id) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'usermanager'
            ));
        }
		ini_set('max_execution_time', 0);
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$user_role = $this->get_current_role($id);
		
		
		$newUnreview = $this->getPropertyMetaTable()->getFromUnreview('unreviewdoc');
		
		$unreviewArr = json_decode($newUnreview['val']);
        $sortedOrderData = array();
        
		$agen_unreviwe_doc = array();
		
		if($user_role == 'agent')
		{
			$underClients = $this->getUserTable()->fetchClientsByAgentId($id);
			$underClientsids = array_map(create_function('$arr', 'return $arr["user_id"];'), $underClients);
			$underClientsids[] = $id;
			
			$underClientsids = array_diff($underClientsids, array($id)); //Removing Self uploaded Documents
			
			foreach($unreviewArr as $unreview){	
				$user_id = explode("|",$unreview)[3];
				$doc_upload_user_role = $this->get_current_role($user_id);
				if(in_array($user_id,$underClientsids)){
					
					$agen_unreviwe_doc[] = $unreview;
				}
			}
			$unreviewArr = $agen_unreviwe_doc;
		}
	
		/* $unreviewArr = array_reverse($unreviewArr); */
		
        usort($unreviewArr, function($a, $b) {
            $atime = explode('___',$a)[0];
            $btime = explode('___',$b)[0];
            
            return($atime > $btime);
        });
		
		
		$page =1;
	    $perPage = 50;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($unreviewArr));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		$this->layout('layout/admin');
        
		return array(
            'results'    => $propertyPaginator,
			'count' => count($unreviewArr),
			'records_number' => $perPage,
			'user_id' => $id,
            
        );
		//return array('results'=>$unreviewArr);
	}
    
    /**
     *  
     *  
     *  @details - fetches overdue files from db which has crossed Anticipated Closing Date 
     *  			currently it fetches data from offer-tab only.
     *  
     *  created on - February 15, 2018 PJ
     *  
     *  last modified on - April 02, 2018 sb
     *  
     *  mod 1 - If Anticipated Closing Date is null don't show it on Overdue Files section.
     *  
     *  mod 2 - if files are reviewed don't show them in Overdue Files section.
     *  
     *  
     */
    public function overdueFilesAction(){
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        $overdue_properties = $this->getPropertyMetaTable()->getOverdueFiles($current_user_id);
 
		$incr = 0;
		foreach($overdue_properties as $result){
 
			$files = explode("|",$result['files']);
			$verifyDoc = explode("|",$result['verifyDoc']);
 
				$vab = true;
				foreach($files as $single_file){
					if(!in_array($single_file,$verifyDoc )){
						 
						$vab = false;
						
						break;
					}
				}
 
			
			if($vab === true){
 
				unset($overdue_properties[$incr]);
			}

			$incr++;
 
		}
 
        $page =1;
	    $perPage = 50;
	    $overduePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($overdue_properties));
	    $overduePaginator->setCurrentPageNumber($page);
        $overduePaginator->setItemCountPerPage($perPage);
		$this->layout('layout/admin');
        
		return array(
            'results'    => $overduePaginator,
			'count' => count($overdue_properties),
			'records_number' => $perPage,
			'user_id' => $current_user_id,            
        );
    }
	

	
	
    public  function overdueFilesPaginatorAction()
	 {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        $overdue_properties = $this->getPropertyMetaTable()->getOverdueFiles($current_user_id);
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
		
		
		
        $overduePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($overdue_properties));
        $overduePaginator->setCurrentPageNumber($page);
        $overduePaginator->setItemCountPerPage($perPage);
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/overdue-files-paginator')
           ->setVariables(array(
                'results'    => $overduePaginator,
			    'count' => count($overduePaginator),
			    'records_number' => $perPage,
				'user_id' => $current_user_id,
		    ));
           
        return $view;
	 }
 
	 public function overdueFilesPaginatorLayoutAction()
	 {
	    if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
		
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        $overdue_properties = $this->getPropertyMetaTable()->getOverdueFiles($current_user_id);
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $overduePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($overdue_properties));
		
        $overduePaginator->setCurrentPageNumber($page);
        $overduePaginator->setItemCountPerPage($perPage);	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/overdue-files-paginator-layout')
           ->setVariables(array(
                'results'    => $overduePaginator,
			    'count' => count($overduePaginator),
			    'records_number' => $perPage,
				'user_id' => $current_user_id,
		    ));
           
        return $view;
	 }
    
    
		
    	/**
     *  
     *   
     *  
     *  @details - AnticipatedClosingDatePaginatorAction
     *  
     *  date- 22/03/18 sb
     *  
     */
    public  function AnticipatedClosingDatePaginatorAction()
	 {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
		
		$current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
		$role = $this->get_current_role($current_user_id);
 
        $AnticipatedClosingDateData = $this->getPropertyMetaTable()->anticipated_closing_date_Files($current_user_id, $role);
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $AnticipatedClosingDatePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($AnticipatedClosingDateData));
        $AnticipatedClosingDatePaginator->setCurrentPageNumber($page);
        $AnticipatedClosingDatePaginator->setItemCountPerPage($perPage);
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/anticipated-closing-date-paginator')
           ->setVariables(array(
                'results'    => $AnticipatedClosingDatePaginator,
			    'count' => count($AnticipatedClosingDatePaginator),
			    'records_number' => $perPage,
				'user_id' => $current_user_id,
		    ));
           
        return $view;
	 }
 
    	/**
     *  
     *   
     *  
     *  @details - AnticipatedClosingDatePaginatorLayoutAction
     *  
     *  date- 22/03/18 sb
     *  
     */
	 public function AnticipatedClosingDatePaginatorLayoutAction()
	 {
	    if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
		
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
		$role = $this->get_current_role($current_user_id);
 
        $AnticipatedClosingDateData = $this->getPropertyMetaTable()->anticipated_closing_date_Files($current_user_id, $role);
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $AnticipatedClosingDatePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($AnticipatedClosingDateData));
		
        $AnticipatedClosingDatePaginator->setCurrentPageNumber($page);
        $AnticipatedClosingDatePaginator->setItemCountPerPage($perPage);	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/anticipated-closing-date-paginator-layout')
           ->setVariables(array(
                'results'    => $AnticipatedClosingDatePaginator,
			    'count' => count($AnticipatedClosingDatePaginator),
			    'records_number' => $perPage,
				'user_id' => $current_user_id,
		    ));
           
        return $view;
	 }
    
	/**
	 *  
	 *  
	 *  
	 *  @details - anticipatedClosingDateAction
	 *  
	 *  date - 22/03/18 sb
	 *  
	 */
	public function anticipatedClosingDateAction(){
		
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
		$role = $this->get_current_role($current_user_id);
 
        $AnticipatedClosingDateData = $this->getPropertyMetaTable()->anticipated_closing_date_Files($current_user_id, $role);
        
        $page =1;
	    $perPage = 50;
	    $AnticipatedClosingDatePaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($AnticipatedClosingDateData));
	    $AnticipatedClosingDatePaginator->setCurrentPageNumber($page);
        $AnticipatedClosingDatePaginator->setItemCountPerPage($perPage);
		$this->layout('layout/admin');
        
		return array(
            'results'    => $AnticipatedClosingDatePaginator,
			'count' => count($AnticipatedClosingDateData),
			'records_number' => $perPage,
			'user_id' => $current_user_id,            
			'perPage' => $perPage,            
        );
		
    }
	
	
	 public  function unreviewedPaginatorAction()
	 {
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		/* $records = ($role === 'administrator') ? $this->getPropertyMetaTable()->getFiles() : $this->getPropertyMetaTable()->getFilesByRole($id,$role);
	   		
		$unreviewArr = array();
		foreach($records as $rec):
			$file = explode('|',$rec['meta_value']);
		
			$rev = $this->getPropertyMetaTable()->newcheckReviewFiles($rec['property_id'],$rec['client_id'],$rec['dsr']);
			
			$newarr = explode('|',$rev['meta_value']);
			
			$result=array_diff($file,$newarr);
			if(!empty($result))
			{
				foreach($result as $val){
					$propAddress = $this->getPropertyMetaTable()->get_property_address($rec['property_id']);
					$city = $this->getPropertyMetaTable()->get_property_meta($rec['property_id'],'city',$rec['dsr']);
					$unreviewArr[] = $val."|".$rec['dsr']."|".$rec['property_id']."|".$rec['client_id']."|".$rec['user_name']."|".$propAddress."|".$city; 
				}
			}
		endforeach; */
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$user_role = $this->get_current_role($id);
		
		
		$newUnreview = $this->getPropertyMetaTable()->getFromUnreview('unreviewdoc');
		$unreviewArr = json_decode($newUnreview['val']);		
		$agen_unreviwe_doc = array();
		
		if($user_role == 'agent')
		{
			$underClients = $this->getUserTable()->fetchClientsByAgentId($id);
			$underClientsids = array_map(create_function('$arr', 'return $arr["user_id"];'), $underClients);
			$underClientsids[] = $id;
			
			$underClientsids = array_diff($underClientsids, array($id)); //Removing Self uploaded Documents
			
			foreach($unreviewArr as $unreview){	
				$user_id = explode("|",$unreview)[3];
				$doc_upload_user_role = $this->get_current_role($user_id);
				if(in_array($user_id,$underClientsids)){
					
					$agen_unreviwe_doc[] = $unreview;
				}
			}
			$unreviewArr = $agen_unreviwe_doc;
		}
        usort($unreviewArr, function($a, $b) {
            $atime = explode('___',$a)[0];
            $btime = explode('___',$b)[0];
            
            return($atime > $btime);
        });
		
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($unreviewArr));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/unreviewed-paginator')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($unreviewArr),
			    'records_number' => $perPage,
				'user_id' => $id,
		    ));
           
        return $view;
	 }
 
	
	public function unreviewedPaginatorLayoutAction()
	{
	    $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		/* $records = ($role === 'administrator') ? $this->getPropertyMetaTable()->getFiles() : $this->getPropertyMetaTable()->getFilesByRole($id,$role);
		
		$unreviewArr = array();
		foreach($records as $rec):
			$file = explode('|',$rec['meta_value']);
			foreach($file as $ff):
				$ff=addslashes($ff);
				$rev = $this->getPropertyMetaTable()->checkReviewFiles($ff,$rec['property_id'],$rec['client_id'],$rec['dsr']);
				if(count($rev) > 0):
					continue;
				else:
					$propAddress = $this->getPropertyMetaTable()->get_property_address($rec['property_id']);
					$city = $this->getPropertyMetaTable()->get_property_meta($rec['property_id'],'city',$rec['dsr']);
					$unreviewArr[] = $ff."|".$rec['dsr']."|".$rec['property_id']."|".$rec['client_id']."|".$rec['user_name']."|".$propAddress."|".$city; 
				endif;
			endforeach;
		endforeach; */
		
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$user_role = $this->get_current_role($id);
		
		$newUnreview = $this->getPropertyMetaTable()->getFromUnreview('unreviewdoc');
		$unreviewArr = json_decode($newUnreview['val']);
		$agen_unreviwe_doc = array();
		
		if($user_role == 'agent')
		{
			$underClients = $this->getUserTable()->fetchClientsByAgentId($id);
			$underClientsids = array_map(create_function('$arr', 'return $arr["user_id"];'), $underClients);
			$underClientsids[] = $id;
			
			$underClientsids = array_diff($underClientsids, array($id)); //Removing Self uploaded Documents
			
			foreach($unreviewArr as $unreview){	
				$user_id = explode("|",$unreview)[3];
				$doc_upload_user_role = $this->get_current_role($user_id);
				if(in_array($user_id,$underClientsids)){
					
					$agen_unreviwe_doc[] = $unreview;
				}
			}
			$unreviewArr = $agen_unreviwe_doc;
		}
        usort($unreviewArr, function($a, $b) {
            $atime = explode('___',$a)[0];
            $btime = explode('___',$b)[0];
            
            return($atime > $btime);
        });
		
		
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($unreviewArr));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/unreviewed-paginator-layout')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($unreviewArr),
			    'records_number' => $perPage,
				'user_id' => $id,
		    ));
           
        return $view;
	}
	
	public function reviewdocAction()
	{
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die('not logged in');
        }
        $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
		$doc =  $this->getRequest()->getPost('doc');        
		$pid =  $this->getRequest()->getPost('pid');
		$cid =  $this->getRequest()->getPost('cid');
		$tab =  $this->getRequest()->getPost('tab');
		$u_id =  $this->getUserTable()->getPropUser($pid);
		$rev =  $this->getPropertyMetaTable()->revFile($doc,$pid,$cid,$tab);
        
        /* 2:47 PM 09 February, 2018 by PJ
         this function was added to log reviewer details for certain docs review
        */
        $log_data = array(
            'user_id'       => $current_user_id,
            'property_id'   => $pid,
            'tab'           => $tab,
            'doc_name'      => $doc,
        );
        $this->getPropertyMetaTable()->file_review_logger($log_data);
        /* Logger Ends here */
        
		$agent_email = $this->get_agent_email($u_id);

		$sub = "File Successfully Reviewed";
		$message = "File has been successfully reviewed.Check out by clicking below. <br><br><br><br>";
		$message .= "<a href='http://nrgadmin.com/application/index/propertylisting?p_id=".$pid."&u_id=".$u_id."'>http://nrgadmin.com/application/index/propertylisting?p_id=".$pid."&u_id=".$u_id."</a>";
		
		$headers = "From: NRG Administrator<randy@randyburg.com>\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		if(!empty($agent_email)):
			mail($agent_email, $sub, $message, $headers); 
		endif;
        
        $multidocs = array($doc);
        if(isset($_COOKIE['review_queue'])){
            $docs = json_decode($_COOKIE['review_queue'],true);
            if(empty($docs)){
                $docs = array();
            }
            $docs = array_unique(array_merge($docs,$multidocs));
            $docs = json_encode($docs);
            setcookie("review_queue", $docs, time() + 900, "/");
        }else{
            setcookie("review_queue", json_encode($multidocs), time() + 900, "/");
        }
        
        $this->flashMessenger()->addSuccessMessage('Documents reviewing process has started in background, they will be removed from list once process completes');
		exit();
	}
	
	/**
	 *  @brief Brief
	 *  
	 *  @return Return_Description
	 *  
	 *  @details Details
	 *  This function is added to bulk review unverified files
	 */
	public function reviewbulkdocAction()
	{
		$multidocs = $this->getRequest()->getPost('multidocs');
		if(empty($multidocs)){
			//exit();
			die("empty");
		}
		$multidocs = json_decode($multidocs);
        
        // echo "<pre>";print_r($multidocs);echo "</pre>";die;
		$all_docs = array();
		foreach($multidocs as $docs){
			$docdata = explode(',',$docs);
            $data =  count($docdata)-3;
            $cid =  count($docdata)-2;
            $tab =  count($docdata)-1;
            // 
            // 
           
           
            
			$doc =  $docdata[0];
            $all_docs[] = $doc;
			$pid =  $docdata[$data];
			$cid =  $docdata[$cid];
			$tab =  $docdata[$tab];
            
            
            // file_put_contents(__FILE__.'docdata.txt',print_r($docdata,true),FILE_APPEND);
            // file_put_contents(__FILE__.'cid.txt',print_r($cid,true),FILE_APPEND);
            // file_put_contents(__FILE__.'pid.txt',print_r($pid,true),FILE_APPEND);
            // file_put_contents(__FILE__.'tab.txt',print_r($tab,true),FILE_APPEND);
            
            
			
			$u_id =  $this->getUserTable()->getPropUser($pid);
            
            // file_put_contents(__FILE__.'u_id.txt',print_r($u_id,true),FILE_APPEND);
            // echo "<pre>";print_r($docdata);echo "</pre>";die;
			$rev =  $this->getPropertyMetaTable()->revFile($doc,$pid,$cid,$tab);
			$agent_email = $this->get_agent_email($u_id);
            // $agent_email = "sandbox4paytest2@gmail.com";
			$sub = "File Successfully Reviewed";
			$message = "File has been successfully reviewed.Check out by clicking below. <br><br><br><br>";
			$message .= "<a href='http://nrgadmin.com/application/index/propertylisting?p_id=".$pid."&u_id=".$u_id."'>http://nrgadmin.com/application/index/propertylisting?p_id=".$pid."&u_id=".$u_id."</a>";
			
			$headers = "From: NRG Administrator<randy@randyburg.com>\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			if(!empty($agent_email)):
				mail($agent_email, $sub, $message, $headers); 
			endif;
		}
        
        $multidocs = $all_docs;
        if(isset($_COOKIE['review_queue'])){
            $docs = json_decode($_COOKIE['review_queue'],true);
            if(empty($docs)){
                $docs = array();
            }
            $docs = array_unique(array_merge($docs,$multidocs));
            setcookie("review_queue", json_encode($docs), time() + 900, "/");
        }else{
            setcookie("review_queue", json_encode($multidocs), time() + 900, "/");
        }
        
        $this->flashMessenger()->addSuccessMessage('Documents reviewing process has started in background');
		exit();
	}
	
	public function get_agent_email($id)
	{
		$agentEmail = $this->getUserTable()->getAgentEmail($id);
		//var_dump($agentEmail);die;
		return $agentEmail;
	}
	
	public function agentcommisionAction()
	{
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
			$role = $this->get_current_role($id);
				$agent =  $this->getUserTable()->agentCommission($id, $role);
		$this->layout('layout/admin');
		return array('results'=>$agent);
	}
	
	public function agentcommissiondetailsAction() 
	{
		$user_id = $this->getRequest()->getQuery('u_id'); 
		$name = $this->get_user_name($user_id);
		$agent = $this->getPropertyMetaTable()->getAgentCommission($name);
		$this->layout('layout/admin');
		
		foreach($agent as $res)
		{
			$client = $res['client_id'];
			$property_id = $res['property_id'];
			$meta_key = $res['meta_key'];
			if($meta_key == 'first_agent_name')
			{
				$meta_key = 'first_amt';
			}
			if($meta_key == 'second_agent_name')
			{
				$meta_key = 'second_amt';
			}
			if($meta_key == 'third_agent_name')
			{
				$meta_key = 'third_amt';
			}
			$agentdetatil[] = ($this->getPropertyMetaTable()->getAgentProp($client, $property_id, $meta_key));
		
		}
		foreach($agentdetatil as $key=>$val)
		{
			$amount = $val[0];
			$address = $val[1];
			$result[] = array_merge($amount, $address);
		}
	//	print"<pre>";print_r($result);die;
		return array('name'=>$name, 'results'=>$result);
	}
	public function filterpropAction()
	{
		$status = $this->getRequest()->getPost('sts');
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results=($role == 'administrator') ? $this->getUserTable()->fetchallClientbyStatus($status) : $this->getUserTable()->fetchClientByRole($id,$role,$status);
		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		$viewModel = new ViewModel();
		$viewModel->setTerminal(true)
					->setVariables(array(
							'total'=>count($results),
							'results'=> $propertyPaginator,
							'table'=> $this->getPropertyMetaTable(),
							'sts'=> $status,
							'role'=>$role
					));
		return $viewModel;
	}
	public function filterpropaaAction()
	{
		$status = $this->getRequest()->getPost('sts');
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results=($role == 'administrator') ? $this->getUserTable()->fetchallClientbyStatusaa($status) : $this->getUserTable()->fetchClientByRole($id,$role,$status);
		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		$viewModel = new ViewModel();
		$viewModel->setTerminal(true)
					->setVariables(array(
							'total'=>count($results),
							'results'=> $propertyPaginator,
							'table'=> $this->getPropertyMetaTable(),
							'sts'=> $status,
							'role'=>$role
					));
		return $viewModel;
	}
	public function filterPropertyPaginatorAction()
	 {
		$status = $this->getRequest()->getPost('filter_status', 'all');
        $results = $this->getUserTable()->fetchallClientbyStatus($status);
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/filter-property-paginator')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
	 }
 
	 public function filterPropertyPaginatorLayoutAction()
	 {
		$status = $this->getRequest()->getPost('filter_status', 'all');
	    $results = $this->getUserTable()->fetchallClientbyStatus($status);
       
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	   
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/filter-property-paginator-layout')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
	 }
	
	
	
	
	
	
		#######################################################################
		############################ 	Register New User 	 ##################	
		#######################################################################
		
	// public function adduserAction()
	// {
		// $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		
		// return array('result'=>$result);
		
	// }
	public function rolechangeAction()
	{
		$request = $this->getRequest();
		if($request->isPost()){
			$role_name = $request->getPost('role');
			
			if($role_name == 'client')
			{
				$role_name = 'agent';
			} 
			else{
				$role_name = '';
			}
			$user = $this->getUserTable()->fetchByname($role_name);
		}
		
		$view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/getroles')
           ->setVariables(array(
                'users'    => $user, 
				'span_name'=> $role_name,
		    ));
           
        return $view;
	}
	
	public function agentAction()
	{
		$request = $this->getRequest();
		
		if($request->isPost()){
			$role_name = $request->getPost('role');
			if($role_name == 'agent')
			{
				$role_name = 'branchmanager';
			}
			
			else{
				$role_name = $role_name;
			}
			$result= $this->getUserTable()->fetchByname($role_name);
		}
	
		$view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/getagents')
           ->setVariables(array(
                'users'    => $result,  
		    ));
           
        return $view;
	}
     
	public function saveClientAction()
	{
		  $request = $this->getRequest();
		   
		   if ($request->isPost()) 
		   {
			 $data = $this->getUserTable()->getEmail($request->getPost('email'));
			
				 if(!$data)
				 { 	
					$userdata = $request->getPost();
					//var_dump($userdata);die;
					$this->getUserTable()->saveClient($userdata);
					if(($_POST['sendpass'])== '1')
					 {	
						 $this->sendpasswordEmail($_POST['email'], $_POST['password']);
					 }
					 return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'usermanager'));
				 }
				 else{
						$this->flashMessenger()->addErrorMessage('Email id already exists ! Please try another');
						}
				}
		return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'usermanager'));			
	}
	
	     
	public function editpropertyAction()
	{
		$request = $this->getRequest();

		if ($request->isPost()) 
		{
			$data = $request->getPost();
			$data_raw = $request->getPost('data');
			$propId = $request->getPost('prop_edit_id_single');
			
			// print_r($data);
			// die;
			
			$prop_data = array( 
			
				"address"=> $data_raw['address'], 
				"client_name"=> $data_raw['client_name'], 
				"client_email"=> $data_raw['client_email'], 
				"year_build"=> $data_raw['year_build'], 
				"date_submitted"=> $data_raw['date_submitted'], 
			
			
			);
			
			$this->getPropertyTable()->update_prop($propId, $prop_data);
			
			$this->getPropertyMetaTable()->updatePropertymeta_edit($data_raw, $propId );
			
			$this->flashMessenger()->addSuccessMessage('Property Updated !');
			
			return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'allpropertylist'));
			
		}
		
		
		die;
		 
	}
	
	
	
	public function allpropertylistAction()
	{
		//die('ll');
		//ini_set('display_errors',true);
		//$time_start = microtime(true);

		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);

		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$role);

		$managerlist = $this->getUserTable()->fetchallmanager();

		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}

		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		
		// echo "<pre>";
		
		// print_R(count($results));
		
		// echo "</pre>";
		// die;
		
		file_put_contents(__FILE__."propertyPaginator.txt",print_R($propertyPaginator, true), FILE_APPEND);
		
		$this->layout('layout/admin');

		return array(
            'results'    => $propertyPaginator,
			'count' => count($results),
			'records_number' => $perPage,
			'role'=>$role,         
			'managerlist' => $managerlist,           
			'agentList' => $agentList            
        );	
		
	} 

	
		
	
	public function allpropertylist2Action()
	{
		//die('ll');
		//ini_set('display_errors',true);
		//$time_start = microtime(true);

		// if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // return $this->redirect()->toRoute('zfcuser/login');
        // }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		//print_r($role); die;
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$role);
		/* $results1= ($role == 'administrator') ? $this->getUserTable()->fetchallClient1() : $this->getUserTable()->fetchClientByRole($id,$role); */
		$managerlist = $this->getUserTable()->fetchallmanager();
		//print_r($managerlist);die;
		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}

		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		
		
		
		$this->layout('layout/admin');
		

		
		//die('ll');
		return array(
            //'results1'    => $results1,
            'results'    => $propertyPaginator,
			'count' => count($results),
			'records_number' => $perPage,
			'role'=>$role,         
			'managerlist' => $managerlist,           
			'agentList' => $agentList            
        );	
		
	} 

	
	
	public function filteragentAction()
	{	
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$status = $this->getRequest()->getPost('stss');
		
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		
		$results=($role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$role);
		$agentLists = $this->getUserTable()->fetchallAgent($status);
		
		//print_r($agentLists);die;
		$view = new ViewModel();
        $view->setTerminal(true)
           ->setVariables(array(
                'agentLists'    => $agentLists,  
		    ));
           
        return $view;
	}
	
	
	 
    public  function propertyPaginatorAction()
    {
	   
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
        
        $results= ($role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$role);
        
        $managerlist = $this->getUserTable()->fetchallmanager();

		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/property-paginator')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
                'role'=>$role,         
                'managerlist' => $managerlist,           
                'agentList' => $agentList      
		    ));
           
        return $view;
	 }
 
	 public function propertyPaginatorLayoutAction()
	 {
	    if (!$this->zfcUserAuthentication()->hasIdentity()) {
            die;
        }
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
        
        $results= ($role == 'administrator') ? $this->getUserTable()->fetchallClient() : $this->getUserTable()->fetchClientByRole($id,$role);
        // echo "<pre>";print_r($results);die;
        $managerlist = $this->getUserTable()->fetchallmanager();

		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	   
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/property-paginator-layout')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
	 }
	 
    public function unverifiedpropAction()
	{

		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallUnverifiedClient() : $this->getUserTable()->fetchUnverifiedClientByRole($id,$role);

		$managerlist = $this->getUserTable()->fetchallmanager();

		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}
				
		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		$this->layout('layout/admin');

		return array(
            'results'    => $propertyPaginator,
			'count' => count($results),
			'records_number' => $perPage,
			'role'=>$role,         
			'managerlist' => $managerlist,           
			'agentList' => $agentList            
        );	
		
	} 
    
    public  function propertyUnverifiedPaginatorAction()
    {
	   
        $results = $this->getUserTable()->fetchallUnverifiedClient();
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/property-unverified-paginator')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
    }
	 
    public function propertyUnverifiedPaginatorLayoutAction()
    {
	    $results = $this->getUserTable()->fetchallUnverifiedClient();
       
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	   
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/property-unverified-paginator-layout')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
    }
    
    public function verifiedTransactionsAction()
	{
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallVerifiedClient() : $this->getUserTable()->fetchVerifiedClientByRole($id,$role);
		
		$managerlist = $this->getUserTable()->fetchallmanager();

		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}
				
		$page =1;
	    $perPage = 15;
	    $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
	    $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);
		$this->layout('layout/admin');

		return array(
            'results'    => $propertyPaginator,
			'count' => count($results),
			'records_number' => $perPage,
			'role'=>$role,         
			'managerlist' => $managerlist,           
			'agentList' => $agentList            
        );	
		
	} 
    
    public  function verifiedTransactionsPaginatorAction()
    {
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallVerifiedClient() : $this->getUserTable()->fetchVerifiedClientByRole($id,$role);
	   
        // $results = $this->getUserTable()->fetchallUnverifiedClient();
        
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/verified-transactions-paginator')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
    }
	 
    public function verifiedTransactionsPaginatorLayoutAction()
    {
        $id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallVerifiedClient() : $this->getUserTable()->fetchVerifiedClientByRole($id,$role);
	    // $results = $this->getUserTable()->fetchallUnverifiedClient();
       
        $page =  (int)$this->getRequest()->getPost('page', 0);
        $perPage = (int)$this->getRequest()->getPost('per_page', 0);
        
        $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
        $propertyPaginator->setCurrentPageNumber($page);
        $propertyPaginator->setItemCountPerPage($perPage);	
	   
        $view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/verified-transactions-paginator-layout')
           ->setVariables(array(
                'results'    => $propertyPaginator,
			    'count' => count($results),
			    'records_number' => $perPage,
		    ));
           
        return $view;
    }
	
	public function unrcommentsAction()
	{
		//ini_set('display_errors',true);
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$role = $this->get_current_role($id);
		$results= ($role == 'administrator') ? $this->getUserTable()->fetchallUnrcomments() : $this->getUserTable()->fetchUnverifiedClientByRole($id,$role);
		
		$managerlist = $this->getUserTable()->fetchallmanager();
		
		$agentList =array();
		if($role == 'branchmanager') {		
			$agentList = $this->getUserTable()->fetchagents($id);
		}
				
		$page =1;
	    $perPage = 15;
		/*
            $propertyPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($results));
            $propertyPaginator->setCurrentPageNumber($page);
            $propertyPaginator->setItemCountPerPage($perPage); 
        */
	    $propertyPaginator = $results;
	    $propertyPaginator->$page;
		$propertyPaginator->$perPage;
		$this->layout('layout/admin');
		
		return array(
            'results'    => $propertyPaginator,
			'count' => count($results),
			'records_number' => $perPage,
			'role'=>$role,         
			'managerlist' => $managerlist,           
			'agentList' => $agentList            
        );	
		
	} 
	
	 
    
	 
	 
	public function addpropertyAction()
	{
		
		// echo "<pre>";print_r($_POST);die;
		$id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$request = $this->getRequest(); 
		//print_r($_POST()); die;
		if ($request->isPost()) 
		{
			//print"<pre>";print_r($request->getPost());//die;
			$data = $request->getPost('data');
			$add_id = $request->getPost('agent') ;
			$role = $this->get_current_role($id);
			
			if($role == 'administrator' && empty($add_id)){
				$add_id = $request->getPost('manager');
			}else{
				$add_id = ($request->getPost('agent')) ? $request->getPost('agent') : $id;
			}
			//print $add_id;die;
			
			$propId = $this->getPropertyTable()->savePropertyId($add_id);
			foreach($data as $key => $val)
			{
                $arr = array(
                            'client_id'=>$add_id,
                            'property_id'=>$propId ,
                            'meta_key'=>$key,
                            'meta_value'=>$val
                        );

                $this->getPropertyMetaTable()->saveProperty($arr);
			}
            $data = file_get_contents('http://nrgadmin.com/metaMigrate.php');
			$info = new Container('prop_info');
			$info->u_id = $add_id;
			$info->p_id = $propId;

			$this->flashMessenger()->addSuccessMessage('property Added  !');
			return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'allpropertylist'));
			
		}
		return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'allpropertylist'));
	}
	
	
	public function searchpropAction()
	{
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$current_id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$current_role = $this->get_current_role($current_id);
		$SearchForm = new SearchForm();
		$this->layout('layout/admin');
		return array('form'=>$SearchForm, 'current_role'=>$current_role);
	}
   	
	public function deletepropertyAction()
     {
        $property_id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$property_id && !is_int($property_id)) {
            return $this->redirect()->toRoute('administrator', array(
                'action' => 'allpropertylist'
            ));
        }
        
        try {
            $this->getPropertyTable()->deletepropertyDetail($property_id); 
            $this->flashMessenger()->addSuccessMessage('Property Deleted Successfully !');
            return $this->redirect()->toRoute('administrator',array('controller' => 'administrator','action' => 'allpropertylist'));    
        }
        catch (\Exception $ex) {
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'allpropertylist'
            ));
        }
      
     }
	 
	######################## Post Image file uploader ###############
	public function postImageUploaderAction()
	{
			/*******************************************************
			   * Only these origins will be allowed to upload images *
			   ******************************************************/
			  $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://nrgadmin.com");

			  /*********************************************
			   * Change this line to set the upload folder *
			   *********************************************/
			  $imageFolder ='public/img/cmsUpload/'; 

			  reset ($_FILES);
			  $temp = current($_FILES);
			  if (is_uploaded_file($temp['tmp_name'])){
				if (isset($_SERVER['HTTP_ORIGIN'])) {
				  // same-origin requests won't set an origin. If the origin is set, it must be valid.
				  if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
					header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
				  } else {
					// header("HTTP/1.0 403 Origin Denied");
					// return;
					die('error');
				  }
				}

				/*
				  If your script needs to receive cookies, set images_upload_credentials : true in
				  the configuration and enable the following two headers.
				*/
				// header('Access-Control-Allow-Credentials: true');
				// header('P3P: CP="There is no P3P policy."');

				// Sanitize input
				if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
					header("HTTP/1.0 500 Invalid file name.");
					return;
				}

				// Verify extension
				if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return;
				}

				// Accept upload if there was no origin, or if it is an accepted origin
				$filetowrite = $imageFolder . $temp['name'];
				$pathreturn = '/img/cmsUpload/'.$temp['name']; 
				// echo $filetowrite;die;
				move_uploaded_file($temp['tmp_name'], $filetowrite);

				// Respond to the successful upload with JSON.
				// Use a location key to specify the path to the saved image resource.
				// { location : '/your/uploaded/image/file'}
				echo json_encode(array('location' => $pathreturn));
			  } else {
				// Notify editor that the upload failed
				// header("HTTP/1.0 500 Server Error");
				die('error');
			  }
			 die; 
	}
    
    public function uploadArtAction()
	{	
		
		$uploads_dir ='public/art/'; 
		$path = 'art/';
		$sn = 0;
		foreach($_FILES["file"]["tmp_name"] as $upfl):
			$tmp_name = $upfl;
			$name = time()."___".$_FILES["file"]["name"][$sn];
			//$filename = $name;
			$filename = preg_replace("/[^a-zA-Z0-9_.]/", "-", $name);
			
			/* $filter     = new Zend\Filter\Compress(array(
				'adapter' => 'Bz2',
				'options' => array(
					'archive' => $filename.'.bz2',
				),
			));
			$compressed = $filter->filter($filename); */
			
			/* $uploadfilename = new Container('user');
			$uploadfilename->uploadfilename = $compressed; */
			if(move_uploaded_file($tmp_name, $uploads_dir.$filename))
			{
                $insertImage = $this->getArtTable()->insertArt($name,$path.$filename);
				$message['success'][time()][] = $filename; 
			}
			else{
				echo "Failed";
			}
			$sn++;
		endforeach;
		echo json_encode($message);
		exit();
	}
    
    public function uploadArtImagesAction()
    {
        $this->layout('layout/admin');
    }
    
    public function downloadArtImagesAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
		$current_id = $this->zfcUserAuthentication()->getIdentity()->getId();
		$current_role = $this->get_current_role($current_id);
        $artImages = $this->getArtTable()->getArtsImages();
        
        $this->layout('layout/admin');
        return array('artImages'=>$artImages);
    }
    
    public function downloadImageAction()
    {
        $image_id = $this->params()->fromRoute('id');
        $getImage = $this->getArtTable()->getSingleImage($image_id);
        
        $filepath = 'public/'.$getImage['path'];
        //echo $filepath; die;
    // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit;
        }
        else
        {
            echo "unable to downlaod";
            die;
        }
    }
	 
	
###################End########################################################	
    
    /**
      * *****************************************************************************************
      *                         FUNCTION FOR ENTITY OBJECT SECTION
      *******************************************************************************************
      **/
      
    /**
     * Get Role Table object through service manager to perform CURD Operations
     * 
     * @author developed by Trs Software Solutions
     * @return get Instance of RoleTable Model
     **/
    private function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Administrator\Model\RoleTable');
        }

        return $this->roleTable;
    }
    /**
     * Get User Role Table object through service manager to perform CURD Operations
     * 
     * @author developed by Trs Software Solutions
     * @return get Instance of User Role Table Model
     **/
    private function getUserRoleTable()
    {
        if (!$this->userRoleTable) {
            $sm = $this->getServiceLocator();
            $this->userRoleTable = $sm->get('Administrator\Model\UserRoleTable');
        }

        return $this->userRoleTable;
    } 

    
    
	 
	 
	
	public function getPropertyTable()
    {
        if (!$this->propertyTable) 
		{
            $sm = $this->getServiceLocator();
            $this->propertyTable = $sm->get('Application\Model\PropertyTable');
        }

        return $this->propertyTable;
    }
    
    public function getArtTable()
    {
        if (!$this->artTable) 
		{
            $sm = $this->getServiceLocator();
            $this->artTable = $sm->get('Application\Model\ArtTable');
        }

        return $this->artTable;
    }
	
	public function getPropertyMetaTable()
    {
        if (!$this->propertymetaTable) 
		{
            $sm = $this->getServiceLocator();
            $this->propertymetaTable = $sm->get('Application\Model\PropertyMetaTable');
        }

        return $this->propertymetaTable;
    }
     
    /**
	 * GetUserTale method is used for getting the object of user Table from the service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */
    private function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Administrator\Model\userTable');
        }

        return $this->userTable;
    }
	
	public function get_current_role($user_id)
	{
		$role = $this->getUserTable()->get_role($user_id);
		return $role;
	}
    public function get_user_name($user_id)
	{
		$name = $this->getUserTable()->getUsername($user_id);
		return $name;
	}
    
    
    /**
	 * getcategoryTale method is used for getting the object of category Table from the service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */

    private function getTaxonomyTable()
    {
        if (!$this->taxonomyTable) {
            $sm = $this->getServiceLocator();
            $this->taxonomyTable = $sm->get('Administrator\Model\TaxonomyTable');
        }

        return $this->taxonomyTable;
    }
    
    /**
	 * GetadminmailTable method is used for getting the object of adminmail Table from the service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */
    private function getAdminMailTable()
    {
        if (!$this->adminMailTable) {
            $sm = $this->getServiceLocator();
            $this->adminMailTable = $sm->get('Administrator\Model\AdminMailTable');
        }

        return $this->adminMailTable;
    }
     /**
	 * GetFieldsTable method is used for getting the object of fields Table from the service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */
    private function getFieldsTable()
    {
        if (!$this->fieldsTable) {
            $sm = $this->getServiceLocator();
            $this->fieldsTable = $sm->get('Administrator\Model\FieldsTable');
        }

        return $this->fieldsTable;
    }
    
    /**
	 * GetPlanTable method is used for getting the object of Plan Table from the service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */
    private function getPlanTable()
    {
        if (!$this->planTable) {
            $sm = $this->getServiceLocator();
            $this->planTable = $sm->get('Application\Model\PlanTable');
        }

        return $this->planTable;
    }
    
    /**
	 * getTransactionTable method is used for getting the object of Transaction Table from the service manager. 
	 *
	 * @author developed by Trs Software Solutions
	 * @return entity Object
	 */
	private function getTransactionTable()
	{
	  if (!$this->transactionTable) {
            $sm = $this->getServiceLocator();
            $this->transactionTable = $sm->get('Application\Model\TransactionTable');
        }

        return $this->transactionTable;
	
	}
    
    /**
	 * getUserAuthentication method is provide authservice by service manager. 
	 * 
     * @author developed by Trs Software Solutions
	 * @return  entity object
	 * */
    private function getUserAuthentication()
    {
        if (! $this->authService) {
            $this->authService = $this->getServiceLocator()->get('Authentication');
        }
        return $this->authService;
    }
    
     /**
	 * Check request user login or not if login then Grab Login user information 
     *
     *  @author developed by Trs Software Solutions 
     * @return type User info Object/False
	 */
    private function is_userLogin()
    {
        if(!$this->getUserAuthentication()->hasIdentity()):
             $this->flashmessenger()->addErrorMessage('You are not authorized user! login again');
             return $this->redirect()->toRoute('home');
        else:
            $userObj = $this->getUserAuthentication()->getIdentity();
            return $userObj;
       endif;
    }
    
    /**
	 * getPagsTable method is used for getting the object of pages Table from the service manager. 
	 *
	 * @author developed by Trs Software Solutions
	 * @return entity Object
	 */
    private function getPagesTable()
    {
        if (!$this->pagesTable) {
            $sm = $this->getServiceLocator();
            $this->pagesTable = $sm->get('Application\Model\PagesTable');
        }
        return $this->pagesTable;
    }
    
	
	private function getRemindersTable()
    {
        if (!$this->remindersTable) {
            $sm = $this->getServiceLocator();
            $this->remindersTable = $sm->get('Administrator\Model\RemindersTable');
        }
        return $this->remindersTable;
    }
    
    /**
	 * getMenuTable method is used for getting the object of menu Table from the service manager. 
	 *
	 * @author developed by Trs Software Solutions
	 * @return entity Object
	 */
    private function getMenuTable()
    {
        if (!$this->menuTable) {
            $sm = $this->getServiceLocator();
            $this->menuTable = $sm->get('Application\Model\MenuTable');
        }
        return $this->menuTable;
    }
    
    /**
    * getMenuTable method is used for getting the object of menu Table from the service manager. 
    *
    * @author developed by Trs Software Solutions
    * @return entity Object
    */
    private function getAnnouncementTable()
    {
        if (!$this->announcementTable) {
            $sm = $this->getServiceLocator();
            $this->announcementTable = $sm->get('Administrator\Model\AnnouncementTable');
        }
        return $this->announcementTable;
    }
	 
	 private function getContractorTable()
    {
        if (!$this->contractorTable) {
            $sm = $this->getServiceLocator();
            $this->contractorTable = $sm->get('Application\Model\ContractorTable');
        }

        return $this->contractorTable;
    }
	
	
    
     /** 
      * This Function remove salshes 
      * 
      * @input it take String,Object,Array
      * @author developed by Trs Software Solutions
      * @return according to parameter it will return
      */
    public function removeSlashes($input)
    {
        if (is_array($input)) {
            $input = array_map($this->removeSlashes, $input);
        } elseif (is_object($input)) {
            $vars = get_object_vars($input);
            foreach ($vars as $k=>$v) {
                $input->{$k} = $this->removeSlashes($v);
            }
        } else {
            $input = stripslashes($input);
        }
     return $input;
    }
    
    /** 
     * Convert Object Into Array Format
     * @obj Object Parameter
     * @arr Array Parameter
     * @author developed by Trs Software Solutions
     * @return type array
     **/ 
    public function objToArray($obj, &$arr)
    {

        if(!is_object($obj) && !is_array($obj)){
            $arr = $obj;
            return $arr;
        }
        foreach ($obj as $key => $value){
            if (!empty($value)){
                $arr[$key] = array();
                $this->objToArray($value, $arr[$key]);
            }else{
                $arr[$key] = $value;
            }
        }
        return $arr;
    }
    public function getPaginatorRequest($action)
    {
        switch($action):
         
             case 'recieve':
               $resultset = $this->getAdminMailTable()->recieveMail();
               return $resultset;
               break;
             case 'sent':
               $resultset = $this->getAdminMailTable()->sentMail();
               return $resultset;
               break;
             case 'draft':
               $resultset = $this->getAdminMailTable()->draftMail();
               return $resultset;
               break;
       endswitch;
    }

	public function sendpasswordEmail($mail_id, $password)
	{
		$to = $mail_id;
		$subject = 'New User Registre';
		$message .='<div>
						<p>hello user,</p>
						<p>your user name: '.$mail_id.' and</p>
						<p>	your password is: '.$password.'	
					</div>';
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		# Mail Headers
		$headers="From: NRG Administrator <randy@randyburg.com>"; // Who the email is from (example)
		$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";

		# Mail Body
		$email_message .= "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" . $message;
		$email_message .= "\n\n";
		$retval = mail($to,$subject,$email_message,$headers);
	}
	
	public function searchuserAction()
	{
		$request = $this->getRequest();
		
		if($request->isPost()){
			$keyword = $request->getPost('keyword');
			$result= $this->getUserTable()->getUsers($keyword);
		}
		//echo "<pre>"; print_r($result); die;
		$view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/getusers')
           ->setVariables(array(
                'users'    => $result,  
		    ));
           
        return $view;
	}
    
    public function searchuserbyroleAction()
	{
		$request = $this->getRequest();
		
		if($request->isPost()){
			$keyword = $request->getPost('keyword');
			$result= $this->getUserTable()->getUsersByRole($keyword);
		}
		//echo "<pre>"; print_r($result); die;
		$view = new ViewModel();
        $view->setTerminal(true)
           ->setTemplate('partial/getusersbyrole')
           ->setVariables(array(
                'users'    => $result,  
		    ));
           
        return $view;
	} 
	public function contractorphonebookAction()
	{ 
		$this->layout('layout/admin');
		
		 $contractor = $this->getContractorTable()->fetchallContractor();
		// echo "<pre>";print_r($contractor);die;
		return array("contractor"=>$contractor);
	}
	
	public function saveContractorAction()
	{
		  $request = $this->getRequest();
		  
		   if ($request->isPost()) 
		   {
			   
			 $data = $this->getContractorTable()->getEmail($request->getPost('con_email'));
			// echo "<pre>";print_r($data);die;
				 if(!$data)
				 { 	
					$userdata = $request->getPost();
					// var_dump($userdata);die;
				$data = $this->getContractorTable()->insertContractor($userdata);
				
					
					 return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'contractorphonebook'));
				 }
				 else{
						$this->flashMessenger()->addErrorMessage('Email id already exists ! Please try another');
						}
				}
		return $this->redirect()->toRoute('administrator',array('controller'=>'administrator','action'=>'contractorphonebook'));			
	}
	
		public function viewContractorAction()
	 {
		$request = $this->getRequest();
        
		if($request->isPost())
		{
			$user_id = $request->getPost('user_id');
		 // echo "<pre>";print_r($user_id);die;
			$results = $this->getContractorTable()->costractor_profile($user_id);
			
			
			
			$view = new ViewModel();
			$view->setTerminal(true)
			   ->setVariables(array(
					'user_id'         => $user_id,
					'results'         => array_shift($results),
				));
			   
			return $view;
		}
	 }
	 
	  public function checkConEmailAction()
	 {
		$request = $this->getRequest();
        // echo "<pre>";print_r($request->getPost('user_id'));die;
		if($request->isPost())
		{
			$user_id = $request->getPost('user_id');
			$email   = trim($request->getPost('email'));
			
			$check = $this->getContractorTable()->getEmailCheck($user_id,$email);
			if(isset($check['id'])){
				echo "404";
				die;
			}else{
				echo "200";   
				die;
			}			
		}
		die;
	 } 
	
	 public function updateContractorAction()
	 {
		$request = $this->getRequest();
        
		if($request->isPost())
		{
			// print"<pre>";print_r($request->getPost());die;
			
			$user_id = $request->getPost('user_id');
			$data = array(
				'name'      =>   trim($request->getPost('firstname')),
				
				'email'          =>   trim($request->getPost('email')),
				'phone_number'         =>   trim($request->getPost('mobile')),
				'website'           =>   trim($request->getPost('website')),
			);
			
			$this->getContractorTable()->updateContractor($data,$user_id);
			
			$this->flashMessenger()->addSuccessMessage('Contractor Details Update Successfully !');
			return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'contractorphonebook'));
		}
		return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'contractorphonebook'));
	 }
     
	
	
	 public function deletecontractorAction()
     {
       $user_id = (int) $this->params()->fromRoute('id', 0);
		 // echo "<pre>";print_r($user_id);die;
        
         if (!$user_id &&!is_int($user_id)) return $this->redirect()->toRoute('administrator', array('action' => 'contractorphonebook'));
        
        
        try {
            
          //current user login id
          $current_user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
          
          if($current_user_id ==$user_id):
             
             $this->flashMessenger()->addErrorMessage('Current Contractor Profile can not be deleted !');
             return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'contractorphonebook'));
             exit;
          endif;
            
          $user =  $this->getContractorTable()->deleteContractor($user_id);
          
          $this->flashMessenger()->addSuccessMessage('Contractor Profile Deleted Successfully !');
          return $this->redirect()->toRoute('administrator', array('controller' => 'administrator', 'action' => 'contractorphonebook'));
          exit; 
         
         }catch (\Exception $ex){
             return $this->redirect()->toRoute('administrator', array(
                'action' => 'contractorphonebook'
            ));
        }
     }
}