<?php

namespace App\Lib;

class MultiVideoUploader {
    public static function checkSizeValidation() {
        $request = request();

        if ($request->hasFile('three_sixty_video')) {
            $fileSize = $request->file('three_sixty_video')->getSize();
            if ($fileSize > 4194304000) {
                return ['error' => true, 'message' => 'File size must be lower then 4 gb'];
            }
        }

        if ($request->hasFile('four_eighty_video')) {
            $fileSize = $request->file('four_eighty_video')->getSize();
            if ($fileSize > 4194304000) {
                return ['error' => true, 'message' => 'File size must be lower then 4 gb'];
            }
        }

        if ($request->hasFile('seven_twenty_video')) {
            $fileSize = $request->file('seven_twenty_video')->getSize();
            if ($fileSize > 4194304000) {
                return ['error' => true, 'message' => 'File size must be lower then 4 gb'];

            }
        }

        if ($request->hasFile('thousand_eighty_video')) {
            $fileSize = $request->file('thousand_eighty_video')->getSize();
            if ($fileSize > 4194304000) {
                return ['error' => true, 'message' => 'File size must be lower then 4 gb'];
            }
        }

        return ['error' => false];
    }

    public static function multiQualityVideoUpload($oldVideo, $quality) {
        $request     = request();
        $qualityName = $quality . '_video';
        $serverName  = 'server_' . $quality;
        $linkName    = $quality . '_link';

        $video  = null;
        $server = null;

        if ($oldVideo) {
            $video  = $oldVideo->$qualityName;
            $server = $oldVideo->$serverName;
        }

        if (@$request->$linkName || @$request->$qualityName) {
            if ($request->hasFile($qualityName)) {
                $videoUploader            = new VideoUploader();
                $videoUploader->file      = $request->file($qualityName);
                $videoUploader->oldFile   = $video;
                $videoUploader->oldServer = $server;
                $videoUploader->upload();
                $error = $videoUploader->error;

                if ($error) {
                    return ['error' => true, 'message' => 'Could not upload the Video'];
                }

                $video  = $videoUploader->fileName;
                $server = $videoUploader->uploadedServer;
            } else {
                $removeFile          = new VideoUploader();
                $removeFile->oldFile = $video;
                $removeFile->removeOldFile();

                $video  = $request->$linkName;
                $server = 4;
            }
        }
        return ['error' => false, $qualityName => $video, 'server' => $server];
    }
}