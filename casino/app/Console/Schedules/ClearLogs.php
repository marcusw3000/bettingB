<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Task;

class ClearLogs
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $task = Task::where(['finished' => 0, 'category' => 'gelete_stat', 'action' => 'delete' ])->first();
        if($task){
            $task->update(['finished' => 1]);
            \DB::table('stat_game')->where('date_time', '<', now()->subDays(5))->delete();
        }
        $task = Task::where(['finished' => 0, 'category' => 'gelete_log', 'action' => 'delete' ])->first();
        if($task){
            $task->update(['finished' => 1]);
            \DB::table('game_log')->where('time', '<', now()->subDay())->delete();
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('ClearLogs');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}
