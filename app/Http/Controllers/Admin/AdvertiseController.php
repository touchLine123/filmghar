<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\VideoUploader;
use App\Models\Advertise;
use App\Models\VideoAdvertise;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AdvertiseController extends Controller {
    public function index() {
        $ads       = Advertise::orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = "Advertises";
        return view('admin.advertise.index', compact('ads', 'pageTitle'));
    }

    public function store(Request $request, $id = 0) {
        $imageValidate = $id ? 'nullable' : 'required';
        if ($request->ads_type == 'script') {
            $imageValidate = 'nullable';
        }
        $request->validate([
            'type'     => 'required|integer|in:1,2',
            'device'   => 'required|integer|in:1,2',
            'ads_show' => 'required|integer|in:1,2',
            'ads_type' => 'required|string|in:banner,script',
            'image'    => [$imageValidate, new FileTypeValidate(['jpg', 'jpeg', 'png', 'gif'])],
        ]);

        if ($id == 0) {
            $advertise    = new Advertise();
            $notification = 'Advertise added successfully';
            $oldFile      = null;
        } else {
            $advertise    = Advertise::findOrFail($id);
            $notification = 'Advertise updated successfully';
            $oldFile      = $advertise->content->image;
            $filename     = $advertise->content->image;
        }

        $advertise->type     = $request->type;
        $advertise->device   = $request->device;
        $advertise->ads_show = $request->ads_show;
        $advertise->ads_type = $request->ads_type;

        if ($request->hasFile('image')) {
            try {
                if ($request->ads_show == 1) {
                    $size = $request->device == 1 ? '1200x700' : '400x500';
                } else {
                    $size = '728x90';
                }
                $filename = fileUploader($request->image, getFilePath('ads'), $size, $oldFile);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }

        }

        $data = [
            'image'  => @$filename,
            'link'   => $request->ads_type == 'banner' ? $request->link : null,
            'script' => $request->ads_type == 'script' ? $request->script : null,
        ];

        $advertise->content = $data;
        $advertise->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function remove($id) {
        $ads = Advertise::findOrFail($id);
        fileManager()->removeFile(getFilePath('ads') . '/' . @$ads->content->image);
        $ads->delete();
        $notify[] = ['success', 'Advertise deleted successfully'];
        return back()->withNotify($notify);
    }

    public function videoAdvertise() {
        $pageTitle = "Video Advertises";
        $ads       = VideoAdvertise::latest()->paginate(getPaginate());
        return view('admin.advertise.videos', compact('ads', 'pageTitle'));
    }

    public function videoAdvertiseStore(Request $request, $id = 0) {
        $request->validate([
            'type'  => 'required|integer|in:1,2',
            'link'  => 'required_if:type,1|nullable',
            'video' => ['required_if:type,2', 'nullable', new FileTypeValidate(['mp4', 'mkv', '3gp'])],
        ]);

        if ($request->link && preg_match('/\.m3u8$/', $request->link)) {
            $notify[] = ['error', '.m3u8 format file is not supported'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $videoAd      = VideoAdvertise::findOrFail($id);
            $oldFile      = $videoAd->content->video;
            $notification = 'Advertise updated successfully';
        } else {
            $videoAd      = new VideoAdvertise();
            $notification = 'Advertise created successfully';
            $oldFile      = null;
        }

        $videoAd->type = $request->type;
        $filename      = null;
        if ($request->video) {
            $videoUploader            = new VideoUploader();
            $videoUploader->file      = $request->file('video');
            $videoUploader->oldFile   = $oldFile;
            $videoUploader->oldServer = @$videoAd->server;
            $videoUploader->upload();

            $error = $videoUploader->error;
            if ($error) {
                $notify[] = ['error', 'Could not upload the Video'];
                return back()->withNotify($notify);
            }

            $filename = $videoUploader->fileName;

            $serverName = gs('server');
            if ($serverName == 'current') {
                $server = Status::CURRENT_SERVER;
            } else if ($serverName == 'current-ftp') {
                $server = Status::FTP_SERVER;
            } else if ($serverName == 'wasabi') {
                $server = Status::WASABI_SERVER;
            } else {
                $server = Status::DIGITAL_OCEAN_SERVER;
            }

            $videoAd->server = $server;
        }
        $data = [
            'link'  => $request->link,
            'video' => $filename,
        ];
        $videoAd->content = $data;
        $videoAd->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function videoAdvertiseRemove($id) {
        $ads = VideoAdvertise::findOrFail($id);
        if (@$ads->content->video) {
            $general = gs();
            $path    = $ads->server ? $general->ftp->domain : getFilePath('ads');
            fileManager()->removeFile($path . '/' . @$ads->content->video);
        }
        $ads->delete();
        $notify[] = ['success', 'Advertise deleted successfully'];
        return back()->withNotify($notify);
    }
}
