<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $user   =   $request->user();

        $sizes  =   Size::where('user_id',$user->id)->paginate(20);

        if (!empty($sizes)){
            foreach ($sizes as $size)
            {
                $data['sizes'][]  =   [
                    'size_name'        => $size->name,
                    'gender'        => $size->gender,
                    'shoulder_to_seam'        => $size->shoulder_to_seam,
                    'shoulder_to_hips'        => $size->shoulder_to_hips,
                    'shoulder_to_floor'        => $size->shoulder_to_floor,
                    'arm_length'        => $size->arm_length,
                    'bicep'        => $size->bicep,
                    'wrist'        => $size->wrist,
                    'waist'        => $size->waist,
                    'lower_waist'        => $size->lower_waist,
                    'waist_to_floor'        => $size->waist_to_floor,
                    'hips'        => $size->hips,
                    'max_thigh'        => $size->max_thigh,
                    'calf'        => $size->calf,
                    'ankle'        => $size->ankle,
                    'chest'        => $size->chest,
                    'navel_to_floor'        => $size->navel_to_floor,
                ];
            }
            $data['links']['current_page'] = $sizes->currentPage();
            $data['links']['first_page_url'] = $sizes->url($sizes->currentPage());
            $data['links']['from'] = $sizes->firstItem();
            $data['links']['last_page'] = $sizes->lastPage();
            $data['links']['last_page_url'] = $sizes->url($sizes->lastPage());
            $data['links']['next_page_url'] = $sizes->nextPageUrl();
            $data['links']['per_page'] = $sizes->perPage();
            $data['links']['prev_page_url'] = $sizes->previousPageUrl();
            $data['links']['to'] = $sizes->lastItem();
            $data['links']['total'] = $sizes->total();
        }else{
            $data['sizes'] =   [];
            $data['links'] = new \stdClass();
        }
        $data['status']  =   true;
        $data['messages']  =   'Sizes Listing';
        return response()->json($data, 200);
    }
}
