<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Models\Users;

class ApiContoller extends Controller
{
    public function register(Request $request) {        
        $rules = array (
            'firstName' => 'required|alpha',
            'lastName' => 'required|alpha',
            'userName' => 'required|alpha_dash',            
            'email' => 'required|email',            
            'password' => 'required',
            'role' => 'required|alpha|in:Admin,admin,User,user',
            // 'role' => 'required|alpha|in_array:["Admin", "admin", "User", "user"]',
            // 'role' => 'required|alpha|in:' . implode(',', ['Admin', 'admin', 'User', 'user']),
            'phone' => 'required|numeric|digits_between:1,20',
            'dob' => 'date|date_format:Y-m-d'
        );
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
             return $validator->errors();             
        }else { 
            $userNameCheck = Users::where('userName','=', $request->userName)->get();
            $emailCheck = Users::Where('email','=', $request->email)->get();
                                  
            if(!empty($emailCheck->toArray()) && !empty($userNameCheck->toArray())){
                return ['result'=>"You can't use same user Name  and email!!"]; 
            }else if(!empty($userNameCheck->toArray())) {
                return ['result'=>"You can't use same user Name. Enter anothrer user name!"]; 
            }else if(!empty($emailCheck->toArray())) {
                return ['result'=>"You can't use same email. Enter another email!!"]; 
            }else{  
                $user = new Users;                
                $user->firstName = $request->firstName;                
                $user->lastName = $request->lastName;               
                $user->userName = $request->userName;                
                $user->email = $request->email;               
                $user->password = md5($request->password);   
                $user->role = ucfirst($request->role);               
                $user->phone = $request->phone;                
                $user->dob= $request->dob;
                $result = $user->save();
                return $result ? ['result'=>'data saved!!'] : 
                            ['result'=>'Operation Failed!!']; 
            }            
        }
    }
    public function login(Request $request) {
        
        $rules = array (
            'userName' => 'required',
            'password' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
             return $validator->errors();             
        }else { 
            $loginCheck = Users::where('userName','=', $request->userName)->
                                    Where('password','=', md5($request->password))->get();
            return ['result'=>$loginCheck->id ."&nbsp;". $loginCheck->userName . "&nbsp;". $loginCheck->role ]; die;
            if(!empty($loginCheck->toArray())) {
                $request->session()->put([
                    'user_id'=>$loginCheck->id, 
                    'user_name'=>$loginCheck->userName,
                    'user_role'=>$loginCheck->role
                ]);

                $token = $loginCheck->createToken('my-app-token')->plainTextToken;
        
            $response = [
                'user' => $loginCheck,
                'token' => $token,
                'result'=>'Congratulations, Login Successful!!'
            ];        
            return response($response, 201);
            }else{ 
                return response([
                    'message' => 'These credentials do not match our records. Unauthorised login'
                ], 404);
            }            
        }
    }
}
