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

    public static function updatePriorities(array $orderedTaskIds): array
    {
        // $index = 1;
        // $res = array_reduce($orderedTaskIds,
        // function ($acc, $item) use (&$index) {
        //     $acc["case"] .= (empty($acc["case"]) ? "\n" : "") . 'when id='.$item.' then '.$index . "\n";
        //     $acc["in"] .= empty($acc["in"]) ? $item : ',' . $item;
        //     $index++;
        //     return  $acc;
        // }, ["case"=>'', "in"=>'']);

        // $query = "UPDATE tasks set priority = (CASE " . $res['case'] . "END) WHERE id IN (".$res['in'].")";

        // Log::info("running raw db query: {$query}");
        $queryTmp = self::getUpdatePrioritiesQuery($orderedTaskIds, true);
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

    private static function getUpdatePrioritiesQuery(array $orderedTaskIds, bool $isTemp = false): string
    {
        $index = $isTemp ? -1 : 1;
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

        $query = "UPDATE tasks set priority = (CASE " . $res['case'] . "END) WHERE id IN (".$res['in'].")";

        Log::info("running raw db query: {$query}");

        return $query;
    }
}
