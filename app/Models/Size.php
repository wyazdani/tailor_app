<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable =   ['user_id','name','gender','shoulder_to_seam','shoulder_to_hips',
                             'shoulder_to_floor','arm_length','bicep','wrist','waist',
                             'lower_waist','waist_to_floor','hips','max_thigh','calf',
                             'ankle','chest','navel_to_floor',
        ];
}
