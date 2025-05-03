<?php

namespace App\Http\Controllers\API\Service;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = Service::published()->get();
        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Services not found", 500);
    }

    public function show($id)
    {
        $data = Service::published()->find($id);
        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }
        return $this->error("Service not found", 500);
    }
}
