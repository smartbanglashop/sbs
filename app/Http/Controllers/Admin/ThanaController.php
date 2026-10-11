<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingDistrict;
use App\Models\Thana;
use Illuminate\Http\Request;
use Toastr;

class ThanaController extends Controller
{
    public function index(Request $request)
    {
        $data = Thana::with('district')->orderBy('id', 'DESC')->get();

        return view('backEnd.thana.index', compact('data'));
    }

    public function create()
    {
        $districts = ShippingDistrict::all();

        return view('backEnd.thana.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required|unique:thanas,name_en',
            'name_bn' => 'required|unique:thanas,name_bn',
            'shipping_district_id' => 'required',
        ]);
        $input = $request->all();
        Thana::create($input);
        Toastr::success('Success', 'Data insert successfully');

        return redirect()->route('thana.index');
    }

    public function edit($id)
    {
        $districts = ShippingDistrict::all();
        $edit_data = Thana::find($id);

        return view('backEnd.thana.edit', compact('edit_data', 'districts'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required|unique:thanas,name_en,'.$request->id,
            'name_bn' => 'required|unique:thanas,name_bn,'.$request->id,
            'shipping_district_id' => 'required',
        ]);
        $update_data = Thana::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');

        return redirect()->route('thana.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Thana::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = Thana::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = Thana::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');

        return redirect()->back();
    }
}
