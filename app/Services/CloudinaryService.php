<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CloudinaryService
{

    public function uploadImage($filePath, $folder = null)
    {
        $url = "https://api.cloudinary.com/v1_1/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload";
        $postData = [
            'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
        ];
        if ($folder) {
            $postData['folder'] = $folder;
        }
        $response = Http::attach(
            'file',
            file_get_contents($filePath),
            'file'
        )->post($url, $postData);
        $responseData = $response->json();
        if ($response->successful() && isset($responseData['public_id'])) {
            $imageUrl = $responseData['secure_url'] ?? (
                "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload/" . $responseData['public_id']
            );
            return [
                'url' => $imageUrl
            ];
        } else {
            return [
                'error' => 'Upload failed. Please try again.',
                'details' => $responseData
            ];
        }
    }



    // public function deleteImage($publicId)
    // {
    //     $url = "https://api.cloudinary.com/v1_1/" . env('CLOUDINARY_URL') . "/resources/image/upload/" . $publicId;

    //     $response = Http::post($url, [
    //         'api_key' => 'your_api_key',
    //         'api_secret' => 'your_api_secret',
    //     ]);

    //     return $response->json();
    // }
}
