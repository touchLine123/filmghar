<?php

namespace App\Lib;

use App\Constants\Status;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideoUploader {
    private $general;

    public $path;
    public $file;
    public $oldFile;
    public $oldServer;
    public $uploadedServer;
    public $fileName;
    public $error;

    public function __construct() {
        $this->general = gs();
        $date          = date('Y') . '/' . date('m') . '/' . date('d');
        $this->date    = $date;
    }

    public function upload() {
        $general = $this->general;

        $uploadDisk = $general->server;

        if ($this->oldFile) {
            $this->removeOldFile();
        }
        if ($uploadDisk == 'current') {
            $this->uploadedServer = Status::CURRENT_SERVER;
            return $this->uploadToCurrentServer();
        }
        if ($uploadDisk == 'custom-ftp') {
            $this->uploadedServer = Status::FTP_SERVER;
            $this->uploadToServer('custom-ftp', 'videos');
        }
        if ($uploadDisk == 'wasabi') {
            $this->uploadedServer = Status::WASABI_SERVER;
            $this->uploadToServer('wasabi', 'videos');
        }
        if ($uploadDisk == 'digital_ocean') {
            $this->uploadedServer = Status::DIGITAL_OCEAN_SERVER;
            $this->uploadToServer('digital_ocean', 'videos');
        }
    }

    public function uploadToCurrentServer() {
        $file     = $this->file;
        $location = 'assets/videos/';
        $path     = $location . $this->date;

        try {
            $video          = $this->date . '/' . fileUploader($file, $path, null);
            $this->fileName = $video;
        } catch (\Exception $exp) {
            $this->error = true;
        }
    }

    public function uploadToServer($server = null, $param = null) {
        $file     = $this->file;
        $location = $param . '/';
        $path     = $location . $this->date;

        try {
            $fileExtension = $file->getClientOriginalExtension();
            if ($server == 'wasabi' || $server == 'digital_ocean') {
                $this->configureDisk($server);
            } else {
                $this->configureFTP();
            }

            $file = File::get($file);
            $disk = Storage::disk($server);

            $this->makeDirectory($path, $disk);

            $video = uniqid() . time() . '.' . $fileExtension;
            $disk->put($path . '/' . $video, $file);
            $this->fileName = $path . '/' . $video;

        } catch (\Exception $e) {
            $this->error = true;
        }
    }

    private function makeDirectory($path, $disk) {
        if ($disk->exists($path)) {
            return true;
        }
        $disk->makeDirectory($path);
    }

    public function configureFTP() {
        $general = $this->general;
        //ftp
        Config::set('filesystems.disks.custom-ftp.driver', 'ftp');
        Config::set('filesystems.disks.custom-ftp.host', $general->ftp->host);
        Config::set('filesystems.disks.custom-ftp.username', $general->ftp->username);
        Config::set('filesystems.disks.custom-ftp.password', $general->ftp->password);
        Config::set('filesystems.disks.custom-ftp.port', 21);
        Config::set('filesystems.disks.custom-ftp.root', $general->ftp->root);

    }
    public function configureDisk($server) {
        $general = $this->general;
        Config::set('filesystems.disks.' . $server . '.visibility', 'public');
        Config::set('filesystems.disks.' . $server . '.driver', $general->$server->driver);
        Config::set('filesystems.disks.' . $server . '.key', $general->$server->key);
        Config::set('filesystems.disks.' . $server . '.secret', $general->$server->secret);
        Config::set('filesystems.disks.' . $server . '.region', $general->$server->region);
        Config::set('filesystems.disks.' . $server . '.bucket', $general->$server->bucket);
        Config::set('filesystems.disks.' . $server . '.endpoint', $general->$server->endpoint);
    }

    public function removeFtpVideo() {
        $oldFile = $this->oldFile;
        $storage = Storage::disk('custom-ftp');
        if ($storage->exists($oldFile)) {
            $storage->delete($oldFile);
        }
    }

    public function removeOldFile() {
        if ($this->oldServer == Status::CURRENT_SERVER) {
            $location = 'assets/videos/' . $this->oldFile;
            fileManager()->removeFile($location);
        } else if (in_array($this->oldServer, [Status::FTP_SERVER, Status::WASABI_SERVER, Status::DIGITAL_OCEAN_SERVER])) {
            try {
                if ($this->oldServer == Status::WASABI_SERVER) {
                    $server = 'wasabi';
                    $this->configureDisk($server);
                } else if ($this->oldServer == Status::DIGITAL_OCEAN_SERVER) {
                    $server = 'digital_ocean';
                    $this->configureDisk($server);
                } else if ($this->oldServer == Status::FTP_SERVER) {
                    $this->configureFTP();
                    $server = 'custom-ftp';
                }
                $disk = Storage::disk($server);
                $disk->delete($this->oldFile);
            } catch (\Exception $e) {
            }
        }
    }

}

