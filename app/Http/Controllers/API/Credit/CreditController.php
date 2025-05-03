<?php

namespace App\Http\Controllers\API\Credit;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = Credit::published()->get();
        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Credits not found", 500);
    }

    public function show($id)
    {
        $data = Credit::published()->find($id);
        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }
        return $this->error("Credit not found", 500);
    }
}
