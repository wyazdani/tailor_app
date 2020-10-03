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

    public function store(Request $request)
    {
        $validation_fields  =   [
            'size_name'        => 'required|max:255',
            'gender'        => 'required|in:male,female',
            'shoulder_to_seam'        => 'required|numeric',
            'shoulder_to_hips'        => 'required|numeric',
            'shoulder_to_floor'        => 'required|numeric',
            'arm_length'        => 'required|numeric',
            'bicep'        => 'required|numeric',
            'wrist'        => 'required|numeric',
            'waist'        => 'required|numeric',
            'lower_waist'        => 'required|numeric',
            'waist_to_floor'        => 'required|numeric',
            'hips'        => 'required|numeric',
            'max_thigh'        => 'required|numeric',
            'calf'        => 'required|numeric',
            'ankle'        => 'required|numeric',
            'chest'        => 'required|numeric',
            'navel_to_floor'        => 'required|numeric',
        ];
        $validator     =  $this->getValidationFactory()->make($request->all(),$validation_fields);
        if($validator->fails()) {
            $messages   =   [];
            foreach ($validator->messages()->getMessages() as $key =>   $message){
                $messages[]    =
                    $message[0];
            }
            $messages =   implode(" ",$messages);
            return response()->json([
                'status'     =>  false,
                'messages'   =>  $messages
            ], 200);
        }

        $user   =   $request->user();
        Size::create([
            'user_id'        => $user->id,
            'name'        => $request->size_name,
            'gender'        => $request->gender,
            'shoulder_to_seam'        => $request->shoulder_to_seam,
            'shoulder_to_hips'        => $request->shoulder_to_hips,
            'shoulder_to_floor'        => $request->shoulder_to_floor,
            'arm_length'        => $request->arm_length,
            'bicep'        => $request->bicep,
            'wrist'        => $request->wrist,
            'waist'        => $request->waist,
            'lower_waist'        => $request->lower_waist,
            'waist_to_floor'        => $request->waist_to_floor,
            'hips'        => $request->hips,
            'max_thigh'        => $request->max_thigh,
            'calf'        => $request->calf,
            'ankle'        => $request->ankle,
            'chest'        => $request->chest,
            'navel_to_floor'        => $request->navel_to_floor,
        ]);

        $data_user['status']  =   true;
        $data_user['messages']  =   'Size created Successfully';
        return response()->json($data_user, 200);
    }
}
