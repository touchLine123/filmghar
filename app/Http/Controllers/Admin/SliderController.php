<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Slider;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class SliderController extends Controller {
    public function index() {
        $pageTitle = "Sliders";
        $items     = Item::hasVideo()->active()->orderBy('id', 'desc')->get(['id', 'title']);
        $sliders   = Slider::orderBy('id', 'desc')->with('item:id,title')->paginate(getPaginate());
        return view('admin.sliders.index', compact('pageTitle', 'items', 'sliders'));
    }

    public function addSlider(Request $request) {
        $request->validate([
            'item'  => 'required|integer',
            'image' => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        try {
            $general    = gs();
            $sliderPath = ($general->active_template == 'basic') ? 'slider' : 'labflixSlider';
            $image      = fileUploader($request->image, getFilePath($sliderPath), getFileSize($sliderPath), null);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Image could not be uploaded'];
            return back()->withNotify($notify);
        }

        $slider               = new Slider();
        $slider->item_id      = $request->item;
        $slider->image        = $image;
        $slider->caption_show = $request->caption_show ? Status::ENABLE : Status::DISABLE;
        $slider->save();

        $notify[] = ['success', 'Slider added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],

        ]);

        $slider = Slider::findOrFail($id);
        $image  = $slider->image;

        if ($request->hasFile('image')) {
            try {
                $general    = gs();
                $sliderPath = ($general->active_template == 'basic') ? 'slider' : 'labflixSlider';
                $image      = fileUploader($request->image, getFilePath($sliderPath), getFileSize($sliderPath), $slider->image);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }

        }

        $slider->image        = $image;
        $slider->caption_show = $request->caption_show ? Status::ENABLE : Status::DISABLE;
        $slider->save();

        $notify[] = ['success', 'Slider updated successfully'];
        return back()->withNotify($notify);
    }

    public function remove($id) {
        $slider = Slider::findOrFail($id);
        fileManager()->removeFile(getFilePath('slider') . '/' . $slider->image);
        $slider->delete();

        $notify[] = ['success', 'Slider deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Slider::changeStatus($id);
    }

}
