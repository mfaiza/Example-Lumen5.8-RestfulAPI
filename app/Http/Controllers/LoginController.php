<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $customMessage = [
            'required' => ':attribute tidak boleh kosong'
        ];
        $this->validate($request,$rules, $customMessage);
        $email = $request->input('email');
        try
        {
            $login = User::where('email', $email)->first();
            if ($login){
                if($login->count() > 0){
                    if(Hash::check($request->input('password'), $login->password)) {
                        try{
                            $api_token = sha1($login->id_user.time());
                            $create_token = User::where('id', $login->id_user)->update(['api_token' => $api_token]);
                            return response()->json([
                                'success' => true,
                                'message' => 'Success login !',
                                'data' => $login,
                                'api_token' => $api_token,
                            ], 200);
                        } catch (Illuminate\Database\QueryException $ex) {
                            return response()->json([
                                'success' => true,
                                'message' => $ex->getMessage(),
                            ], 500);
                        }
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Username or Email Not Found',
                        ], 404);
                    }
                }else{
                    return response()->json([
                        'success' => true,
                        'message' => 'Username or Email Not Found',
                    ], 404);
                }
            }else{
                return response()->json([
                    'success' => true,
                    'message' => 'Username or Email Not Found',
                ], 404);
            }
        } catch (Illuminate\Database\QueryException $ex){
            return response()->json([
                'success' => true,
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

}
