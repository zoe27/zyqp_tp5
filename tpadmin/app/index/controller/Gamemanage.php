<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Db;
use \think\Request;
use \think\Paginator;
use app\index\model\Gamemanage as gmanage;

class Gamemanage extends Auth{
	/*
	*根据用户权限自动加载左侧菜单栏并判断当前选择的是哪个菜单
	*/ 
	public function _initialize(){

		$this->jump_login($this->is_login());
        
        $view = $this->view; 
        
        $this->set_action($view);

        $gid = Session::get('admin_group_id','admin');

        $this->handle_power($gid,$view);
               
    }

    /*
	*消耗钻石列表
	*/ 
	public function dimond_log(){

		$view = $this->view;
       
        $view->header_title = '消耗钻石';

        $get = input('get.');

        $gamemanage = new gmanage();

        $all_info = array();

        $where = array();

        if(!empty($get)){
        	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['write_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_cost = Db::name("everyday_user_dimond_log")->order('write_time desc')->sum('everyday_total_use');

        $all_info = $gamemanage->get_all_dimond_log_info($where,365);

        $everyday_total_use = 0;

        foreach ($all_info as $k => $v) {
        	$everyday_total_use = $everyday_total_use + $v['everyday_total_use'];
        }

        $page = $all_info->render();	
		
		$view->page = $page;
		
		$view->all_cost = $all_cost;
		
		$view->search_cost = $everyday_total_use;

        $view->all_info = $all_info; 
							      
	    // 模板输出
	    return $this->fetch();
	}

	/*
	*excel导出代理出售给代理列表
	*/ 
	public function dimond_log_down_excel(){

		$get = input('get.');

        $where = array();

        if(!empty($get)){
            	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['write_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_info = Db::name("everyday_user_dimond_log")->field('date_time,everyday_total_use')->where($where)->order('write_time desc')->select();

        $header=['日期','每日钻石消耗'];

        $setWidth = [
        'A'=>'15','B'=>'15'
        ];
        
        self::excel('消耗钻石',$header,$all_info,$setWidth);
        
	}

	/*
	*玩家反馈列表
	*/ 
	public function user_complain(){

		$view = $this->view;
       
        $view->header_title = '玩家反馈';

        $get = input('get.');

        $gamemanage = new gmanage();

        $all_info = array();

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['handler'])){

        		$where['handler'] = $get['handler'];
        	
        	}
        	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }
       
        $all_info = $gamemanage->get_all_user_complain_info($where,100);

        $page = $all_info->render();	
		
		$view->page = $page;
		
        $view->all_info = $all_info; 
							      
	    // 模板输出
	    return $this->fetch();
	}

	/*
	*excel导出玩家反馈列表
	*/ 
	public function user_complain_down_excel(){
		
		$get = input('get.');

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['handler'])){

        		$where['handler'] = $get['handler'];
        	
        	}
      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_info = Db::name("user_complain")->field('uid,contact_way,status,content,call_back,handler,create_time,update_time')->where($where)->order(['create_time'=>'desc'])->select();

        foreach ($all_info as $k => $v) {
        	
        	$all_info[$k]['create_time'] = date('Y-m-d h:i:s',$v['create_time']);   
			
			$all_info[$k]['update_time'] = date('Y-m-d h:i:s',$v['update_time']); 
			
			$all_info[$k]['status'] = $v['status']==1 ? '已回复' : '未回复'; 
			        
        }

        $header=['玩家UID','联系方式','状态','内容','回复','处理人','生成时间','回复时间'];

        $setWidth = [
        'B'=>'20','D'=>'30','E'=>'30','G'=>'20','H'=>'20'
        ];
        
        self::excel('玩家反馈',$header,$all_info,$setWidth);
        
	}

	/*
	*钻石明细列表
	*/ 
	public function dimond_used(){

		$view = $this->view;
       
        $view->header_title = '钻石明细';

        $get = input('get.');

        $gamemanage = new gmanage();

        $all_info = array();

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}
       	        	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }
       
        $all_info = $gamemanage->get_all_dimond_used_info($where,100);

        $all_dimond_used = Db::name("user_dimond_log")->order('use_time desc')->sum('use_dimond');

        $page = $all_info->render();	
		
		$view->page = $page;
		
        $view->all_info = $all_info; 
        
        $view->all_dimond_used = $all_dimond_used; 
							      
	    // 模板输出
	    return $this->fetch();
	}

	/*
	*excel导出钻石消耗列表
	*/ 
	public function dimond_used_down_excel(){
		
		$get = input('get.');

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_info = Db::name("user_dimond_log")->field('uid,use_dimond,use_time')->where($where)->order(['use_time'=>'desc'])->select();

        foreach ($all_info as $k => $v) {
        	
        	$all_info[$k]['use_time'] = date('Y-m-d h:i:s',$v['use_time']);   
			        
        }

        $header=['玩家UID','消耗钻石','消耗时间'];

        $setWidth = [
        'C'=>'20',
        ];
        
        self::excel('钻石明细',$header,$all_info,$setWidth);
        
	}

	/*
	*玩家反馈列表
	*/ 
	public function admin_action_log(){

		$view = $this->view;
       
        $view->header_title = '日志管理';

        $get = input('get.');

        $gamemanage = new gmanage();

        $all_info = array();

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['content'])){

        		$where['content'] = $get['content'];
        	
        	}

        	if(!empty($get['handler'])){

        		$where['handler'] = $get['handler'];
        	
        	}
        	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['action_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }
       
        $all_info = $gamemanage->get_all_ban_log_info($where,100);

        $page = $all_info->render();	
		
		$view->page = $page;
		
        $view->all_info = $all_info; 
							      
	    // 模板输出
	    return $this->fetch();
	}

	/*
	*excel导出日志记录列表
	*/ 
	public function admin_action_log_down_excel(){
		
		$get = input('get.');

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['content'])){

        		$where['content'] = $get['content'];
        	
        	}

        	if(!empty($get['handler'])){

        		$where['handler'] = $get['handler'];
        	
        	}

			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['action_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_info = Db::name("ban_log")->field('uid,content,handler,action_type,action_time')->where($where)->order(['action_time'=>'desc'])->select();

        foreach ($all_info as $k => $v) {
        	
        	$all_info[$k]['action_time'] = date('Y-m-d h:i:s',$v['action_time']);   
        	 			        
        	switch ($v['action_type']) {
	        	case 0:
	        		$all_info[$k]['action_type'] = '默认';
	        		break;
	        	case 1:
	        		$all_info[$k]['action_type'] = '封禁';
	        		break;
	        	case 2:
	        		$all_info[$k]['action_type'] = '解封';
	        		break;		
	        	
	        	default:
	        		$all_info[$k]['action_type'] = '错误';
	        		break;
	        }
        }



        $header=['玩家UID','内容','操作者','类型','操作时间'];

        $setWidth = [
        'B'=>'40','E'=>'20',
        ];
        
        self::excel('日志记录',$header,$all_info,$setWidth);
        
	}

	/*
	*线下赛玩家列表
	*/ 
	public function offline_player_list(){

		$view = $this->view;
       
        $view->header_title = '线下赛';

        $get = input('get.');

        $gamemanage = new gmanage();

        $all_info = array();

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['username'])){

        		$where['username'] = $get['username'];
        	
        	}
              	      				
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }
       
        $all_info = $gamemanage->get_all_offline_player_list_info($where,100);

        $page = $all_info->render();	
		
		$view->page = $page;
		
        $view->all_info = $all_info; 
							      
	    // 模板输出
	    return $this->fetch();
	}

	/*
	*excel导出线下赛记录列表
	*/ 
	public function offline_player_list_down_excel(){
		
		$get = input('get.');

        $where = array();

        if(!empty($get)){

        	if(!empty($get['uid'])){

        		$where['uid'] = $get['uid'];
        	
        	}

        	if(!empty($get['username'])){

        		$where['username'] = $get['username'];
        	
        	}
        	
			if(!empty($get['date_begin']) && !empty($get['date_end'])){

				$where['create_time'] = ['between',[strtotime($get['date_begin']),strtotime($get['date_end'])]];
			
			}
			  		
        }

        $all_info = Db::view('offline_play','uid,create_time')
			    ->view('game_user','username','offline_play.uid=game_user.uid','LEFT')	
			    ->where($where)			
			    ->order(['create_time'=>'desc'])
			    ->select();

        foreach ($all_info as $k => $v) {
        	
        	$all_info[$k]['create_time'] = date('Y-m-d h:i:s',$v['create_time']);   
        	 			                	
        }

        $header=['玩家UID','操作时间','玩家名'];

        $setWidth = [
        'B'=>'20','C'=>'30',
        ];
        
        self::excel('线下赛',$header,$all_info,$setWidth);
        
	}

}