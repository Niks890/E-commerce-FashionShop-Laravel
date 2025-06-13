<h2>Danh sách sản phẩm sắp hết hàng:</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng tồn</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->ProductVariants->stock }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p>Vui lòng nhập hàng bổ sung!</p>
