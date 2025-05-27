<?php

uses(Tests\TestCase::class);
// use App\Models\Task;

test('that Task::updatePriorities works correctly', function () {
    $queryTmp = <<<SQL
UPDATE tasks set priority = (CASE 
when id=1 then -1
when id=2 then -2
when id=3 then -3
END) WHERE id IN (1,2,3)
SQL;

    $queryActual = <<<SQL
UPDATE tasks set priority = (CASE 
when id=1 then 1
when id=2 then 2
when id=3 then 3
END) WHERE id IN (1,2,3)
SQL;

    $expected = [
        "status"=> "success",
        "data"=> [
            "queryTmp" => $queryTmp,
            "queryActual" => $queryActual,
        ]
    ];
    $res = App\Models\Task::updatePriorities([1,2,3]);
    expect($res)->toEqual($expected);
});
