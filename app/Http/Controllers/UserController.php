<?php

namespace App\Http\Controllers;
use App\Models\task_detail;
use App\Models\User;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>'false',
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }
        $input = $request->all();
        $input['password']= Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name']=$user->name;
        $response = [
            'success'=>true,
            'data'=>$success,
            'message'=>"User Register Successfully!"
        ];
        return response()->json($response,202);
    }


    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>'false',
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user['token'] = $user->createToken('MyApp')->plainTextToken;
            $response = [
                'success'=>true,
                'data'=>$user,
                'message'=>"User Login Successfully!"
            ];
            return response()->json($response);
        } else {
            $response = [
                'success'=>'false',
                'message'=>'Unauthorized'
            ];
            return response()->json($response,202);
        }
    }



    public function getUserId(Request $request){
        $url = 'https://users.roblox.com/v1/usernames/users';
        $requestBody = [
            'usernames' => [$request->username],
            'excludeBannedUsers' => true
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $requestBody);
            $userData = $response->json();
            if (count($userData['data']) > 0) {
                $userId = $userData['data'][0]['id'];
                if ($userId){
                    $url = "https://thumbnails.roblox.com/v1/users/avatar-headshot?userIds=".$userId."&size=420x420&format=Png&isCircular=false";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = [
                        'user_ID'=> $userId,
                         'response'=> curl_exec($ch)
                    ];
                    return response($response);
                }
            } else {
                return response()->json("User Not Found",401);
            }
        } catch (RequestException $e) {
            throw new \Exception("Error fetching user ID: " . $e->getMessage());
        }
    }





    public function makePayout()
    {
        $groupId = 2782840;
        $recipientId = 4688965561;
        $token = 'tokengoeshere';
        $cookie = '_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_1236B4BEEA9AAE1960AD8CFC1A04792F44CB30F8F7D5ADC72B34ADD9FF961810C54C72E319828EF100CD3C4A268D2D06F6856D0B2E5CFD398FCCD05CEE50DAB8FD371D614D4E741A2672F6E7342A751EEDF4B8FF19C2CC09AEC3DB06D403A240C53D0A7F1D3B5CBF65891E13BB00177D6AA756E54BCEAAFB69CD1F4D684A41E08FBCC4173ABD524BE4569B83E0A5C8C54471BD802A928F525AB67FB515CEE8152F9A1465A8FC651CF1C2AFF66A8D7ED3F8B6AB4BD8A5AAF6CA3E6963AA38E0AEA632DC03D149B89157AE72591B30CB89121D4B1BC169BFBC08A24E170FA4BF4711D271B17FB2AAB72D5D2142E0175F489D3E39414FF518793981437B544502EDE76E20772778913463339BD8026A8F4566ABE2A8224B5FB3B05AD171C8FA8F752F43A0C995784B1C7E62A81540BCF26D85CCB3144FF23A585243386CE813D0554FE27678369350F87C37E98498E927E296712982BE3B408F94871D3A73B40975C1C304F679EF0720B1E5A09FC9260AF5D685A525B5E8F1D8A2519EAA3C35C0FD4935F32ED9963BA6552F90506D2B421CB909D9F0DA8D21ECB098ED5536BB9CE4F6EDB05B73114CF2A28A2C196D57985805B4007565FC94533E5D8BC025ABA5F8DAAB808887516712F36E9AD716A4927306C99956BADDC8576D437BF71558E0D218C9B735E8BE7ACAEDC9BCEC0FD5B209369A2C0C152DFE0BFCE42B9243110CF7580F86825B351669699C5D0C7E9B38DCE62DED980AC7054E6DAD961E2D3532354FF85E9EAD8E4A66EC460F3BD558E535A587DAA463121767E264A217D143F18CCC70BF843B5436CE21B59152DB0FCF31D5B93862B242A648EB65D128A9D312D87AC9EE7C277E9312455404535BF2FBE1C066146B298DA040DD0B46B4DBFA4A81D2BC56FA208B74AC05FBE1B1D6CB4C2BA87029AA447EFC3326F24C72AF50BD3A4DE27EEE';

        try {
            $response = Http::withHeaders([
                'Cookie' => ".ROBLOSECURITY={$cookie}",
                'Content-Type' => 'application/json',
            ])->post("https://groups.roblox.com/v1/groups/{$groupId}/payouts", [
                'PayoutType' => 'FixedAmount',
                'Recipients' => [
                    [
                        'recipientId' => $recipientId,
                        'recipientType' => 'User',
                        'amount' => 1
                    ]
                ]
            ]);
            return response()->json($response->status()); // Return the HTTP status code
        } catch (RequestException $e) {
            throw new \Exception("Error making payout: " . $e->getMessage());
        }
    }




    public function create_task_detail(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>'false',
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }
        $currentDate = now();
        $user_id = $request->user_id;
        $data = [
            "username" => $request->username,
            "user_id" => $request->user_id,
            "start_date" => $currentDate->format('Y-m-d'),
            "end_date" => $currentDate->copy()->addDays(7)->format('Y-m-d'),
            'created_at'=>$currentDate,
            'updated_at'=>$currentDate
        ];
        $check = User::where('user_id',$user_id)->first();
        if (!$check){
            DB::table('users')->insert($data);
        }


        for ($i = 0; $i < 7; $i++) {
            $task = new task_detail();
            $task->user_id = $request->user_id;
            $task->day = $i + 1;
            $task->date_created = $currentDate->format('Y-m-d');
            $task->save();
            $currentDate = $currentDate->addDay();
        }
    }




    public function new_user(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'user_id' => 'required',
            'profile_pic'=>'required',
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>'false',
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }
        $user = User::where('user_id',$request->user_id)->first();
        if (!$user){
            DB::table('users')->insert(['name'=>$request->username,'user_id'=>$request->user_id,'profile_pic'=>$request->profile_pic]);
            return response()->json("New User Registered in Robux Gold !!!",202);
        }
        else{
            return response()->json('User Already Exists!!!');
        }
    }




}
