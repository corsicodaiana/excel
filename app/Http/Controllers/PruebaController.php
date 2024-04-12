<?php

namespace App\Http\Controllers;

use App\Imports\PruebaImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class PruebaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $file = $request->file('excel_prueba');

            Excel::import(new PruebaImport, $file);
        } catch (\Throwable $th) {
            $mensaje = '';
            
            if ($th instanceof ValidationException) {
                $mensaje = $th->validator->errors()->all();
            } else {
                $mensaje = $th->getMessage();
            }

            return new JsonResponse([
                'error' => true,
                'mensaje' => $mensaje
            ], 500);
        }
    }
}
