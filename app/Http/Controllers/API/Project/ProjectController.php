<?php

namespace App\Http\Controllers\API\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $new = $request->input('recently_added');
        $search = $request->input('search');
//        $minPrice = $request->input('min_price');
//        $maxPrice = $request->input('max_price');
        $locationRadius = $request->input('location_radius');
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');
        $localAuthority = $request->input('local_authority');
        $clientName = $request->input('client_name');
        $address = $request->input('address');
        $per_page = $request->input('per_page');

        $data = Project::published()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('postal_code', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
            })
            ->when($localAuthority, function ($query) use ($localAuthority) {
                $query->where('local_authority', 'like', '%' . $localAuthority . '%');
            })
            ->when($clientName, function ($query) use ($clientName) {
                $query->where('client_name', 'like', '%' . $clientName . '%');
            })
            ->when($address, function ($query) use ($address) {
                $query->where('address', 'like', '%' . $address . '%');
            })
            ->when($new, function ($query) {
                $query->latest();
            })
            ->when($userLatitude && $userLongitude, function ($query) use ($userLatitude, $userLongitude, $locationRadius) {
                $query->selectRaw(
                    "projects.*,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                    [$userLatitude, $userLongitude, $userLatitude]
                )
                    ->having('distance', '<=', $locationRadius);
            })
//            ->when($minPrice, function ($query) use ($minPrice) {
//                $query->where('price', '>=', $minPrice); // Apply min price filter
//            })
//            ->when($maxPrice, function ($query) use ($maxPrice) {
//                $query->where('price', '<=', $maxPrice); // Apply max price filter
//            })
            ->with('projectType', 'unit', 'duration', 'projectImages', 'projectContacts', 'projectLinks');

        if ($per_page) {
            $data = $data->paginate($per_page);
        } else {
            $data = $data->get();
        }

        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Projects not found", 500);
    }

    public function show($id)
    {
        $data = Project::published()->with('projectType', 'unit', 'duration', 'projectImages', 'projectContacts', 'projectLinks')->find($id);
        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }
        return $this->error("Project not found", 500);
    }
}
