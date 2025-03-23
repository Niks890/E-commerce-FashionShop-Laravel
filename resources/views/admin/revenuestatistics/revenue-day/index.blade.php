@can('managers')
    @extends('admin.master')
    @section('title', 'Thống kế doanh thu theo ngày')
    @section('content')
        <canvas id="doanhThuChart" width="500" height="200"></canvas>
    @endsection

    @section('js')
    <!-- Chart JS -->
    <script src="{{asset('assets/js/plugin/chart.js/chart.min.js')}}"></script>

    <script>
        // Chuyển dữ liệu từ PHP sang JavaScript bằng hàm json_encode()
        const labels = @json($day); // Ngày từ PHP
        const dataValues = @json($total); // Doanh thu từ PHP

        // Vẽ biểu đồ với  bằng thư viện Chart.js
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        const doanhThuChart = new Chart(ctx, {
            type: 'line', // Loại biểu đồ đường
            data: {
                labels: labels, // Trục Ox là các dữ liệu về các ngày
                datasets: [{
                    label: 'Doanh Thu Bán Hàng Theo Ngày',
                    data: dataValues, // Trục y là doanh thu (tổng tiền bán hàng theo ngày)
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(20, 182, 192, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true // Bắt đầu trục Oy từ gốc toạ độ 0
                    }
                }
            }
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan
