<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;

class QueueService
{
    public function push($job)
    {
        return Queue::push($job);
    }

    public function later($delay, $job)
    {
        return Queue::later($delay, $job);
    }

    public function bulk(array $jobs)
    {
        return Queue::bulk($jobs);
    }

    public function size(string $queue = null): int
    {
        return Queue::size($queue);
    }

    public function purge(string $queue): int
    {
        return Queue::purge($queue);
    }

    public function laterOn(string $queue, $delay, $job)
    {
        return Queue::laterOn($queue, $delay, $job);
    }
}
