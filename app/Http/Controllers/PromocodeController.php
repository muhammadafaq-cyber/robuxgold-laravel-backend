<?php

namespace App\Http\Controllers;

use App\Models\promocode;
use App\Models\User;
use App\Models\user_promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromocodeController extends Controller
{
    public function allpromocodes(){
        $response = [
            'success'=> true,
            'promodes'=> promocode::all()
        ];
        return response($response);
    }
    public function newpromocode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promocode' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 404);
        }

        $promocode = $request->promocode;
        // Check if the promocode already exists in the database
        $existingPromocode = DB::table('promocodes')->where('promocode', $promocode)->first();

        if ($existingPromocode) {
            $response = [
                'success' => false,
                'message' => 'Promocode already exists in the database.'
            ];
            return response()->json($response, 400);
        }

        $data = [
            "promocode" => $promocode,
            "value" => $request->value
        ];

        $msg = DB::table('promocodes')->insert($data);
        if ($msg) {
            $response = [
                'success' => true,
                'message' => "Promocode added successfully!"
            ];
            return response()->json($response,202);
        } else {
            $response = [
                'success' => false,
                'message' => "Error while adding new promocode."
            ];
            return response()->json($response, 500);
        }
    }





    public function deletepromocode($id){
        $msg = DB::table('promocodes')->where('id',$id)->delete();
        if ($msg){
            $response = [
                'success'=> true,
                'message'=> "Promocode Deleted Successfully !"
            ];
            return response($response);
        }
        else{
            $response = [
                'success'=> false,
                'message'=> "Error while Deleting Promocode"
            ];
        }
        return response($response);
    }


    public function updatepromocode(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'promocode' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>'false',
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }
        $data = [
            "promocode" => $request->promocode,
            "value" => $request->value
        ];
        $msg = DB::table('promocodes')->where('id',$id)->update($data);
        if ($msg){
            $response = [
                'success'=> true,
                'message'=> "Promocode Updated Successfully !"
            ];
            return response($response);
        }
        else{
            $response = [
                'success'=> false,
                'message'=> "Error while Updating Promocode"
            ];
        }
        return response($response);
    }



    public function claim_promocode(Request $request){
        $validator = Validator::make($request->all(),[
            'user_id'=>'required',
            'promocode' => 'required'
        ]);
        if ($validator->fails()){
            $response =[
                'success'=>false,
                'message'=>$validator->errors()
            ];
            return response()->json($response,404);
        }

        $promocode = $request->promocode;
        $user_id = $request->user_id;

        $promo = promocode::where('promocode',$promocode)->first();
        if ($promo){
            $check_already =  user_promocode::where('promocode',$promocode)->where('user_id',$user_id)->first();
            if ($check_already){
                $response = [
                    'success' => false,
                    'message'=>'You have already claimed this promocode !'
                ];
                return response()->json($response,404);
            }
            else{
                $status = DB::table('user_promocodes')->insert(['promocode'=>$promo->promocode, 'user_id'=>$user_id]);
                $user = User::where('user_id',$user_id)->first();


                if ($status and $user){
                    $new_blanace = $user->balance+$promo->value;


                    DB::table('users')->where('user_id',$user_id)->update(['balance'=> $new_blanace]);
                    $response = [
                        'success' => true,
                        'message'=>'Promocode successfully Claimed !'
                    ];
                    return response()->json($response,202);
                }
                else{
                    $response = [
                        'success' => false,
                        'message'=>'Something went wrong !'
                    ];
                    return response()->json($response,404);
                }
            }
        }
        else{
            $response = [
                'success' => false,
                'message'=>'Wrong Promocode !'
            ];
            return response()->json($response,404);
        }
    }
}
