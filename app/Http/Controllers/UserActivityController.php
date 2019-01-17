<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use View;
use App\Users;
use Auth;
class UserActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }





    public  function  userupdateProfile(Request $request)
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'email'     => 'required|email',
            //'password'  => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'height' => 'required',
            'weight' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'failure',
                'message'=>$validator->errors(),
                'code'=>400
            ], 400);
        }

        $userExist = Users::where('email', $request->input('email'))
            ->where('id', $request->input('id'))->first();

        if(!$userExist)
        {
            return response()->json([
                'status'=>'failure',
                'message' => 'User Not Found.',
                'code' => 400
            ], 400);
        }


        //check if image exist
        $profilePicNew= '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $profilePic= sha1(date('YmdHis') . str_random(30));
            $profilePic = $profilePic.'.'.$image->getClientOriginalExtension();
            //$destinationPath  = 'C:\Users\Guest User\PhpstormProjects\rikskamp\public\newDesignAssets\users';
            $destinationPath = base_path('public').'/images';
            $image->move($destinationPath, $profilePic);

            $profilePicNew = 'public/images/'.$profilePic;

        }
        //end image

        //save user data
        $Userinput = $request->all();
        $userExist->name = $Userinput['fname'] . ' '. $Userinput['lname'];
        $userExist->sex = $Userinput['gender'];
        $userExist->dob = $Userinput['dob'];
        $userExist->height = $Userinput['height'];
        $userExist->weight = $Userinput['weight'];
        $userExist->photo = $profilePicNew;
        //print_r($input);exit;
        $userExist->save();

        $res = array(
            //'token' => $this->jwt($user),
            'user' => array(
                'id' =>$userExist->id,
                'firstname'=>$Userinput['fname'],
                'lastname' =>$Userinput['lname'],
                'email'=>$Userinput['email'],
                'profileImage'=>$profilePicNew,
                'thumbImage'=>$profilePicNew,
                'gender'=>$userExist->sex,
                'dateOfBirth'=>$userExist->dob,
                'streetAddress'=>$userExist->street,
                'phonenumber'=>$userExist->mobile,
                'height'=>$userExist->height,
                'weight'=>$userExist->weight,
                'role_id'=>$userExist->role_id,



            )
        );
        return response()->json([
            'status'=>'success',
            'message'=>'profile updated  Successfully',
            'code'=>200,
            'result'=>$res

        ], 200);

    }
    function bcrypt($value, $options = [])
    {

        return app('hash')->make($value, $options);

    }
}