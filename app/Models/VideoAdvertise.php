<?php

namespace App\Models;

use App\Constants\Status;
use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoAdvertise extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts   = [
        'content' => 'object',
    ];

    public function videoAds(): Attribute {
        return new Attribute(function () {
            $general = gs();
            $ads     = null;
            if (@$this->server == Status::FTP_SERVER) {
                $ads = $general->ftp->domain . '/' . $this->content->video;
            } else if (@$this->server == Status::WASABI_SERVER) {
                $general = gs();
                $s3      = new S3Client([
                    'endpoint'    => $general->wasabi->endpoint,
                    'region'      => $general->wasabi->region,
                    'version'     => 'latest',
                    'credentials' => [
                        'key'    => $general->wasabi->key,
                        'secret' => $general->wasabi->secret,
                    ],
                ]);

                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $general->wasabi->bucket,
                    'Key'    => $this->content->video,
                    'ACL'    => 'public-read',
                ]);
                $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                $ads     = (string) $request->getUri();

            } else if (@$this->server == Status::DIGITAL_OCEAN_SERVER) {
                $general = gs();
                $s3      = new S3Client([
                    'endpoint'    => $general->digital_ocean->endpoint,
                    'region'      => $general->digital_ocean->region,
                    'version'     => 'latest',
                    'credentials' => [
                        'key'    => $general->digital_ocean->key,
                        'secret' => $general->digital_ocean->secret,
                    ],
                ]);

                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $general->digital_ocean->bucket,
                    'Key'    => $this->content->video,
                    'ACL'    => 'public-read',
                ]);

                $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                $ads     = (string) $request->getUri();
            } else {
                $ads = asset('assets/videos/' . $this->content->video);
            }
            return $ads;
        });
    }
}
