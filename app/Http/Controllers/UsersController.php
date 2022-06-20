<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Auth;
use App\User;
use Validator;
class UsersController extends Controller
{
	
	// protected $usersRepository;

    // public function __construct()
    // {
        // $this->usersRepository = $usersRepository;
    // }


    public function getLogin(){
         return view('users.login');
    }
	
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
	
	public function getLogout()
    {
        Auth::logout();
        return redirect('users/login');
    }
	
	public function index(){
		
         return view('users.profile');
    }
}
