<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
class LuckydrawController extends Controller
{
    public function showLuckdraw(){
         return view('luckdraw.form');
    }
	
	public function takeLuckdraw(Request $request){
			// $giftcode = $request->giftcode;
		    // $msg = "This is a simple message. $giftcode";
			// return response()->json(array('msg'=> $msg), 200);	
	
		$validator = Validator::make($request->all(), [
			'giftcode'    => 'required'
		]);

		if ($validator->fails()) {
			return redirect('luckdraw')
					->withErrors($validator)
					->withInput();
		} else {
			$id_user = Auth::id();
			$giftcode = $request->giftcode;
			$user_gifts = DB::table('user_gift')
				->select('modifytime','is_takeluckydraw')
                ->where('gift_code', '=', $giftcode)
                ->where('id_user', '=', $id_user)	
                ->first();
			$is_takeluckydraw = $user_gifts->is_takeluckydraw;
			if($is_takeluckydraw){
				$modifytime = $user_gifts->modifytime;
				return response()->json(array('msg'=> 'Bạn đã sử dụng mã này vào lúc : ' . $modifytime), 200);				
			}else{
				$gift_quantity_total = 0;
				$gift_arr = array();
				
				//Load data quà tặng
				$gifts = DB::table('gifts')
				->select('id', 'name', 'quantity', 'weight')
				->orderBy('weight', 'asc')
                ->get();
				
				foreach($gifts AS $gift){
					$gift_id = $gift->id;
					$gift_name = $gift->name;
					$gift_quantity = $gift->quantity;
					$gift_weight = $gift->weight;
					$gift_arr[$gift_id]['name'] = $gift_name;
					$gift_arr[$gift_id]['quantity'] = $gift_quantity;
					$gift_arr[$gift_id]['weight'] = $gift_weight;
					$gift_quantity_total =  $gift_quantity_total  + $gift_weight;
				}
				
				// Load data quà đã nhận
				$user_gifts = DB::table('user_gift')				
                     ->select(DB::raw('count(id) as quantity, id_gift'))
                     ->where('is_takeluckydraw', '=', 1)
                     ->where('id_gift', '<>', null)
                     ->groupBy('id_gift')
                     ->get();					 
		
				foreach($user_gifts AS $user_gift){
					$id_gift = $user_gift->id_gift;
					$quantity = $user_gift->quantity;
					$gift_arr[$id_gift]['used'] = $quantity;
				}
				
				// Get user shopid
				$user_shop = DB::table('user_shop')
				->select('shop_id')            
                ->where('user_id', '=', $id_user)	
                ->first();
				$shop_id = $user_shop->shop_id;
				
				if($shop_id == 1){
					$rate_plus = 10;
				}else{
					$rate_plus = 0;
				}		
			
				// Quy đổi thành 100%
				$quantity_total_percent  = $gift_quantity_total/100;
				
				// Cho random từ 1->100, nếu shop_id = 1 thì sẽ giảm rand xuống 10% để tăng khả năng trúng giải lên 10%
				$randomnumber  = rand(1, 100) - $rate_plus;
				
 				foreach($gift_arr AS $gift_id => $gift){
					$gift_name = $gift['name'];					
					$gift_quantity = $gift['quantity'];
					$gift_weight = $gift['weight'];
					if(isset($gift['used'])){
						$gift_used = $gift['used'];	
					}else{
						$gift_used = 0;	
					}
					
					$gift_rate = round($gift_weight / $quantity_total_percent,2);
					// Bốc thăm trúng thưởng & Còn quà tặng
					if ($randomnumber <= $gift_rate && $gift_used > 0) {		
						//Update vào db
						$updated = DB::table('user_gift')
							->where('id_user', '=', $id_user)
							->where('gift_code', '=', $giftcode)
							->update([
								'is_takeluckydraw'       => 1,
								'id_gift'      => $gift_id,
								'modifytime'    => \Carbon\Carbon::now()						
								]);
						if($updated){
							return response()->json(array('msg'=> 'Chúc mừng Bạn đã may mắn trúng được ' . $gift_name), 200);
						}else{
							return response()->json(array('msg'=> 'Xin lỗi, phát sinh lỗi trong quá trình xử lý.'), 200);
						}
					}
				}			
					DB::table('user_gift')
					->where('id_user', '=', $id_user)
					->where('gift_code', '=', $giftcode)
					->update([
						'is_takeluckydraw'       => 1,
						'id_gift'      => -1,
						'modifytime'    => \Carbon\Carbon::now()						
						]);
				return response()->json(array('msg'=> 'Chúc Bạn may mắn lần sau.'), 200);
			}
		}
	}
	
	public function takeLuckdraw_(Request $request){
	
		$validator = Validator::make($request->all(), [
			'giftcode'    => 'required'
		]);

		if ($validator->fails()) {
			return redirect('luckdraw')
					->withErrors($validator)
					->withInput();
		} else {
			$id_user = Auth::id();
			$giftcode = $request->giftcode;
			$user_gifts = DB::table('user_gift')
				->select('modifytime','is_takeluckydraw')
                ->where('gift_code', '=', $giftcode)
                ->where('id_user', '=', $id_user)	
                ->first();
			$is_takeluckydraw = $user_gifts->is_takeluckydraw;
			if($is_takeluckydraw){
				$modifytime = $user_gifts->modifytime;		
				return redirect('luckdraw')	
					->withErrors(['msg' => 'Bạn đã sử dụng mã này vào lúc : ' . $modifytime]);				
			}else{
				$gift_quantity_total = 0;
				$gift_arr = array();
				
				//Load data quà tặng
				$gifts = DB::table('gifts')
				->select('id', 'name', 'quantity', 'weight')
				->orderBy('weight', 'asc')
                ->get();
				
				foreach($gifts AS $gift){
					$gift_id = $gift->id;
					$gift_name = $gift->name;
					$gift_quantity = $gift->quantity;
					$gift_weight = $gift->weight;
					$gift_arr[$gift_id]['name'] = $gift_name;
					$gift_arr[$gift_id]['quantity'] = $gift_quantity;
					$gift_arr[$gift_id]['weight'] = $gift_weight;
					$gift_quantity_total =  $gift_quantity_total  + $gift_weight;
				}
				
				// Load data quà đã nhận
				$user_gifts = DB::table('user_gift')				
                     ->select(DB::raw('count(id) as quantity, id_gift'))
                     ->where('is_takeluckydraw', '=', 1)
                     ->groupBy('id_gift')
                     ->get();					 
		
				foreach($user_gifts AS $user_gift){
					$id_gift = $user_gift->id_gift;
					$quantity = $user_gift->quantity;
					$gift_arr[$id_gift]['used'] = $quantity;
				}
				
				// Get user shopid
				$user_shop = DB::table('user_shop')
				->select('shop_id')            
                ->where('user_id', '=', $id_user)	
                ->first();
				$shop_id = $user_shop->shop_id;
				
				if($shop_id == 1){
					$rate_plus = 10;
				}else{
					$rate_plus = 0;
				}		
			
				// Quy đổi thành 100%
				$quantity_total_percent  = $gift_quantity_total/100;
				
				// Cho random từ 1->100, nếu shop_id = 1 thì sẽ giảm rand xuống 10% để tăng khả năng trúng giải lên 10%
				$randomnumber  = rand(1, 100) - $rate_plus;
				
				foreach($gift_arr AS $gift_id => $gift){
					$gift_name = $gift['name'];
					$gift_quantity = $gift['quantity'];
					$gift_weight = $gift['weight'];					
					$gift_used = $gift['used'];	
					
					$gift_rate = round($gift_weight / $quantity_total_percent,2);
					// Bốc thăm trúng thưởng & Còn quà tặng
					if ($randomnumber <= $gift_rate && $gift_used > 0) {
						echo "<br/> rand: ". $randomnumber;
						echo "<br/> gift_rate: ". $gift_rate;
						echo "<br/> gift_name: ". $gift_name;
						return false;
					}
				}

				echo "<pre>";
				// echo $gift_quantity_total;
				// echo "<br/>";
				print_r($gift_arr);
				echo "</pre>";
				
				//TODO: Dùng ajax --> jquery modal show
			}
		}
    }
}
