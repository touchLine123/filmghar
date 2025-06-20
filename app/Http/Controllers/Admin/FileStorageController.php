<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileStorageController extends Controller {
    public function ftp() {
        $pageTitle = "FTP Setting";
        return view('admin.storage.ftp', compact('pageTitle'));
    }

    public function ftpUpdate(Request $request) {
        $request->validate([
            'ftp.host'     => 'required',
            'ftp.username' => 'required',
            'ftp.password' => 'required',
            'ftp.port'     => 'required',
            'ftp.root'     => 'required',
            'ftp.domain'   => 'required',
        ]);
        $setting      = gs();
        $setting->ftp = $request->ftp;
        $setting->save();

        $notify[] = ['success', 'FTP credentials Updated'];
        return back()->withNotify($notify);
    }
    public function wasabi() {
        $pageTitle = "Wasabi Setting";
        return view('admin.storage.wasabi', compact('pageTitle'));
    }

    public function wasabiUpdate(Request $request) {
        $request->validate([
            'wasabi.driver'   => 'required',
            'wasabi.key'      => 'required',
            'wasabi.secret'   => 'required',
            'wasabi.region'   => 'required',
            'wasabi.bucket'   => 'required',
            'wasabi.endpoint' => 'required',
        ]);
        $setting         = gs();
        $setting->wasabi = $request->wasabi;
        $setting->save();

        $notify[] = ['success', 'Wasabi credentials updated'];
        return back()->withNotify($notify);
    }
    public function digitalOcean() {
        $pageTitle = "Digital Ocean Setting";
        $setting   = gs();
        return view('admin.storage.digital_ocean', compact('pageTitle', 'setting'));
    }

    public function digitalOceanUpdate(Request $request) {
        $request->validate([
            'digital_ocean.driver'   => 'required',
            'digital_ocean.key'      => 'required',
            'digital_ocean.secret'   => 'required',
            'digital_ocean.region'   => 'required',
            'digital_ocean.bucket'   => 'required',
            'digital_ocean.endpoint' => 'required',
        ]);
        $setting                = gs();
        $setting->digital_ocean = $request->digital_ocean;
        $setting->save();

        $notify[] = ['success', 'Digital ocean credentials updated'];
        return back()->withNotify($notify);
    }
}
