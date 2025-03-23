@can('managers')
    @extends('admin.master')
    @section('title', 'Thống kế lợi nhuận theo năm')
    @section('content')
        <canvas id="doanhThuChart" width="500" height="200"></canvas>
    @endsection

    @php
        // dd($result);
    @endphp

    @section('js')
    <!-- Chart JS -->
    <script src="{{asset('assets/js/plugin/chart.js/chart.min.js')}}"></script>

    <script>

        // Chuyển dữ liệu từ PHP sang JavaScript bằng hàm json_encode()
        const labels = @json($nam); // Ngày từ PHP
        const dataValues = @json($loiNhuan); // Doanh thu từ PHP

        // Vẽ biểu đồ với  bằng thư viện Chart.js
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        const doanhThuChart = new Chart(ctx, {
            type: 'line', // Loại biểu đồ đường
            data: {
                labels: labels, // Trục Ox là các dữ liệu về các ngày
                datasets: [{
                    label: 'Lợi nhuận theo năm',
                    data: dataValues, 
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
