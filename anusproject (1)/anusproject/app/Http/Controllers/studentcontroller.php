<?php

namespace App\Http\Controllers;
use App\Models\lab;
use App\Models\user;
use App\Models\usermodels;
use App\Models\lab_system;
use App\Models\complain__master;
use App\Models\students;

use DB;                                                                                             
use Illuminate\Http\Request;

class studentcontroller extends Controller
{
    public function registerget()
    {
        return view ("register");
    }

   
    public function loginadminpost(Request $req)
    {
        $email =$req->emailinput;
        $password= $req->passwordinput;
        $login = usermodels::where(["email"=>$email , "password"=>$password])->get();

        if(isset($login['0']->id))
        {
            $data=usermodels::where('id',$login['0']->id)->first();
            session()->put('user',$data);
            // session(["sessionid"=>$login->std_id]);
            // session(["sessionusername"=>$login->name]);
            session(["user"=>$data->std_id]);
            echo session('user');
            // echo session()->put('user',$data);      
            // if(session()->get('Login_post')){
                $announcement = DB::table('announcements')->orderBy('id','desc')->limit(1)->get();
                $attendances = DB::table('attendances')->orderBy('id','desc')->limit(1)->get();
                $student_data = DB::table('examsubjectmasters')->where('Std_ID' ,session('user'))->orderBy('id','desc')->limit(1)->get();
                return view('student_dashboard',compact('student_data' ,'announcement','attendances'));
                // else{
                //   return view('login');
                // }
                // return view('student_dashboard',compact('student_data'));
            }
        else
        {
            return redirect()->back()->with("errormessage" , "Record Not Found");

        }

    }
    public function adminget()
    {
        return view("/login");
    }
   
    public function registerpost(Request $res)
    {
        $email =$res->emailinput;
        $studcheck =DB::table("students")->where("student_email", $email)->first();
        // echo "nj".$studcheck;
        $pass =$res->passwordinput;

        if(strlen($pass) < 8)
        {
            echo "<script>alert('Woops! Password cannot be less the 8 characters.')
            window.location.href=''
            </script>";
            return;
            
        }
            
        else
        {
            if(isset($studcheck))
        {

        try{
            
        $user =DB::table("usermodels")->where("email", $email)->first();

            if(isset($user))
            {
                echo "<script>alert('Email Already Exists.')
                window.location.href='/register'
                </script>";
                // return redirect()->back()->with("success" , "Data has been inserted");

            }
            else{

                $pass =$res->passwordinput;
                $conpass =$res->coninput;
 
                if($pass == $conpass)
                {
                    $user = new usermodels();
                    $user->name = $res->nameinput;
                    $user->email = $res->emailinput;
                    $user->password = $res->passwordinput;
                    $user->Std_id = $studcheck->Std_id;

                    $user->save();
                }
                else{
                    // echo "<script>alert('Password Not Matched.')</script>";
                    echo "<script>alert('Password Not Matched.')
                    window.location.href=''
                    </script>";

                }
                
            }
        }
        catch(Exception $ex){


            echo $ex->getMessage();
            die;
        }
            echo "<script>alert('User Registration Completed.')
            window.location.href='/login'
            </script>";

        }

        }
    }

    public function dashboard_(Request $req)
    {
        // $studcheck =DB::table("students")->where("Student_email", $req->Auth::user()->email)->first();
        // echo  $studcheck;
        // return view("student_dashboard");
    }

    public function labs()
    {
        return view("labs");
    }
    public function lab_systems_()
    {
        return view("lab_systems");
    }

    public function lab()
    {
        $lab = new lab();
        $lab->No_of_pcs=$req->No_of_pcsinput;
        $lab->save();
        return redirect()->back();
    }

    public function lab_systems()
    {
        $lab = new lab_system();
        $lab->Host_Name=$req->Host_Nameinput;
        $lab->Status=$req->Statusinput;
        $lab->Lab_id=$req->Host_Nameinput;
        $lab->save();
        return redirect()->back();
    }
    // exam fetch
    public function Fetch_Exam(Request $req)
    {

        $session = session('sessionuseremail');
        echo $session;

        $examfetch = DB::table('modulars')->where('Std_ID' ,session('user'))->orderBy('id','desc')->get();
            return view('examfetch',compact('examfetch')); 

        
    }
    public function announcement()
    {
       $announcement = DB::table('announcements')->orderBy('id','desc')->get();
       return view('announcement',compact('announcement'));
    }
    public function attendances()
    {
       $attendances = DB::table('attendances')->orderBy('id','desc')->get();
       return view('attendances',compact('attendances'));
    }
}
