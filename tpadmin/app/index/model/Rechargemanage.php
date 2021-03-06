<?php
namespace app\index\model;
use \think\Model;
use \think\Db;


class Rechargemanage extends Tool{
	

	/*
	*获取所有充值的信息
	*/ 
	public function get_all_user_recharge_info($where,$page){

		if(empty($where)){
			
			$res = Db::name('recharge_log')->order(['order_status'=>'desc','create_time'=>'desc'])->paginate($page);
		
		}else{

			$res = Db::name('recharge_log')->where($where)->order(['order_status'=>'desc','create_time'=>'desc'])->paginate($page);

		}
				
		return $res;
	}

	/*
	*获取所有代理出售给代理的信息
	*/ 
	public function get_all_sell_to_agency_info($where,$page){

		if(empty($where)){
			
			$res = Db::name('agency_sell_to_agency')->order('create_time desc')->paginate($page);
		
		}else{

			$res = Db::name('agency_sell_to_agency')->where($where)->order('create_time desc')->paginate($page);

		}
				
		return $res;
	}

	/*
	*获取所有代理出售给玩家的信息
	*/ 
	public function get_all_sell_to_user_info($where,$page){

		if(empty($where)){
			
			$res = Db::name('sell_log')->order('action_time desc')->paginate($page);
		
		}else{

			$res = Db::name('sell_log')->where($where)->order('action_time desc')->paginate($page);

		}
				
		return $res;
	}

	/*
	*获取所有流水的信息
	*/ 
	public function get_all_recharge_query_info($where,$page){

		if(empty($where)){
			
			$res = Db::name('everyday_total_dimond_log')->paginate($page);
		
		}else{

			$res = Db::name('everyday_total_dimond_log')->where($where)->paginate($page);

		}
				
		return $res;
	}


	public function get_sum($table,$name,$where){
		
		$res =  Db::name($table)->where($where)->sum($name);

		return $res ? $res : 0;
	}



}