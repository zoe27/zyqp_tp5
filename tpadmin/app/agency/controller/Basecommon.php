<?php
namespace app\agency\controller;
use \think\Controller;
use Captcha;
use \think\Session;
use \think\Db;

class Basecommon extends Controller{
	
	/*
	*是否有登陆的判断
	*/ 
	public function is_login(){
		
		$check_agency = Session::has('agency','agency');
		var_dump($check_agency);
    	if(!$check_admin){  	

    		return false;

    	}else{
    		$view = $this->view;
    		$admin_user = Session::get('admin_user','agency');
    		$admin_uid = Session::get('admin_uid','agency');
			$view->admin_user = $admin_user;
			$view->admin_uid = $admin_uid;
			return true;
    	}   	
	}

	/*
	*登陆session过期后的跳转
	*/ 
	protected function jump_login($res){
		if(!$res){
			$this->error('登陆已超时，请重新登陆！','/agency_login.html');
		}	
	}

	/*
	*登陆后当前控制器的赋值，用于左侧菜单栏选中判断
	*/
	public function set_action($view){
       
        $request = \think\Request::instance(); 

        $action = $request->action();

        if($action!='index' && $action!='login'){
        	
        	$view->action = $action;        	
        
        }    
    
    }
   
}