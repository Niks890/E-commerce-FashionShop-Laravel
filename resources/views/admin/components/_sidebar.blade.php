<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="assets/img/TSTShop/LogoTSTFashionShop.webp" width="50px" alt="navbar brand"
                    class="navbar-brand rounded-circle mt-4" height="50" />
                <h4 class="text-white mt-4 ms-2">TFashionShop</h4>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item">
                    <a>
                        <p>Trang Quản Trị</p>
                    </a>
                </li>
                @can('salers')
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base1">
                            <i class="fas fa-layer-group"></i>
                            <p>Quản lý Danh mục</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base1">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('category.index') }}">
                                        <span class="sub-item">Thông tin Danh mục</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base2">
                            <i class="fas fa-tshirt"></i>
                            <p>Quản lý Sản phẩm</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base2">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('product.index') }}">
                                        <span class="sub-item">Thông tin Sản phẩm</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.revenueProductBestSeller') }}">
                                        <span class="sub-item">Thống kê sản phẩm bán chạy theo ngày</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.revenueProductBestSellerMonthYear') }}">
                                        <span class="sub-item">Thống kê sản phẩm bán chạy theo tháng & năm</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base8">
                            <i class="fas fa-box"></i>
                            <p>Quản lý Đơn hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base8">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('order.index') }}">
                                        <span class="sub-item">Đơn hàng chưa xử lý</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order.approval') }}">
                                        <span class="sub-item">Đơn hàng đã xử lý</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order.orderSuccess') }}">
                                        <span class="sub-item">Đơn hàng đã hoàn thành</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base3">
                            <i class="fas fa-percentage"></i>
                            <p>Quản lý Khuyến mãi/Voucher</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base3">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('discount.index') }}">
                                        <span class="sub-item">Thông tin Khuyến mãi</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('voucher.index') }}">
                                        <span class="sub-item">Thông tin Voucher</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                @can('delivery workers')
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base999">
                            <i class="fas fa-layer-group"></i>
                            <p>Quản lý giao hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base999">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('order.trackingOrder') }}">
                                        <span class="sub-item">Đơn hàng đang giao</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                @can('warehouse workers')
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base4">
                            <i class="fas fa-store-alt"></i>
                            <p>Quản lý Nhà cung cấp</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base4">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('provider.index') }}">
                                        <span class="sub-item">Thông tin Nhà cung cấp</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base5">
                            <i class="fas fa-warehouse"></i>
                            <p>Quản lý Nhập hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base5">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('inventory.index') }}">
                                        <span class="sub-item">Quản lý nhập hàng</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.revenueInventory') }}">
                                        <span class="sub-item">Quản lý tồn kho hiện tại</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <span class="sub-item">Thống kê tồn kho</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <span class="sub-item">Thống kê chi phí nhập hàng</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                @can('managers')
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base6">
                            <i class="fas fa-users"></i>
                            <p>Quản lý Nhân viên</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base6">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('staff.index') }}">
                                        <span class="sub-item">Thông tin Nhân viên</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('staff.create') }}">
                                        <span class="sub-item">Thêm mới Nhân viên</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base11">
                            <i class="fas fa-newspaper"></i>
                            <p>Quản lý Blog</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base11">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('blog.index') }}">
                                        <span class="sub-item">Thông tin bài viết</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base9999">
                            <i class="fas fa-layer-group"></i>
                            <p>Quản lý Đánh giá</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base9999">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('comment.index') }}">
                                        <span class="sub-item">Lọc/Ẩn đánh giá</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base66666">
                            <i class="fas fa-users"></i>
                            <p>Quản lý Khách Hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base66666">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('customer.index') }}">
                                        <span class="sub-item">Top khách hàng mua nhiều</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base9">
                            <i class="fas fa-chart-line"></i>
                            <p>Quản lý doanh thu</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base9">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('admin.revenueDay') }}">
                                        <span class="sub-item">Thống kê doanh thu theo ngày</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.revenueMonth') }}">
                                        <span class="sub-item">Thống kê doanh thu theo tháng & Năm</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#base999999">
                            <i class="fas fa-chart-line"></i>
                            <p>Quản lý lợi nhuận</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="base999999">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="javascript:void(0);">
                                        <span class="sub-item">Thống kê lợi nhuận ngày</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.profitYear') }}">
                                        <span class="sub-item">Thống kê lợi nhuận theo tháng và năm</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
