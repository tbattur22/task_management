<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Task extends Model
{
    use HasFactory;
    protected $fillable = ['name','priority','project_id','created_at','updated_at'];

    /**
     * Updates the priorities of the tasks according to the order they were passed in.
     * Running the raw sql query to optimize the performance by running single update
     * statement inside transaction. Also using two step update to avoid unique index constraint error
     *
     * @param array $orderedTaskIds
     * @return array{data: array{queryActual: string, queryTmp: string, status: string}|array{data: string, status: string}}
     */
    public static function updatePriorities(array $orderedTaskIds): array
    {
        // 1st step to update all priorities with negative values
        $queryTmp = self::getUpdatePrioritiesQuery($orderedTaskIds, true);
        // 2nd step to update all priorities with actual values
        $queryActual = self::getUpdatePrioritiesQuery($orderedTaskIds, false);

        DB::beginTransaction();
        try {
            DB::select($queryTmp);
            DB::select($queryActual);
            DB::commit();
            return ["status"=> "success","data"=> ['queryTmp'=>$queryTmp, 'queryActual'=>$queryActual]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status"=> "error","data"=> $e->getMessage()]; ;
        }
    }

    /**
     * Helper method to construct the single update query for tasks' priority values.
     * priorities are set according to the task ids parameter
     * @param array $orderedTaskIds
     * @param bool $isTemp indicates if this is temporary 1st step or not
     * @return string the sql update query to run
     */
    private static function getUpdatePrioritiesQuery(array $orderedTaskIds, bool $isTemp = false): string
    {
        // if it is 1st temporary step need to set negative values
        $index = $isTemp ? -1 : 1;
        // build the update query for all task ids
        $res = array_reduce($orderedTaskIds,
        function ($acc, $item) use (&$index, $isTemp) {
            $acc["case"] .= (empty($acc["case"]) ? "\n" : "") . 'when id='.$item.' then '.$index . "\n";
            $acc["in"] .= empty($acc["in"]) ? $item : ',' . $item;
            if ($isTemp) {
                $index--;
            } else {
                $index++;
            }

            return  $acc;
        }, ["case"=>'', "in"=>'']);

        // construct the final single update statement
        $query = "UPDATE tasks set priority = (CASE " . $res['case'] . "END) WHERE id IN (".$res['in'].")";

        return $query;
    }
}
