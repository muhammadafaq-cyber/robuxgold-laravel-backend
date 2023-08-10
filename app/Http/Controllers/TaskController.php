<?php

namespace App\Http\Controllers;

use App\Models\offerwall;
use App\Models\refer;
use App\Models\task_detail;
use App\Models\User;
use App\Models\popup_ad;
use App\Models\video_ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function getTodayTasks($user_id) {
        $currentDate = now();
        $user = User::where('user_id', $user_id)->first();

        if ($user) {
            $start_date = $user->start_date;
            $end_date = $user->end_date;
            $currentDay = $currentDate->diffInDays($start_date) + 1; // Calculate the current day

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

                // ... (The rest of your task completion logic)

                $todayTaskList = task_detail::where('user_id', $user_id)->where('day', $currentDay)->get();
                return response()->json(['tasks' => $todayTaskList], 200);
            } else {
                // Handle logic when user has completed all 7 days
                task_detail::where('user_id', $user_id)->update([
                    'status' => 'pending'
                ]);
                User::where('user_id', $user_id)->update(['start_date' => now()]);

                return response()->json(['tasks' => []], 200);
            }
        }

        return response()->json(['tasks' => []], 200);
    }

    public function getTasksDayByDay($user_id) {
        $currentDate = now();
        $user = User::where('user_id', $user_id)->first();

        if ($user) {
            $start_date = $user->start_date;
            $end_date = $user->end_date;
            $currentDay = $currentDate->diffInDays($start_date) + 1; // Calculate the current day

            $taskListByDay = [];

            for ($day = 1; $day <= 7; $day++) {
                $taskList = task_detail::where('user_id', $user_id)->where('day', $day)->get();
                
                $allTasksCompleted = $taskList->every(function ($task) {
                    return $task->status === 'complete';
                });

                $claimedStatus = $allTasksCompleted && !$taskList->isEmpty();

                $taskListByDay[] = [
                    'day' => $day,
                    'claimed' => $claimedStatus,
                    'current_day' => $day === $currentDay,
                    'task_list' => $taskList,
                ];
            }

            return response()->json(['tasks' => $taskListByDay], 200);
        }

        return response()->json(['tasks' => []], 200);
    }


    public function claimTask(Request $request) {
        $user_id = $request->input('user_id');
        $task_id = $request->input('task_id');

        // Assuming you have a model for the task_detail table
        $task = task_detail::where('user_id', $user_id)
            ->where('id', $task_id)
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        if ($task->status === 'pending') {
            // Task is already completed, change status to claimed
            $task->status = 'complete';
            $task->save();
            return response()->json(['message' => 'Task claimed successfully'], 200);
        } elseif ($task->status === 'complete') {
            // Task is already claimed, no need to change status
            return response()->json(['message' => 'Task is already claimed'], 200);
        } else {
            // Task is not completed yet, cannot be claimed
            return response()->json(['message' => 'Task is not yet completed'], 400);
        }
    }


    private function getTaskName($day)
    {
        // Your existing code for getTaskName() goes here...
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
}
