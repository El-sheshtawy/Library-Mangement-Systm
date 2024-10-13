<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandleFile
{
    private $domainName = 'http://127.0.0.1:8000';

    public function uploadFile(Request $request, $inputName, $fileType, $customName = null)
    {
        if (!$request->hasFile($inputName)) {
            return $request->input($inputName);
        }

        $file = $request->file($inputName);
        $folder = $this->determineFolder($fileType);
        $disk = $this->determineDisk($fileType);

        return $this->processFileUpload($file, $customName, $folder, $disk);
    }

    private function processFileUpload($file, $customName, $folder, $disk)
    {
        $randomName = uniqid();
        $fileName = ($customName ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $randomName;
        $extension = $file->getClientOriginalExtension();
        $fileFullName = $fileName . '.' . $extension;

        $path = $file->storeAs($folder, $fileFullName, $disk);
        return $this->domainName . '/assets/' . $path;
    }

    public function deleteFile(string $filePath)
    {
        $fileName = basename($filePath);
        $folder = $this->determineFolderByExtension($fileName);
        $fileFullPath = public_path("assets/{$folder}/{$fileName}");

        if (file_exists($fileFullPath)) {
            unlink($fileFullPath);
        }
    }

    private function determineFolder(string $fileType)
    {
        return match ($fileType) {
            'image' => 'images',
            'video' => 'videos',
            default => 'files',
        };
    }

    private function determineDisk(string $fileType)
    {
        return match ($fileType) {
            'image' => 'image',
            'video' => 'video',
            default => 'file',
        };
    }

    private function determineFolderByExtension(string $fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'svg' => 'images',
            'mp4' => 'videos',
            'pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx' => 'files',
            default => 'files',
        };
    }
}
