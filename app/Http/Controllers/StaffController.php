<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Staff::orderby('id', 'ASC')->paginate();
        return view('admin.staff.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        $rule = [
            'name' => 'required|string|min:0|max:255',
            'phone' => 'required|unique:staff,phone',
            'address' => 'required',
            'email' => 'required|email',
            'avatar'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sex' => 'required',
            'username' => 'required|unique:staff,username',
            'password' => 'required',
            'position' => ['required', Rule::in(['Quản lý', 'Nhân viên bán hàng', 'Nhân viên kho', 'Nhân viên giao hàng'])],
            'status' => 'required',
        ];

        $message = [
            'name.required' => 'Họ tên nhân viên không được để trống.',
            'phone.required' => 'Số điện thoại không được để trống',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'address.required' => 'Vui lòng nhập địa chỉ của nhân viên.',
            'email.required' => 'Trường này bắt buộc nhập',
            'avatar.required' => 'Avatar không được để trống',
            'sex.required' => 'Trường này bắt buộc nhâp',
            'username.required' => 'Tên tài khoản không được để trống.',
            'username.unique' => 'Tài khoản này đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'position.required' => 'Vui lòng chọn chức vụ.',
            'position.in' => 'Vui lòng chọn chức vụ phù hợp.',
            'status.required' => 'Trường này bắt buộc nhâp',
        ];

        $data = $request->validate($rule, $message);
        $staff = new Staff();
        $staff->name = $data['name'];
        $staff->phone = $data['phone'];
        $staff->address = $data['address'];
        $staff->email = $data['email'];

        $uploadResult = $cloudinaryService->uploadImage($request->file('avatar')->getPathname(), 'staff_avatars');
        if (isset($uploadResult['error'])) {
            return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
        }
        $staff->avatar = $uploadResult['url'];
        $staff->sex = $data['sex'];
        $staff->username = $data['username'];
        $staff->password = bcrypt($data['password']);
        $staff->position = $data['position'];
        $staff->status = $data['status'];
        $staff->save();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'];
        $user->password = bcrypt($data['password']);
        if ($data['position'] === 'Quản lý') {
            $user->roles = 'manage,sale,inventory';
        } else if ($data['position'] === 'Nhân viên bán hàng') {
            $user->roles = 'sale';
        } else if ($data['position'] === 'Nhân viên kho') {
            $user->roles = 'inventory';
        } else if ($data['position'] === 'Nhân viên giao hàng') {
            $user->roles = 'delivery';
        }
        else {
            $user->roles = '';
        }
        $user->remember_token = Str::random(10);
        $user->save();

        return redirect()->route('staff.index')->with('success', 'Thêm nhân viên mới thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff,  CloudinaryService $cloudinaryService)
    {
        $data = $request->validate([
            'name' => 'required|string|min:0|max:255',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'old_avatar' => 'nullable|string',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required',
            'sex' => 'required',
            'position' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Trường này bắt buộc nhâp',
            'avatar.required' => 'Trường này bắt buộc nhâp',
            'phone.required' => 'Trường này bắt buộc nhâp',
            'address.required' => 'Trường này bắt buộc nhâp',
            'email.required' => 'Trường này bắt buộc nhâp',
            'sex.required' => 'Trường này bắt buộc nhâp',
            'position.required' => 'Trường này bắt buộc nhâp',
            'status.required' => 'Trường này bắt buộc nhâp',
        ]);
        $user = User::where('username', $staff->username)->first();
        $user->name = $data['name'];
        if ($data['position'] === 'Quản lý') {
            $user->roles = 'manage,sale,inventory';
        } else if ($data['position'] === 'Nhân viên bán hàng') {
            $user->roles = 'sale';
        } else if ($data['position'] === 'Nhân viên kho') {
            $user->roles = 'inventory';
        }else if ($data['position'] === 'Nhân viên giao hàng') {
            $user->roles = 'delivery';
        }
        else {
            $user->roles = '';
        }
        $user->save();
       // Cập nhật các field còn lại ngoài avatar
        $staff->name = $data['name'];
        $staff->phone = $data['phone'];
        $staff->address = $data['address'];
        $staff->email = $data['email'];
        $staff->sex = $data['sex'];
        $staff->position = $data['position'];
        $staff->status = $data['status'];

        // Nếu có avatar mới thì upload
        if($request->hasFile('avatar')) {
            $uploadResult = $cloudinaryService->uploadImage($request->file('avatar')->getPathname(), 'staff_avatars');

            if (isset($uploadResult['error'])) {
                return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
            }

            $staff->avatar = $uploadResult['url'];
        } else {
            // Nếu không upload ảnh mới, giữ nguyên ảnh cũ
            $staff->avatar = $data['old_avatar'];
        }
        $staff->save();


        $user_current = User::where('id', $request->input('user_id'))->first();
        if (in_array('admin', $user_current->roles()) || in_array('manage', $user_current->roles())) {
            return redirect()->route('staff.index')->with('success', 'Cập nhật thông tin nhân viên thành công!');
        } else {
            return redirect()->route('admin.dashboard')->with('success', 'Cập nhật thông tin nhân viên thành công!');
        }
        return redirect()->route('staff.index')->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        // if ($staff->Inventory->count() == 0) {
        $staff->delete();
        return redirect()->back();
        // }
        // return redirect()->back();
    }

    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $data = Staff::where('name', 'like', "%$keyword%")
            ->orWhere('phone', 'like', "%$keyword%")
            ->paginate();
        return view('admin.staff.index', compact('data', 'keyword'));
    }

    public function profile()
    {
        $staff = Staff::find(auth()->user()->id - 1);
        return view('admin.staff.profile', compact('staff'));
    }

    public function update_staff(Request $request, Staff $staff)
    {
        // Xác thực dữ liệu từ form
        $data = $request->validate([
            'name' => 'required|string|min:0|max:255',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required',
            'sex' => 'required',
            'position' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Trường này bắt buộc nhâp',
            'phone.required' => 'Trường này bắt buộc nhâp',
            'address.required' => 'Trường này bắt buộc nhâp',
            'email.required' => 'Trường này bắt buộc nhâp',
            'sex.required' => 'Trường này bắt buộc nhâp',
            'position.required' => 'Trường này bắt buộc nhâp',
            'status.required' => 'Trường này bắt buộc nhâp',
        ]);

        // Cập nhật thông tin người dùng (User)
        $user = User::where('username', $staff->username)->first();

        if ($user) {
            // Cập nhật các trường thông tin của user
            $user->name = $data['name'];
            $user->email = $data['email'];

            // Cập nhật vai trò của người dùng theo vị trí
            if ($data['position'] === 'Quản lý') {
                $user->roles = 'manage,sale,inventory';
            } elseif ($data['position'] === 'Nhân viên bán hàng') {
                $user->roles = 'sale';
            } elseif ($data['position'] === 'Nhân viên kho') {
                $user->roles = 'inventory';
            } else {
                $user->roles = '';
            }
            $user->save();
        } else {
            return redirect()->route('staff.index')->with('error', 'Không tìm thấy người dùng!');
        }

        // Cập nhật thông tin trong bảng staff
        $staff->update($data);

        // Debugging: Kiểm tra giá trị của user_id từ request
        $user_id = $request->input('user_id');
        // \Log::info("User ID from request: $user_id"); // Bạn có thể kiểm tra trong file logs của Laravel

        // Lấy thông tin người dùng hiện tại
        $user_current = User::find($user_id);

        // Debugging: Kiểm tra xem $user_current có null không
        if (!$user_current) {
            return redirect()->route('staff.profile')->with('error', 'Không tìm thấy người dùng hiện tại!');
        }

        // Kiểm tra quyền của người dùng hiện tại
        if (in_array('admin', $user_current->roles()) || in_array('manage', $user_current->roles())) {
            return redirect()->route('staff.profile')->with('success', 'Cập nhật thông tin nhân viên thành công!');
        } else {
            return redirect()->route('staff.profile')->with('success', 'Cập nhật thông tin nhân viên thành công!');
        }
    }
}
