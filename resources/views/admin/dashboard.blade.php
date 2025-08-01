@extends('admin.master')

@section('title', 'Trang Quản Trị')

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-section mb-4">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2 class="welcome-title">Chào mừng trở lại {{ auth()->user()->name }} !</h2>
                    <p class="welcome-subtitle">Tổng quan hoạt động hệ thống hôm nay</p>
                </div>
                <div class="welcome-time">
                    <div class="current-time" id="currentTime"></div>
                    <div class="current-date" id="currentDate"></div>
                </div>
            </div>
        </div>

        @can('delivery workers')
            <!-- Delivery Workers Dashboard -->
            <div class="section-title mb-3">
                <h4><i class="fas fa-truck-moving me-2"></i>Quản lý giao hàng</h4>
            </div>
            <div class="stats-grid mb-5">
                <div class="stat-card stat-card-warning">
                    <a href="{{ route('order.trackingOrder') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $orderAssign ?? 0 }}</div>
                            <div class="stat-label">Đơn hàng cần giao</div>
                            <div class="stat-description">Cần giao trong ngày</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-info">
                    <a href="{{ route('order.trackingOrder') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $orderProcessing ?? 0 }}</div>
                            <div class="stat-label">Đơn hàng đang giao</div>
                            <div class="stat-description">Đang trong quá trình</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-success">
                    <a href="{{ route('order.trackingOrder') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $orderSuccess ?? 0 }}</div>
                            <div class="stat-label">Giao thành công</div>
                            <div class="stat-description">Trong tháng này</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('managers')
            <!-- Managers Dashboard -->
            <div class="section-title mb-3">
                <h4><i class="fas fa-chart-line me-2"></i>Tổng quan kinh doanh</h4>
            </div>
            <div class="stats-grid mb-5">
                <div class="stat-card stat-card-primary">
                    <a href="{{ route('staff.index') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $staffQuantity }}</div>
                            <div class="stat-label">Nhân viên</div>
                            <div class="stat-description">Tổng số hiện tại</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-success">
                    <a href="{{ route('admin.revenueDay') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Doanh thu tháng</div>
                            <div class="stat-description">Xem chi tiết ngay</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-warning">
                    <a href="{{ route('admin.profitYear') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Lợi nhuận</div>
                            <div class="stat-description">Quản lý chi tiết</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('salers')
            <!-- Sales Dashboard -->
            <div class="section-title mb-3">
                <h4><i class="fas fa-shopping-cart me-2"></i>Quản lý bán hàng</h4>
            </div>
            <div class="stats-grid mb-5">
                <div class="stat-card stat-card-info">
                    <a href="{{ route('customer.index') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $customerQuantity }}</div>
                            <div class="stat-label">Khách hàng</div>
                            <div class="stat-description">Đã đăng ký</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-secondary">
                    <a href="{{ route('order.index') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $orderQuantity }}</div>
                            <div class="stat-label">Chờ xử lý</div>
                            <div class="stat-description">Đơn hàng mới</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-gradient">
                    <a href="{{ route('admin.revenueProductBestSeller') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Best Seller</div>
                            <div class="stat-description">Tuần qua</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-success">
                    <a href="{{ route('admin.revenueProductBestSellerMonthYear') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Top Seller</div>
                            <div class="stat-description">Tháng này</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="modern-table-card mb-5">
                <div class="table-header">
                    <h5><i class="fas fa-list-alt me-2"></i>Đơn hàng mới nhất</h5>
                    <a href="{{ route('order.index') }}" class="btn-modern btn-primary">
                        <i class="fas fa-eye me-1"></i>Xem tất cả
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderPending as $item)
                                <tr>
                                    <td>
                                        <span class="order-id">#{{ $item->id }}</span>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <i class="fas fa-user-circle me-2"></i>
                                            {{ $item->customer_name }}
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock me-1"></i>{{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="amount">{{ number_format($item->total) }} đ</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('order.index') }}" class="btn-action btn-process">
                                            <i class="fas fa-cog me-1"></i>Xử lý
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Chưa có đơn hàng nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orderPending->hasPages())
                    <div class="table-pagination">
                        {{ $orderPending->links() }}
                    </div>
                @endif
            </div>
        @endcan

        @can('warehouse workers')
            <!-- Warehouse Dashboard -->
            <div class="section-title mb-3">
                <h4><i class="fas fa-warehouse me-2"></i>Quản lý kho hàng</h4>
            </div>

            <!-- Alert Card -->
            <div class="alert-card alert-warning mb-4">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <strong>Cảnh báo tồn kho!</strong>
                    <p>Có <span class="highlight">{{ $productOutOfStock ?? 0 }} sản phẩm</span> đang trong tình trạng tồn kho
                        thấp</p>
                </div>
                <a href="{{ route('admin.revenueInventory') }}" class="alert-action">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card stat-card-danger">
                    <a href="{{ route('admin.revenueInventory') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Sản phẩm</div>
                            <div class="stat-description">Gần hết hàng</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>

                <div class="stat-card stat-card-secondary">
                    <a href="{{ route('provider.index') }}" class="stat-link">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Nhà cung cấp</div>
                            <div class="stat-description">Quản lý</div>
                        </div>
                        <div class="stat-arrow">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('managers')
            <!-- Revenue Chart -->
            <div class="chart-card mt-5">
                <div class="chart-header">
                    <div>
                        <h5><i class="fas fa-chart-area me-2"></i>Doanh thu theo tháng - {{ now()->year }}</h5>
                        <p class="chart-subtitle">Tổng quan hiệu suất kinh doanh</p>
                    </div>
                    <a href="{{ route('admin.revenueMonth') }}" class="btn-modern btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Chi tiết
                    </a>
                </div>

                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="stats-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value">{{ number_format(array_sum($total ?? []), 0, ',', '.') }} VNĐ</div>
                            <div class="summary-label">Tổng doanh thu</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value">
                                {{ number_format(count($total ?? []) > 0 ? array_sum($total) / count($total) : 0, 0, ',', '.') }}
                                VNĐ</div>
                            <div class="summary-label">Trung bình tháng</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value">
                                {{ number_format(count($total ?? []) > 0 ? max($total) : 0, 0, ',', '.') }} VNĐ</div>
                            <div class="summary-label">Cao nhất</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Toast Notification -->
    <div id="toast-success" class="toast-notification toast-success" role="alert">
        <div class="toast-content">
            <i class="fas fa-check-circle me-2"></i>
            <span>Đăng nhập thành công!</span>
        </div>
        <button type="button" class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

@endsection

@section('css')
    <style>
        /* Modern Dashboard Styles */
        .dashboard-container {
            padding: 0;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Welcome Section */
        .welcome-section {
            margin-bottom: 2rem;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .welcome-subtitle {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .welcome-time {
            text-align: right;
        }

        .current-time {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .current-date {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Section Titles */
        .section-title {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }

        .section-title h4 {
            margin: 0;
            color: #1e293b;
            font-weight: 600;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .stat-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .stat-description {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .stat-arrow {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover .stat-arrow {
            opacity: 1;
        }

        /* Color Variants */
        .stat-card-primary .stat-icon {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .stat-card-primary .stat-number {
            color: #3b82f6;
        }

        .stat-card-success .stat-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .stat-card-success .stat-number {
            color: #10b981;
        }

        .stat-card-warning .stat-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .stat-card-warning .stat-number {
            color: #f59e0b;
        }

        .stat-card-danger .stat-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .stat-card-danger .stat-number {
            color: #ef4444;
        }

        .stat-card-info .stat-icon {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .stat-card-info .stat-number {
            color: #06b6d4;
        }

        .stat-card-secondary .stat-icon {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .stat-card-secondary .stat-number {
            color: #6b7280;
        }

        .stat-card-gradient .stat-icon {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .stat-card-gradient .stat-number {
            color: #8b5cf6;
        }

        /* Alert Card */
        .alert-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #f59e0b;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
        }

        .alert-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f59e0b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .alert-content {
            flex: 1;
        }

        .alert-content strong {
            color: #92400e;
            display: block;
            margin-bottom: 0.25rem;
        }

        .alert-content p {
            margin: 0;
            color: #78350f;
        }

        .highlight {
            background: #f59e0b;
            color: white;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .alert-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f59e0b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .alert-action:hover {
            transform: scale(1.1);
            color: white;
        }

        /* Modern Table */
        .modern-table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .table-header h5 {
            margin: 0;
            color: #1f2937;
            font-weight: 600;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: #f9fafb;
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .modern-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: #f9fafb;
        }

        .order-id {
            font-family: 'Monaco', 'Consolas', monospace;
            background: #e5e7eb;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .customer-info {
            display: flex;
            align-items: center;
            color: #374151;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: #dbeafe;
            color: #1e40af;
        }

        .amount {
            font-weight: 600;
            color: #059669;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn-process {
            background: #3b82f6;
            color: white;
        }

        .btn-process:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            color: #d1d5db;
        }

        /* Chart Card */
        .chart-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .chart-header {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #e5e7eb;
        }

        .chart-header h5 {
            margin: 0;
            color: #1f2937;
            font-weight: 600;
        }

        .chart-subtitle {
            margin: 0.5rem 0 0 0;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .chart-container {
            padding: 1.5rem;
            height: 400px;
        }

        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .summary-label {
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Modern Buttons */
        .btn-modern {
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1100;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(400px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid #10b981;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-success {
            border-left-color: #10b981;
        }

        .toast-content {
            display: flex;
            align-items: center;
            color: #065f46;
            font-weight: 500;
        }

        .toast-content i {
            color: #10b981;
        }

        .toast-close {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: color 0.2s ease;
        }

        .toast-close:hover {
            color: #374151;
        }

        /* Pagination */
        .table-pagination {
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .welcome-card {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .welcome-time {
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .chart-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .stats-summary {
                grid-template-columns: 1fr;
            }

            .modern-table {
                font-size: 0.875rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 0.5rem;
            }

            .welcome-card {
                padding: 1.5rem;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-link {
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
            }

            .stat-arrow {
                display: none;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chart Styles */
        #revenueChart {
            max-height: 350px;
        }

        /* Loading States */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Scrollbar Styling */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f3f4;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c7cd;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8b2ba;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Update time and date
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.stat-card, .modern-table-card, .chart-card, .alert-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });

            // Initialize chart if data exists
            @if (isset($revenueByMonth))
                initRevenueChart();
            @endif

            // Show success toast if exists
            @if (Session::has('success'))
                showToast();
            @endif
        });

        function updateDateTime() {
            const now = new Date();
            const timeElement = document.getElementById('currentTime');
            const dateElement = document.getElementById('currentDate');

            if (timeElement && dateElement) {
                const timeOptions = {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                const dateOptions = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };

                timeElement.textContent = now.toLocaleTimeString('vi-VN', timeOptions);
                dateElement.textContent = now.toLocaleDateString('vi-VN', dateOptions);
            }
        }

        @if (isset($revenueByMonth))
            function initRevenueChart() {
                const revenueData = @json(array_values($revenueByMonth));
                const revenueLabels = @json(array_keys($revenueByMonth));

                const ctx = document.getElementById('revenueChart').getContext('2d');

                // Create gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenueData,
                            backgroundColor: gradient,
                            borderColor: '#3b82f6',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#1d4ed8',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                titleColor: '#f3f4f6',
                                bodyColor: '#f3f4f6',
                                borderColor: '#374151',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return `Doanh thu: ${context.parsed.y.toLocaleString('vi-VN')} đ`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6b7280',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6b7280',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + ' đ';
                                    }
                                }
                            }
                        },
                        elements: {
                            point: {
                                hoverRadius: 8
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }
        @endif

        function showToast() {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.classList.add('show');

                // Auto hide after 5 seconds
                setTimeout(() => {
                    hideToast();
                }, 5000);

                // Close button functionality
                const closeBtn = toast.querySelector('.toast-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', hideToast);
                }
            }
        }

        function hideToast() {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.classList.remove('show');
            }
        }

        // Add smooth scrolling to anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading states for buttons
        document.querySelectorAll('.btn-modern').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.classList.contains('loading')) {
                    this.classList.add('loading');
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang tải...';

                    // Remove loading state after navigation
                    setTimeout(() => {
                        this.classList.remove('loading');
                        this.innerHTML = originalText;
                    }, 1000);
                }
            });
        });
    </script>
@endsection
