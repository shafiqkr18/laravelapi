<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Users;

class UsersController extends Controller
{
    public function __construct()
    {
        //  $this->middleware('auth:api');
    }

    public function userlogin(Request $request)
    {

        $this->validate($request, [
            'email'     => 'required|email',
            'password' => 'required'
        ]);
        $user = Users::where('email', $request->input('email'))->first();

        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // return json array.
            return response()->json([
                'status'=>'failure',
                'message' => 'Email does not exist.',
                'code' => 400
            ], 400);
        }
        if(Hash::check($request->input('password'), $user->password)){
            $apikey = base64_encode(str_random(40));
            Users::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;
            $full_name = $user->name;
            $name = explode(' ',$full_name);
            $first_name = $name[0];
            $last_name = $name[1];
            if(!empty($user->photo))
            {
                $profilePic = 'public/images/'.$user->photo;
            }else{
                $profilePic = '';
            }
            $res = array(

                'user' => array(
                    'token' => $apikey,
                    'id' =>$user->id,
                    'firstname'=>$first_name,
                    'lastname' =>$last_name,
                    'email'=>$user->email,
                    'gender'=>$user->sex,
                    'dateOfBirth'=>$user->dob,
                    'streetAddress'=>$user->street,
                    'phonenumber'=>$user->mobile,
                    'profileImage'=>$profilePic,
                    'thumbImage'=>$profilePic,
                    'height'=>$user->height,
                    'weight'=>$user->weight,
                    'role_id'=>$user->role_id,


                )
            );
            return response()->json([
                'status'=>'success',
                'message'=>'user sign in Successfully',
                'code'=>200,
                'result'=>$res

            ], 200);

        }else{
            return response()->json([
                'status'=>'failure',
                'message' => 'Email or password is wrong.',
                'code' => 400
            ], 400);
        }



    }

    /*signup user here
        $param User $user
        return json array
    */
    public  function  usersignup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'failure',
                'message'=>$validator->errors(),
                'code'=>400
            ], 400);
        }

        //check if email exist
        $userExist = Users::where('email', $request->input('email'))->first();

        if($userExist)
        {
            return response()->json([
                'status'=>'failure',
                'message' => 'Email already exist.',
                'code' => 400
            ], 400);
        }

        //save user data
        $Userinput = $request->all();
        $input['name'] = $Userinput['fname'] . ' '. $Userinput['lname'];
        $input['password'] = $this->bcrypt($Userinput['password']);
        $input['email'] = $Userinput['email'];
        $input['role_id']=3;

        //print_r($input);exit;
        $userNew = Users::create($input);

        if($userNew)
        {
            $res = array(
                'user' => array(
                    'id'=>$userNew->id,
                    'firstname'=>$Userinput['fname'],
                    'lastname' =>$Userinput['lname'],
                    'email'=>$userNew->email,



                )
            );
            return response()->json([
                'status'=>'success',
                'message'=>'user signup  Successfully',
                'code'=>200,
                'result'=>$res

            ], 200);
        }


        // Bad Request response
        return response()->json([
            'status'=>'failure',
            'message' => 'Something went wrong.',
            'code' => 400
        ], 400);
    }

    function bcrypt($value, $options = [])
    {

        return app('hash')->make($value, $options);

    }


    public function  userdetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'failure',
                'message'=>$validator->errors(),
                'code'=>400
            ], 400);
        }

        //check if email exist
        $userExist = Users::where('email', $request->input('email'))->first();

        if($userExist)
        {
            return response()->json([
                'status'=>'failure',
                'message' => 'Email already exist.',
                'code' => 400
            ], 400);
        }

        //save user data
        $Userinput = $request->all();
        $input['name'] = $Userinput['fname'] . ' '. $Userinput['lname'];
        $input['password'] = $this->bcrypt($Userinput['password']);
        $input['email'] = $Userinput['email'];
        $input['role_id']=3;

        //print_r($input);exit;
        $userNew = Users::create($input);

        if($userNew)
        {
            $res = array(
                'user' => array(
                    'id'=>$userNew->id,
                    'firstname'=>$Userinput['fname'],
                    'lastname' =>$Userinput['lname'],
                    'email'=>$userNew->email,



                )
            );
            return response()->json([
                'status'=>'success',
                'message'=>'user signup  Successfully',
                'code'=>200,
                'result'=>$res

            ], 200);
        }


        // Bad Request response
        return response()->json([
            'status'=>'failure',
            'message' => 'Something went wrong.',
            'code' => 400
        ], 400);

    }

    public  function  userresetPassword(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required',
            'c_password' => 'required|same:password',
        ]);

        // Find the user by email
        $userExist = Users::where('email', $request->input('email'))->first();

        if (!$userExist) {

            return response()->json([
                'status'=>'failure',
                'message' => 'Email does not exist.',
                'code' => 400
            ], 400);
        }
        //$myUser = App\User::where('email',$userExist->email)->first();

        $userExist->password = $this->bcrypt($request->input('password'));

        $userExist->save();

        return response()->json([
            'status'=>'success',
            'message'=>'Password Change Sucessfully!',
            'code'=>200,
            'result'=>''

        ], 200);
    }

    public  function  userforgetPassword(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email'
        ]);

        // Find the user by email
        $user = Users::where('email', $request->input('email'))->first();

        if (!$user) {

            return response()->json([
                'status'=>'failure',
                'message' => 'Email does not exist.',
                'code' => 400
            ], 400);
        }
        $otp = mt_rand(1000, 9999);

        try {
            $email = $user->email;
            $template_data = ['otp' => $otp, 'name' => $user->name];

            Mail::send(['html' => 'forgetpassword'], $template_data,
                function ($message) use ($email) {
                    $message->to('engr.laravel@gmail.com')
                        ->from('rikskamp@gmail.com')
                        ->subject('Reset Password');
                });

            //Mail::raw('Raw string email', function($msg) { $msg->to(['engr.laravel@gmail.com']); $msg->from(['test@test.com']); });
            //Mail::to('engr.laravel@gmail.com')->send();

            $res = array(
                'user' => array(
                    'id'=>$user->id,
                    'email'=>$user->email,
                    'otp'=>$otp

                )
            );

            return response()->json([
                'status'=>'success',
                'message'=>'email sent successfully',
                'code'=>200,
                'result'=>$res

            ], 200);

        } catch (Exception $ex) {
            return Response::json(['status'=>'failure','code' => 400, 'msg' => 'Something went wrong, please try later.']);
        }


    }
}