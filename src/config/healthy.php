<?php


return [
    "schedule" => [
        "queues" => "rez,we",
        "enabled" => env("IS_SCHEDULE_ENABLED", false),
        "threshold" => 10
    ],
    "job" => [
        "enabled" => env("IS_QUEUE_ENABLED", false),
        "threshold" => 20
    ],
];
