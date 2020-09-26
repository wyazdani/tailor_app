<?php


namespace App\Traits;


use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait UploadImage
{
    public function uploadImage($file,$path='uploads/media')
    {
        $filenamewithextension = $file->getClientOriginalName();
        $filename   = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension  = $file->getClientOriginalExtension();
        $time_      =   time();
        $full_image = $filename.'_'.$time_.'.'.$extension;
        $full_image   = str_replace(" ","_",$full_image);
        $full_image_path    =   $path;
        $file->move($full_image_path,$full_image);
        $fill   =   $full_image_path."/".$full_image;
        return $fill;
    }
    public function deleteOne($image_path)
    {
        $usersImage_thumbnail = public_path("{$image_path}"); /* get previous image from folder*/
        if (File::exists($usersImage_thumbnail)) { /* unlink or remove previous image from folder*/
            File::delete($usersImage_thumbnail);
        }

    }
}
