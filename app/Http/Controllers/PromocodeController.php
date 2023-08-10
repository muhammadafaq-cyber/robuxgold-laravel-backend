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



    public function claim_promocode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'promocode' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $promocode = $request->promocode;
        $user_id = $request->user_id;

        $promo = Promocode::where('promocode', $promocode)->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong Promocode!'
            ], 404);
        }

        $check_already = user_promocode::where('promocode', $promocode)
            ->where('user_id', $user_id)
            ->exists();

        if ($check_already) {
            return response()->json([
                'success' => true,  // Change this to true to indicate success
                'message' => 'You have already claimed this promocode!'
            ], 209); // Custom positive status code
        }

        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $user->balance += $promo->value;
            $user->save();

            UserPromocode::create([
                'promocode' => $promo->promocode,
                'user_id' => $user_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promocode successfully claimed!'
            ], 202);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }


}
