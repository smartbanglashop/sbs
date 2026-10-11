<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingDistrict;
use Illuminate\Http\Request;
use Toastr;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        $data = ShippingDistrict::orderBy('id', 'DESC')->get();

        return view('backEnd.districts.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.districts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required|unique:shipping_districts,name_en',
            'name_bn' => 'required|unique:shipping_districts,name_bn',
        ]);
        $input = $request->all();
        ShippingDistrict::create($input);
        Toastr::success('Success', 'Data insert successfully');

        return redirect()->route('district.index');
    }

    public function edit($id)
    {
        $edit_data = ShippingDistrict::find($id);

        return view('backEnd.districts.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required|unique:shipping_districts,name_en,'.$request->id,
            'name_bn' => 'required|unique:shipping_districts,name_bn,'.$request->id,
        ]);
        $update_data = ShippingDistrict::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');

        return redirect()->route('district.index');
    }

    public function inactive(Request $request)
    {
        $inactive = ShippingDistrict::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = ShippingDistrict::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = ShippingDistrict::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');

        return redirect()->back();
    }
}
