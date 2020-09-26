<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\OrderStatusJob;
use App\Models\Order;
use App\Models\Size;
use App\Traits\UploadImage;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use UploadImage;
    public function store(Request $request)
    {
        $validation_fields  =   [
            'image_url'        => 'required|image|mimes:jpeg,png|max:8000',
            'size_name'        => 'required|max:255',
            'gender'        => 'required|in:male,female',
            'shoulder_to_seam'        => 'required|numeric|max:11',
            'shoulder_to_hips'        => 'required|numeric|max:11',
            'shoulder_to_floor'        => 'required|numeric|max:11',
            'arm_length'        => 'required|numeric|max:11',
            'bicep'        => 'required|numeric|max:11',
            'wrist'        => 'required|numeric|max:11',
            'waist'        => 'required|numeric|max:11',
            'lower_waist'        => 'required|numeric|max:11',
            'waist_to_floor'        => 'required|numeric|max:11',
            'hips'        => 'required|numeric|max:11',
            'max_thigh'        => 'required|numeric|max:11',
            'calf'        => 'required|numeric|max:11',
            'ankle'        => 'required|numeric|max:11',
            'chest'        => 'required|numeric|max:11',
            'navel_to_floor'        => 'required|numeric|max:11',
            'size_id'        => 'required_if:size_type,preset',
            'size_type'        => 'required|in:custom,preset',
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
        $image  =   $this->uploadImage($request->image_url);

        if ($request->size_id){
            $size   =   Size::find($request->size_id);
            $size->update([
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
        }else{
            $size   =   Size::create([
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
        }

        $order  =   Order::create([
             'order_no' =>  Order::CreateRandomBookingID(),
             'user_id' =>  $user->id,
             'tailor_id' =>  0,
             'size_id' =>  $size->id,
             'comments' =>  $request->comments,
             'image_url' =>  $image,
             'order_status' =>  'pending',
        ]);

        /*$this->dispatch(new OrderStatusJob($user,$order));*/
        $data_user['status']  =   true;
        $data_user['messages']  =   'Order Placed Successfully';
        return response()->json($data_user, 200);
    }
}
