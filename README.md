### Khởi tạo ban đầu
1. down source ```https://github.com/laravel/laravel```
2. Cấu hình Database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravelshop
DB_USERNAME=root
DB_PASSWORD=xxxx
```
3. Thay đổi thêm cột table user
php artisan migrate

Thêm level, status vào table user:
```
php artisan make:migration add_level_status_to_users_table --table=users
```
vào folder \database\migrations\xxx_add_level_status_to_users_table.php
```
public function up()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->tinyInteger('level')->after('password')->default(0);
             $table->tinyInteger('status')->after('level')->default(0);
        });
    }
````

### Route
```
Route::prefix('users')->group(function () {
	Route::get('/login', 'App\Http\Controllers\UsersController@getLogin');
});
```
php artisan make:controller UsersController
```
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use Auth;
use App\User;
class UsersController extends Controller
{
    public function getLogin(){
         return view('users.login');
    }
}

```

### Dựng Template jinja
\resources\views\layouts\default.blade.php
```
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>@yield('title') | Got It</title>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
		@include('layouts.navbar')       	
		
		<div class="example">
			<div class="container">
				<div class="row">
				 @yield('content')
				</div>
			</div>				 
		</div>
    </body>
</html>
```
\resources\views\layouts\navbar.blade.php
```
 <div class="example">
	<div id="header">
		<nav class="navbar navbar-inverse">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="" class="navbar-brand">Got It</a>
			</div>

			<div class="navbar-collapse collapse" id="menu">
				<ul class="nav navbar-nav">
					<li><a href="">Trang chủ</a></li>
					<li><a href="">Giới thiệu</a></li>
					<li><a href="">Liên hệ</a></li>
				</ul>
			</div>
		</nav>
	</div>			
</div>
```

\resources\views\users\login.blade.php
```
@extends('layouts.default')

@section('title')
Login page
@endsection

@section('content')
   @if(isset($success))
    <div class="alert alert-success" role="alert">{{ $success }}</div>
    @endif
    @if(isset($fail))
    <div class="alert alert-danger" role="alert">{{ $fail }}</div>
    @endif
	<h2>Login</h2>
		{!! Form::open(array('url' => '/users/login', 'class' => 'form-horizontal')) !!}
		{{ csrf_field() }}
		<div class="form-group">
			 {!! Form::label('name', 'Email', array('class' => 'col-sm-3 control-label')) !!}
			 <div class="col-sm-9">
				{!! Form::text('email', '', array('class' => 'form-control')) !!}
			 </div>
		</div>
		<div class="form-group">	
			{!! Form::label('name', 'Password', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-9">
				{!! Form::input('password', 'password', '', array('class' => 'form-control')) !!}
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-3">
			</div>
			<div class="col-sm-9">
				<div class="checkbox">
					<label><input type="checkbox"> remenber</label>
				</div>
				 <div class="col-sm-offset-2 col-sm-10">
					{!! Form::submit('Login', array('class' => 'btn btn-success')) !!}
				 </div>
			 </div>
		</div>
	{!! Form::close() !!}
@endsection
```

### Khai báo dùng lib Form 
composer require "laravelcollective/html"

config/app.php
```
'aliases' => [
   'Form' => Collective\Html\FormFacade::class,
],
```
### Chức năng login
\routes\web.php
```
	Route::post('/login', 'App\Http\Controllers\UsersController@checkLogin');
```
\app\Http\Controllers\UsersController.php
```
use Validator;
	public function checkLogin(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'email'       => 'required',
			'password'       => 'required'
		]);

		if ($validator->fails()) {
			return redirect('users/login')
					->withErrors($validator)
					->withInput();
		} else {		
			$login = [
				'email' => $request->email,
				'password' => $request->password,
				'level' => 1,
				'status' => 1
			];
			if (Auth::attempt($login)) {
				return redirect('users');
			} else {
				return redirect()->back()->with('status', 'Email hoặc Password không chính xác');
			}
		}
    }
```
### Create Seeding user
```
echo bcrypt("123456");
INSERT INTO  users (`name`, `password`) VALUES ('hoanchuong', '')
```

### User profile page

Đăng ký middlware.

php artisan make:middleware checkUsersLogin
```
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check())
        {
		
            $user = Auth::user();  

            if ($user->status == 1 )
            {
                return $next($request);
            }
            else
            {
                Auth::logout();
                return redirect('users/login');
            }
        } else
		{
            return redirect('users/login');
		}
    }
```

app\Http\Kernel.php
```
   protected $routeMiddleware = [
		'checkuserslogin' => \App\Http\Middleware\checkUsersLogin::class,
    ];
```

\routes\web.php
```
	Route::get('/', 'App\Http\Controllers\UsersController@index')->middleware('checkuserslogin');
```

### Action page rút thăm trúng thưởng
```
php artisan make:controller LuckydrawController
```

Tạo table gifts
```
CREATE TABLE `gifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(3) DEFAULT NULL,
  `weight` int(2) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `gifts` */
insert  into `gifts`(`id`,`name`,`quantity`,`weight`) values 
(1,'Iphone',10,10),
(2,'Một triệu tiền mặt',30,30),
(3,'Vé xem phim',60,60);
```

Tạo table user_gift: 1 record là 1 mã dự thưởng của 1 nhân viên được chuyển từ 10 điểm thưởng thành (tương ứng với cột score_use = 10 cho mục đích tra cứu điểm history), nếu nhân viên có 30 điểm sẽ được 3 record 
```
CREATE TABLE `user_gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `score_use` int(11) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `modifytime` datetime DEFAULT NULL,
  `is_takeluckydraw` int(1) DEFAULT 0,
  `id_gift` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

Tạo table user_shop: Nhân viên thuộc cửa hàng nào
```
CREATE TABLE `user_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

\routes\web.php
```
Route::group(['middleware' => ['checkuserslogin']], function() {
    Route::get('luckdraw/', 'App\Http\Controllers\LuckydrawController@showLuckdraw');	
});
```

\app\Http\Controllers\LuckydrawController.php
```
public function showLuckdraw(){
	 return view('luckdraw.form');
}
```

\resources\views\luckdraw\form.blade.php
```
@extends('layouts.default')

@section('title')
Rút Thăm Trúng Thưởng
@endsection

@section('content')
	@if (count($errors) >0)
		 <ul>
			 @foreach($errors->all() as $error)
				 <li class="text-danger"> {{ $error }}</li>
			 @endforeach
		 </ul>
	 @endif

	 @if (session('status'))
		 <ul>
			 <li class="text-danger"> {{ session('status') }}</li>
		 </ul>
	 @endif
	<h2>Rút Thăm Trúng Thưởng</h2>
		{!! Form::open(array('url' => '/luckdraw/take', 'class' => 'form-horizontal')) !!}
		{{ csrf_field() }}
		<div class="form-group">
			 {!! Form::label('name', 'Nhập mã dự thưởng', array('class' => 'col-sm-3 control-label')) !!}
			 <div class="col-sm-9">
				{!! Form::text('giftcode', '', array('class' => 'form-control')) !!}
			 </div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-3">
			</div>
			<div class="col-sm-9">
				 <div class="col-sm-offset-2 col-sm-10">
					{!! Form::submit('Rút Thăm', array('class' => 'btn btn-success')) !!}
				 </div>
			 </div>
		</div>
	{!! Form::close() !!}
@endsection
```

### Chức năng xử lý bốc thăm trúng thưởng
\routes\web.php
```
    Route::post('luckdraw/take', 'App\Http\Controllers\LuckydrawController@takeLuckdraw');	
```

\app\Http\Controllers\LuckydrawController.php
```
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

	public function takeLuckdraw(Request $request){
	
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
```

### Show popup
\resources\views\users\login.blade.php
```
<div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
			Kết quả bốc thăm
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
			</div>
			<div class="modal-body" id="smallBody">
				<div>				
					<!-- the result to be displayed apply here -->
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".takeluckdraw").click(function(){
		var giftcode = $(".giftcode").val();
		$.ajax({
			type: "POST",
			url: "{{ url('/luckdraw/take') }}",	
			data: { "_token": "{{ csrf_token() }}", giftcode: giftcode},
			success: function(result) {
				$('#smallModal').modal("show");
				$('#smallBody').html(result.msg).show();
			}
		});
    });
});
</script>
```

Cập nhật code update và db và Sửa lại mấy chỗ redirect thành return response()->json
\app\Http\Controllers\LuckydrawController.php
```
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
						
return response()->json
```