<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
// use App\User;
use App\Models\Users;
use App\Models\User;

class ApiContoller extends Controller
{
    public function register(Request $request) {        
        $rules = array (
            'firstName' => 'required|alpha',
            'lastName' => 'required|alpha',
            'userName' => 'required|alpha_dash',            
            'email' => 'required|email',            
            'password' => 'required',
            // 'role' => 'required|alpha|in:Admin,admin,User,user',
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
                // $user->role = ucfirst($request->role);               
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
            'email' => 'required',
            'password' => 'required'
        );        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return $validator->errors();    
        }else{
            $user= User::where('userName', $request->userName)->
                        where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                $loginCheck = Users::where('userName','=', $request->userName)->
                                    Where('email','=', $request->email)->
                                    Where('password','=', md5($request->password))->get();
                if(!empty($loginCheck->toArray())) {
                    $response = [
                        'user' => $loginCheck,
                        'result'=>'Congratulations, Login Successful!!'
                    ];        
                    return response($response, 201);
                }else{ 
                    return response([
                        'message' => 'These credentials do not match our records. Unauthorised login'
                    ], 404);
                }
            }

            $token = $user->createToken('my-app-token')->plainTextToken;
        
            $response = [
                'user' => $user,
                'token' => $token,
                'result'=>'Congratulations, Login Successful!!'
            ];        
            return response($response, 201);
        }
    }
    public function show() {
        return Users::all();
    }
    public function delete($id) {
        $user =  Users::find($id);
        $result =  $user ? $user->delete() : "";
        return !$result  && !$user ? ['result'=>'Record is not found!!'] : 
                 ($result ? ['result'=>'Record is deleted!!'] : 
                            ['result'=>'Operation Failed!!']);
    }
}
