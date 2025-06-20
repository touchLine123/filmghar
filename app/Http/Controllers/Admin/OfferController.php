<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller {
    public function index() {
        $pageTitle = "All Offers";
        $plans     = Offer::latest()->paginate(getPaginate());
        return view('admin.offer.index', compact('pageTitle', 'plans'));
    }

    public function store(Request $request, $id = 0) {
        $limitValidate = gs()->device_limit ? 'required' : 'nullable';
        $request->validate([
            'name'         => 'required|unique:plans,name,' . $id,
            'price'        => 'required|numeric|gt:0',
            'duration'     => 'required|integer|gt:0',
            'no_of_movies' => 'required',
            // 'icon'         => 'required|string',
            // 'app_code'     => 'required|string|max:40|unique:plans,app_code,' . $id,
            'device_limit' => "$limitValidate|integer|gte:0",
        ]);

        if ($id == 0) {
            $plan         = new Offer();
            $notification = 'Offer created successfully';
        } else {
            $plan         = Offer::findOrFail($id);
            $notification = 'Offer updated successfully';
        }

        $plan->name     = $request->name;
        $plan->pricing  = $request->price;
        $plan->duration = $request->duration;
        $plan->no_of_movies = $request->no_of_movies;
        // $plan->icon     = $request->icon;
        // $plan->app_code = $request->app_code;
        $plan->show_ads = $request->show_ads ? Status::ENABLE : Status::DISABLE;
        if (gs('device_limit')) {
            $plan->device_limit = $request->device_limit;
        }
        $plan->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Offer::changeStatus($id);
    }
}
