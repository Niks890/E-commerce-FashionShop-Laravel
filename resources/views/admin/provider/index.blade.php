@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Thông tin Nhà cung cấp')
    @section('content')
    @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
        </div>
    @endif
    @if (Session::has('error'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
            <i class="fas fa-times p-2 bg-danger text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('error') }}
        </div>
    @endif
        <div class="card">
            <div class="card-body">
                <div class="card-sub">
                    <form method="GET" class="form-inline row" action="{{ route('provider.search') }}">
                        {{-- @csrf --}}
                        <div
                            class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input name="query" type="text" placeholder="Nhập vào tên hoặc số điện thoại nhà cung cấp..."
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('provider.create') }}" type="submit" class="btn btn-success"><i
                                    class="fa fa-plus"></i>Thêm mới</a>
                        </div>
                    </form>
                </div>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên nhà cung cấp</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Ngày thêm</th>
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->name }}</td>
                                <td>{{ $model->address }}</td>
                                <td>{{ $model->phone }}</td>
                                <td>{{ $model->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('provider.destroy', $model->id) }}">
                                        @csrf @method('DELETE')
                                        <a class="btn btn-sm btn-primary" href="{{ route('provider.edit', $model->id) }}"><i
                                                class="fa fa-edit pe-2"></i>Sửa</a>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa không?')">
                                            <i class="fa fa-trash pe-2"></i>
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $data->links() }}
        </div>
    @endsection


    @section('css')
        <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    @endsection

    @section('js')
        @if (Session::has('success') || Session::has('error'))
            <script src="{{ asset('assets/js/message.js') }}"></script>
        @endif
    @endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan
