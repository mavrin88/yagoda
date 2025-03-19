<?php

namespace App\Http\Controllers;

use Dadata\DadataClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DaDataController extends Controller
{
    public function __construct()
    {
        $token = config('dadata.token');
        $secret = config('dadata.secret');

        $this->dadata = new DadataClient($token,$secret);
    }

    public function findById(Request $request)
    {
       return $this->dadata->findById($request->type, $request->number);
    }
}
