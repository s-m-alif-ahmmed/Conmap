<?php

namespace App\Http\Controllers\API\About;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = About::published()->get();
        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Abouts not found", 500);
    }

    public function show($id)
    {
        $data = About::published()->find($id);
        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }
        return $this->error("About not found", 500);
    }
}
