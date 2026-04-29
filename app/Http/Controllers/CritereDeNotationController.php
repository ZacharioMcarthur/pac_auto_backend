<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\CritereNotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CritereDeNotationController extends Controller
{
    public function getCritereNotation(){
        try{
            $data = CritereNotation::get();

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    //
}
