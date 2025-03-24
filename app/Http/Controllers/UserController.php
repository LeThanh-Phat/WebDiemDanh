<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function index(){
        return view('user.users');
    }
    public function index1(){
        $users = (DB::select('select * from user'));
        return $users;
    }
    public function login()
    {
        
    }
}
