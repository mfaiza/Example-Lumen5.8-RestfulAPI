<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illluminate\Validation\ValidationException;

class UserController extends Controller
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

    public function register(Request $request)
    {
        $rules = [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ];

        $customMessage = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request,$rules,$customMessage);

        try{
            $hasher = app()->make('hash');
            $username = $request->input('username');
            $email = $request->input('email');
            $password = $hasher->make($request->input('password'));

            $save = User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'api token' => ''
            ]);
                $res['status'] = true;
                $res['message'] = 'Registration Success';
                return response($res, 200);
        } catch(\Illuminate\Database\QueryException $ex) {
            $res['status'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function getUser()
    {
        $user = User::all();
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Cannot Find User!',
            ]);
        }
    }
}
