<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;

class UploadFile extends Model
{
    public static function saveFile($destination, $image, $temperature)
    {
        if ($image != null) {
            $logoPath = public_path('uploads') . $destination;
            $logoName = $image->getClientOriginalName();
            // $unique_logoName = time() . "-" . $logoName;
            $unique_logoName = $logoName;
            if ($image->move($logoPath, $unique_logoName)) {
                $logo_url = Request::root() . '/public/uploads' . $destination . '/' . $unique_logoName;
            }

            if ($temperature != '') {
                $filename = public_path() . '/uploads' . $destination . '/' . $temperature;
                File::delete($filename);
            }
        } else {
            $unique_logoName = $temperature;
        }

        return $unique_logoName;
    }
    public static function uploadFileDMC($destination, $image, $getFileName = null)
    {
        $logoPath = public_path('uploads') . $destination;
        $fileUrl = $destination . '/' . ($getFileName ?? '');
        $image->move($logoPath, $getFileName);
        return $fileUrl;
    }

    public static function deleteFile($destination, $temperature)
    {
        if ($temperature) {
            $filename = public_path() . '/uploads' . $destination . '/' . $temperature;
            File::delete($filename);
        }
    }

    public static function uploadPhoto($destination, $image, $temperature)
    {
        if ($image) {
            $logoPath = public_path('images') . $destination;
            $logoName = $image->getClientOriginalName();
            $unique_logoName = time() . "-" . $logoName;
            $fileUrl = 'images' . $destination . '/' . $unique_logoName;
            try {
                $image->move($logoPath, $unique_logoName);
            } catch (Exception $error) {
                dd($error);
            }

            if ($temperature != '') {
                $oldFilePath = public_path() . parse_url($temperature, PHP_URL_PATH);
                File::delete($oldFilePath);
            }
        } else {
            $fileUrl = $temperature;
        }

        return $fileUrl;
    }
}
