<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MoviePlans;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller {
    public function index() {
        $pageTitle = "All Subscription Plans";
        $plans     = Plan::latest()->paginate(getPaginate());
        $moviesListing = $this->rentItems();
        return view('admin.plan.index', compact('pageTitle','moviesListing', 'plans'));
    }

    public function rentItems()
    {
        return $this->itemsData('rentItems');
    }

    private function itemsData($scope = null)
    {
        $query = Item::select('id', 'title', 'slug', 'addedBy');
        if ($scope && method_exists(Item::class, 'scope' . $scope)) {
            $query = $query->{$scope}();
        }
        return $query->get();
    }
     
    public function store(Request $request, $id = 0) {
     // echo "<pre>"; print_r($request->all()); die;
        $limitValidate = gs()->device_limit ? 'required' : 'nullable';
        $request->validate([
            'name'         => 'required|unique:plans,name,' . $id,
            'price'        => 'required|numeric|gt:0',
            'duration'     => 'required|integer|gt:0',
            // 'icon'         => 'required|string',
            // 'app_code'     => 'required|string|max:40|unique:plans,app_code,' . $id,
            'device_limit' => "$limitValidate|integer|gte:0",
        ]);
 
        if ($id == 0) {
            $plan         = new Plan();
            $notification = 'Plan created successfully';
        } else {
            $plan         = Plan::findOrFail($id);
            $notification = 'Plan updated successfully';
        }

        $plan->name     = $request->name;
        $plan->pricing  = $request->price;
        $plan->duration = $request->duration;
        $plan->item_ids =  implode(',', $request->movies) ;
        $plan->item_name = $request->hiddenItemTitle;
        // $plan->icon     = $request->icon;
        // $plan->app_code = $request->app_code;
        $plan->show_ads = $request->show_ads ? Status::ENABLE : Status::DISABLE;
        if (gs('device_limit')) {
            $plan->device_limit = $request->device_limit;
        }
        $plan->save();


        $selectedMovieIds = $request->input('movies', []); // e.g. [7,8]
        $addedByList = explode(',', $request->hiddenAddedBy);
        MoviePlans::where('plan_id', $plan->id)->update(['status' => 0]);

         foreach ($selectedMovieIds as $index => $movieId) {
            $addedBy = $addedByList[$index] ?? null;

            $MoviePlans = MoviePlans::where('plan_id', $plan->id)
                ->where('item_id', $movieId)
                ->first();

            if ($MoviePlans) {
                $MoviePlans->status = 1; 
                $MoviePlans->save();
            } else {
                MoviePlans::create([
                    'plan_id' => $plan->id,
                    'item_id' => $movieId,
                    'addedBy'  => $addedBy,
                    'status'   => 1
                ]);
            }
        }

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Plan::changeStatus($id);
    }
}
