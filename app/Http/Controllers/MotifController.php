<?php

namespace App\Http\Controllers;

use App\Models\Motif;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MotifController extends Controller
{
    public function getMotif(){
        try{
            $data = Motif::all();

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

    public function getMotifById($motifId)
    {
        try {
            $motif = Motif::find($motifId);

            if (!$motif) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'Motif introuvable.',
                    'status' => 404
                ]);
            }

            return response()->json([
                'data' => $motif,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }
}
