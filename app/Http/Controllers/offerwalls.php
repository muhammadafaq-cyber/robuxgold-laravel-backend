<?php

namespace App\Http\Controllers;

use App\Models\giveaway;
use App\Models\giveaway_winner;
use App\Models\offerwall;
use App\Models\popup_ad;
use App\Models\refer;
use App\Models\task;
use App\Models\task_detail;
use App\Models\User;
use App\Models\video_ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class offerwalls extends Controller
{
  //    Adgatemedia
  // ******************************************************
  public function adgatemediapostback(Request $request){
        $user_id = $request->input('user_id');
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $id = $user->id;
        }
        else{
            $id = 1;
        }


        $values = [
            'offerwall_name'=>"Adgate Media",
            'user'=>$id,
            'user_id'=>$request->input('user_id'),
            'conversion_id'=>$request->input('conversion_id'),
            'point_value'=>$request->input('point_value'),
            'usd_value'=>$request->input('usd_value'),
            'offer_title'=>$request->input('offer_title')
        ];

        $status = offerwall::updateorCreate($values);
        if ($status){
            $response = [
                'status'=>'OK'
            ];
            return response()->json($response,200);
        }
        else{
            $response = [
                'status'=>'Something Wrong'
            ];
        }
        return response()->json($response,400);
    }
    public function offertoro(Request $request){
        $offer_conversion_id = $request->input('oid');
        $user_id = $request->input('user_id');
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $id = $user->id;
        }

        $values = [
            'offerwall_name'=>"OfferToro",
            'user'=>$id,
            'user_id'=>$request->input('user_id'),
            'conversion_id'=>$offer_conversion_id,
            'point_value'=>$request->input('amount'),
            'usd_value'=>$request->input('payout'),
            'offer_title'=>$request->input('o_name')
        ];

        $status = offerwall::updateorCreate($values);
        if ($status){
            return 1;
        }
        else{
            return 0;
        }

    }
    public function ayetstudio(Request $request){
        $offer_conversion_id = $request->input('transaction_id');
        $user_id = $request->input('user_id');
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $id = $user->id;
        }

        $values = [
            'offerwall_name'=>"eyeTStudio",
            'user'=>$id,
            'user_id'=>$request->input('user_id'),
            'conversion_id'=>$offer_conversion_id,
            'point_value'=>$request->input('amount'),
//            'usd_value'=>$request->input('payout'),
            'offer_title'=>$request->input('offer_name')
        ];

        $status = offerwall::updateorCreate($values);
        if ($status){
            $response = [
                'status'=>'OK'
            ];
            return response()->json($response,200);
        }
        else{
            $response = [
                'status'=>'Something Wrong'
            ];
            return response()->json($response,404);
        }

    }

    public function lootably(Request $request){
        $offer_conversion_id = $request->input('transactionID');
        $user_id = $request->input('userID');
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $id = $user->id;
        }
        else{
            return "0";
        }

        $values = [
            'offerwall_name'=>"Lootably",
            'user'=>$id,
            'user_id'=>$user_id,
            'conversion_id'=>$offer_conversion_id,
            'point_value'=>$request->input('amount'),
            'usd_value'=>$request->input('usd_value'),
            'offer_title'=>$request->input('offerName')
        ];

        $status = offerwall::updateorCreate($values);
        if ($status){
            return "1";
        }
        else{
            return "0";
        }

    }
    public function cpx_research(Request $request){
        $offer_conversion_id = $request->input('transaction_id');
        $user_id = $request->input('user_id');
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $id = $user->id;
        }
        else{
            return "0";
        }

        $values = [
            'offerwall_name'=>"cpx_research",
            'user'=>$id,
            'user_id'=>$user_id,
            'conversion_id'=>$offer_conversion_id,
            'point_value'=>$request->input('amount'),
            'usd_value'=>$request->input('usd_value'),
//            'offer_title'=>$request->input('offerName')
        ];

        $status = offerwall::updateorCreate($values);
        if ($status){
            return "1";
        }
        else{
            return "0";
        }

    }




    //    AdGem
    // ******************************************************
    public function adgem(Request $request){
//        $values = [
//            'user_id'=>$request->input('user_id'),
//            'conversion_id'=>$request->input('conversion_id'),
//            'point_value'=>$request->input('point_value'),
//            'usd_value'=>$request->input('usd_value'),
//            'offer_title'=>$request->input('offer_title')
//        ];

//        $status = offerwall::updateorCreate($values);
//        if ($status){
        $response = [
            'status'=>'OK'
        ];
        return response()->json($response,200);
//        }
//        else{
//            $response = [
//                'status'=>'Something Wrong'
//            ];
//        }
//        return response()->json($response,400);

    }



  public function offerwalls_counts($user_id)
  {
    $currentDate = now();
    $user = User::where('user_id', $user_id)->first();

    if ($user) {
      $start_date = $user->start_date;
      $end_date = $user->end_date;
      $currentDay = $currentDate->diffInDays($start_date) + 1; // Calculate the current day

      // return response()->json($currentDay);
      if ($currentDay <= 7) {
        // Check if any previous task is pending or incomplete
        $previousTasksPending = task_detail::where('user_id', $user_id)
          ->where('day', '<', $currentDay)
          ->whereIn('status', ['pending', 'incomplete'])
          ->count();

        if ($previousTasksPending > 0) {
          // Reset all tasks to "pending" and start from the 1st day
          task_detail::where('user_id', $user_id)->update([
            'status' => 'pending'
          ]);

          $currentDay = 1; // Reset the current day to 1
        }

        // Create previous days' records if they don't exist
        for ($day = 1; $day < $currentDay; $day++) {
          $task = task_detail::where('user_id', $user_id)->where('day', $day)->first();
          if (!$task) {
            $task_name = $this->getTaskName($day);
            task_detail::create([
              'user_id' => $user_id,
              'day' => $day,
              'task_name' => $task_name,
            ]);
          }
        }
        $task = task_detail::where('user_id', $user_id)->where('day', $currentDay)->first();
        if (!$task) {
          $task_name = $this->getTaskName($currentDay);
          task_detail::create([
            'user_id' => $user_id,
            'day' => $currentDay,
            'task_name' => $task_name,
          ]);
          $task = task_detail::where('user_id', $user_id)->where('day', $currentDay)->first();
        }
        // Check if all previous tasks are completed
        $previousTasksCompleted = task_detail::where('user_id', $user_id)
          ->where('day', '<', $currentDay)
          ->where('status', 'complete')
          ->count();
        if ($previousTasksCompleted == ($currentDay - 1)) {
          if ($currentDay == 1 or $currentDay == 2 or $currentDay == 3 or $currentDay == 4) {
            $completedOfferwalls = Offerwall::where('user_id', $user_id)
              ->whereDate('created_at', $currentDate)
              ->count();
            [$requiredOfferwalls, $coins] = $this->getOfferwallRequirements($currentDay);
            if ($completedOfferwalls >= $requiredOfferwalls) {
              $task->status = 'complete';
              $task->robux = $coins;
              $task->save();
            }
          }
          if ($currentDay == 5) {
            $completedRefers = refer::where('user_id', $user_id)
              ->whereDate('created_at', $currentDate)
              ->count();
            [$requiredRefers, $coins] = $this->getOfferwallRequirements($currentDay);
            if ($completedRefers >= $requiredRefers) {
              $task->status = 'complete';
              $task->robux = $coins;
              $task->save();
            }
          }
          if ($currentDay == 6) {
            $completedPopup_ads = popup_ad::where('user_id', $user_id)
              ->whereDate('created_at', $currentDate)
              ->count();
            [$requiredPopup_ad, $coins] = $this->getOfferwallRequirements($currentDay);
            if ($completedPopup_ads >= $requiredPopup_ad) {
              $task->status = 'complete';
              $task->robux = $coins;
              $task->save();
            }
          }
          if ($currentDay == 7) {
            $completedVideo_ads = video_ad::where('user_id', $user_id)
              ->whereDate('created_at', $currentDate)
              ->count();
            [$requiredVideo_ad, $coins] = $this->getOfferwallRequirements($currentDay);
            if ($completedVideo_ads >= $requiredVideo_ad) {
              $task->status = 'complete';
              $task->robux = $coins;
              $task->save();
            }
          }
        } else {
          // Previous tasks are not completed, set current task to "pending"
          $task->status = 'pending';
          $task->save();
        }
      } else {
        // Handle logic when user has completed all 7 days
        task_detail::where('user_id', $user_id)->update([
          'status' => 'pending'
        ]);
        User::where('user_id', $user_id)->update(['start_date' => now()]);
      }
    }

    $tasks = task_detail::where('user_id', $user_id)->get()->values();
    // return $tasks;
    // return response()->json($tasks, 200);
    return response()->json(['tasks' => $tasks], 200);
  }

  private function getTaskName($day)
  {
    if ($day <= 4) {
      return 'offerwall';
    } elseif ($day === 5) {
      return 'refer';
    } elseif ($day === 6) {
      return 'video_ad';
    } elseif ($day === 7) {
      return 'popup_ad';
    }
  }

  private function getOfferwallRequirements($day)
  {
    if ($day === 1) {
      return [1, 5];
    } elseif ($day === 2) {
      return [5, 25];
    } elseif ($day === 3) {
      return [20, 100];
    } elseif ($day === 4) {
      return [100, 500];
    } elseif ($day === 5) {
      return [10, 100];
    } elseif ($day === 6) {
      return [1, 0.5];
    } elseif ($day === 7) {
      return [1, 1];
    }

    return [100000, 0];
  }

  public function popup_ad($user_id)
  {
    if ($user_id) {
      $data = [
        'user_id' => $user_id
      ];
      DB::table('popup_ads')->insert($data);
    }
  }
  public function video_ad($user_id)
  {
    if ($user_id) {
      $data = [
        'user_id' => $user_id
      ];
      DB::table('video_ads')->insert($data);
    }
  }

  public function new_giveaway_entry($user_id)
  {
    if ($user_id) {
      $checkuser = giveaway::where('user_id', $user_id)->first();
      if (!$checkuser) {
        $new_entry = new giveaway();
        $new_entry->user_id = $user_id;
        $new_entry->save();
        $response = [
          'success' => true,
          'message' => "You have been entered in giveaway successfully!"
        ];
        return response()->json($response, 202);
      }
    } else {
      $response = [
        'success' => false,
        'message' => "Something wrong entered!!!"
      ];
      return response()->json($response, 404);
    }
    $response = [
      'success' => false,
      'message' => "You already have been added in this giveaway!!!"
    ];
    return response()->json($response, 404);
  }

  public function check_giveaway_entry($user_id)
  {
    if ($user_id) {
      $checkuser = giveaway::where('user_id', $user_id)->first();
      if (!$checkuser) {
        $response = [
          'success' => true,
          'message' => "New Member !",
          'member_count' => giveaway::all()->count()
        ];
        return response()->json($response, 202);
      } else {
        $response = [
          'success' => false,
          'message' => "Already Exists",
          'member_count' => giveaway::all()->count()
        ];
        return response()->json($response, 404);
      }
    }
    $response = [
      'success' => false,
      'message' => "User ID is missing!"
    ];
    return response()->json($response, 404);
  }

  public function draw_giveaway()
  {
    $selected = giveaway::inRandomOrder()->first();

    if ($selected) {
      $winner = new giveaway_winner();
      $winner->user_id = $selected->user_id;
      $winner->prize = $selected->prize;
      $winner->save();

      $user = User::where('user_id', $selected->user_id)->first();
      $balance = $user->balance;
      $balance = $balance + $selected->prize;
      User::where('user_id', $selected->user_id)->update(['balance' => $balance]);

      giveaway::truncate();
      return response()->json($winner, 202);
    } else {
      $response = [
        'success' => false,
        'message' => 'No Participent'
      ];
      return response()->json($response, 404);
    }
  }

  //     public function leaderboard(){
  //         $topThreeUsers = Offerwall::select('user', DB::raw('count(*) as total_offers'))
  //             ->groupBy('user')
  //             ->orderByDesc('total_offers')
  //             ->limit(12)
  //             ->with('user')
  //             ->get();
  // //        $topThreeUsers = offerwall::with('user')->get();
  //         return response()->json(['leaderboard'=>$topThreeUsers]);
  //     }

  public function leaderboard()
  {
    $topThreeUsers = Offerwall::select('user', DB::raw('count(*) as total_offers'))
      ->groupBy('user')
      ->orderByDesc('total_offers')
      ->limit(12)
      ->get();

    $leaderboards = [];
    foreach ($topThreeUsers as $leaderboard) {
      $user = DB::table('users')->where('id', $leaderboard->user)->first();
      $leaderboards[] = [
        'id' => $user->id,
        'name' => $user->name,
        'user_id' => $user->user_id,
        'balance' => $user->balance,
        'start_date' => $user->start_date,
        'end_date' => $user->end_date,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'total_offers' => $leaderboard->total_offers,
      ];
    }

    return response()->json(['leaderboard' => $leaderboards]);
  }

  public function giveaway_winners()
  {
    $topEightWinners = giveaway_winner::recentLastWeek()->get();
    $winners = [];
    foreach ($topEightWinners as $result) {
      $user = DB::table('users')->where('user_id', $result->user_id)->first();
      $winners[] = [
        'user_id' => $user->user_id,
        'user_name' => $user->name,
        'avatar' => $user->profile_pic,
        'winning_date' => $result->created_at->format('Y-m-d'),
      ];
    }
    return response()->json(['winners' => $winners], 202);
  }

  public function add_demo()
  {
    echo "DEMO";
  }
}
