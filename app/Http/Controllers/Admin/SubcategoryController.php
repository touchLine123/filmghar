<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller {
    public function index() {
        $pageTitle     = "All Subcategory";
        $categories    = Category::active()->orderBy('id', 'desc')->get();
        $subcategories = SubCategory::orderBy('id', 'desc')->searchable(['name', 'category:name'])->with('category')->paginate(getPaginate());
        return view('admin.subcategory.index', compact('pageTitle', 'subcategories', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name'        => 'required',
            'category_id' => 'required',
        ]);

        if ($id == 0) {
            $subcategory  = new SubCategory();
            $notification = 'Subcategory added successfully.';
        } else {
            $subcategory  = SubCategory::findOrFail($id);
            $notification = 'Subcategory updated successfully';
        }

        $subcategory->name        = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return SubCategory::changeStatus($id);
    }
}
