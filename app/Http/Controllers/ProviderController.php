<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Provider::orderby('id', 'ASC')->paginate();
        return view('admin.provider.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.provider.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rule = [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ];
        $message = [
            'name.required' => 'Trường này bắt buộc nhâp',
            'address.required' => 'Trường này bắt buộc nhâp',
            'phone.required' => 'Trường này bắt buộc nhâp',

        ];
        $data = $request->validate($rule, $message);
        $providers = new Provider();
        $providers->name = $data['name'];
        $providers->address = $data['address'];
        $providers->phone = $data['phone'];
        $providers->save();
        return redirect()->route('provider.index')->with("success", "Thêm nhà cung cấp mới thành công");
    }

    /**
     * Display the specified resource.
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Provider $provider)
    {
        return view('admin.provider.edit', compact('provider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Provider $provider)
    {
        $rule = [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ];
        $message = [
            'name.required' => 'Trường này bắt buộc nhâp',
            'address.required' => 'Trường này bắt buộc nhâp',
            'phone.required' => 'Trường này bắt buộc nhâp',

        ];
        $data = $request->validate($rule, $message);
        $provider->name = $data['name'];
        $provider->address = $data['address'];
        $provider->phone = $data['phone'];
        $provider->save();
        return redirect()->route('provider.index')->with("success", "Sửa thông tin nhà cung cấp thành công");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provider $provider)
    {
        if ($provider->Inventories()->count() == 0) {
            $provider->delete();
            return redirect()->back()->with('success', 'Xoá nhà cung cấp thành công!');
        }
        return redirect()->back()->with('error', 'Xoá thất bại!');
    }

    public function search(Request $request){
        $keyword = $request->input('query');
        $data = Provider::where('name', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%")
                        ->paginate();
        return view('admin.provider.index', compact('data', 'keyword'));
    }

}
