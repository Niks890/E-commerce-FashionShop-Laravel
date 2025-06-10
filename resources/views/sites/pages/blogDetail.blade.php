@extends('sites.master')
@section('title', 'Chi tiết bài viết')
@section('content')
    <section class="blog-hero spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-9 text-center">
                    <div class="blog__hero__text">
                        <h2>{{ $blogDetail->title }}</h2>
                        <ul>
                            <li>By {{ $blogDetail->staff->name }}</li>
                            <li>{{ $blogDetail->created_at }}</li>
                            <li>8 Bình luận</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-12">
                    <div class="blog__details__pic">
                        <img src="{{ $blogDetail->image }}" alt="">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <div class="blog__details__share">
                            <span>Chia sẻ</span>
                            <ul>
                                <li><a href="https://www.facebook.com/?locale=vi_VN"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="https://x.com/?lang=vi" class="twitter"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="https://www.youtube.com/?app=desktop&hl=vi" class="youtube"><i
                                            class="fa fa-youtube-play"></i></a></li>
                                <li><a href="https://www.linkedin.com/" class="linkedin"><i class="fa fa-linkedin"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="blog__details__text">
                            {!! $blogDetail->content !!}
                        </div>
                        <div class="blog__details__quote">
                            <i class="fa fa-quote-left"></i>
                            <p>“He he he, ha ha ha”</p>
                            <h6>_ {{ $blogDetail->staff->name }} _</h6>
                        </div>
                        <div class="blog__details__text">
                            <p>{!! $blogDetail->content !!}</p>
                        </div>
                        <div class="blog__details__option">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="blog__details__author">
                                        <div class="blog__details__author__pic">
                                            <img src="{{ $blogDetail->staff->avatar }}" alt="">
                                        </div>
                                        <div class="blog__details__author__text">
                                            <h5>{{ $blogDetail->staff->name }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="blog__details__tags">
                                        @php
                                            $tags = explode(',', $blogDetail->tags);
                                        @endphp
                                        @foreach ($tags as $tag)
                                            <a href="javascript:void(0);">#{{ $tag }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="blog__details__btns">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    @if ($previousBlog)
                                        <a href="{{ route('sites.blogDetail', $previousBlog->slug) }}"
                                            class="blog__details__btns__item">
                                            <p><span class="arrow_left"></span>Tin trước đó</p>
                                            <h5>{{ $previousBlog->title }}</h5>
                                        </a>
                                    @else
                                        <a href="javascript:void(0);" class="blog__details__btns__item">
                                            <p><span class="arrow_left"></span>Không có bài viết trước đó</p>
                                            <h5></h5>
                                        </a>
                                    @endif
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    @if ($nextBlog)
                                        <a href="{{ route('sites.blogDetail', $nextBlog->slug) }}"
                                            class="blog__details__btns__item blog__details__btns__item--next">
                                            <p>Tin tiếp theo<span class="arrow_right"></span></p>
                                            <h5>{{ $nextBlog->title }}</h5>
                                        </a>
                                    @else
                                        <a href="javascript:void(0);"
                                            class="blog__details__btns__item blog__details__btns__item--next">
                                            <p>Không có bài viết tiếp theo<span class="arrow_right"></span></p>
                                            <h5></h5>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="blog__details__recent-comments">
                            <div class="comment-filter-container">
                                <h4>Bình luận (8)</h4>
                                <div class="comment-filter-options">
                                    <button class="filter-btn active" data-filter="recent">Gần đây nhất</button>
                                    <button class="filter-btn" data-filter="popular">Nhiều lượt tương tác nhất</button>
                                    <button class="filter-btn" data-filter="relevant">Phù hợp nhất</button>
                                </div>
                            </div>


                            <div class="comment-list">
                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=11" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Trần Văn An</span>
                                            <span class="comment-date">2 giờ trước</span>
                                        </div>
                                        <p class="comment-text">Bài viết rất hữu ích, cảm ơn tác giả đã chia sẻ kiến
                                            thức này!</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích (12)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=12" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Nguyễn Thị Bình</span>
                                            <span class="comment-date">5 giờ trước</span>
                                        </div>
                                        <p class="comment-text">Mình đã thử áp dụng và thấy hiệu quả rõ rệt chỉ sau 3
                                            ngày.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích (8)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>

                                        <div class="comment-replies">
                                            <div class="comment-item">
                                                <div class="comment-avatar">
                                                    <img src="https://i.pravatar.cc/80?img=1" alt="Avatar">
                                                </div>
                                                <div class="comment-content">
                                                    <div class="comment-meta">
                                                        <span class="comment-author">{{ $blogDetail->staff->name }}</span>
                                                        <span class="comment-date">4 giờ trước</span>
                                                    </div>
                                                    <p class="comment-text">Cảm ơn bạn đã chia sẻ trải nghiệm. Rất vui
                                                        vì bài viết đã giúp ích cho bạn!</p>
                                                    <div class="comment-actions">
                                                        <a href="#" class="like-btn"><i class="fa fa-heart-o"></i>
                                                            Thích (5)</a>
                                                        <a href="#" class="reply-btn">Trả lời</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=13" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Lê Hoàng C</span>
                                            <span class="comment-date">Hôm qua</span>
                                        </div>
                                        <p class="comment-text">Có cách nào để tối ưu hơn nữa không ạ? Mình muốn biết
                                            thêm chi tiết.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích
                                                (3)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=13" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Lê Hoàng C</span>
                                            <span class="comment-date">Hôm qua</span>
                                        </div>
                                        <p class="comment-text">Có cách nào để tối ưu hơn nữa không ạ? Mình muốn biết
                                            thêm chi tiết.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích
                                                (3)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=13" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Lê Hoàng C</span>
                                            <span class="comment-date">Hôm qua</span>
                                        </div>
                                        <p class="comment-text">Có cách nào để tối ưu hơn nữa không ạ? Mình muốn biết
                                            thêm chi tiết.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích
                                                (3)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=13" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Lê Hoàng C</span>
                                            <span class="comment-date">Hôm qua</span>
                                        </div>
                                        <p class="comment-text">Có cách nào để tối ưu hơn nữa không ạ? Mình muốn biết
                                            thêm chi tiết.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích
                                                (3)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar">
                                        <img src="https://i.pravatar.cc/80?img=13" alt="Avatar">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-meta">
                                            <span class="comment-author">Lê Hoàng C</span>
                                            <span class="comment-date">Hôm qua</span>
                                        </div>
                                        <p class="comment-text">Có cách nào để tối ưu hơn nữa không ạ? Mình muốn biết
                                            thêm chi tiết.</p>
                                        <div class="comment-actions">
                                            <a href="#" class="like-btn"><i class="fa fa-heart-o"></i> Thích
                                                (3)</a>
                                            <a href="#" class="reply-btn">Trả lời</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar"><img src="https://i.pravatar.cc/80?img=20"
                                            alt="Avatar"></div>
                                    <div class="comment-content">
                                        <div class="comment-meta"><span class="comment-author">Khách 8</span><span
                                                class="comment-date">6 ngày trước</span></div>
                                        <p class="comment-text">Bình luận thứ 8.</p>
                                        <div class="comment-actions"><a href="#" class="like-btn"><i
                                                    class="fa fa-heart-o"></i> Thích</a><a href="#"
                                                class="reply-btn">Trả lời</a></div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar"><img src="https://i.pravatar.cc/80?img=21"
                                            alt="Avatar"></div>
                                    <div class="comment-content">
                                        <div class="comment-meta"><span class="comment-author">Khách 9</span><span
                                                class="comment-date">7 ngày trước</span></div>
                                        <p class="comment-text">Bình luận thứ 9, dài hơn một chút để kiểm tra hiển thị.</p>
                                        <div class="comment-actions"><a href="#" class="like-btn"><i
                                                    class="fa fa-heart-o"></i> Thích</a><a href="#"
                                                class="reply-btn">Trả lời</a></div>
                                    </div>
                                </div>
                                <div class="comment-item">
                                    <div class="comment-avatar"><img src="https://i.pravatar.cc/80?img=22"
                                            alt="Avatar"></div>
                                    <div class="comment-content">
                                        <div class="comment-meta"><span class="comment-author">Khách 10</span><span
                                                class="comment-date">8 ngày trước</span></div>
                                        <p class="comment-text">Bình luận cuối cùng.</p>
                                        <div class="comment-actions"><a href="#" class="like-btn"><i
                                                    class="fa fa-heart-o"></i> Thích</a><a href="#"
                                                class="reply-btn">Trả lời</a></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="load-more-container mb-3">
                            <button id="loadMoreComments" class="site-btn">Xem thêm bình luận</button>
                        </div>
                    </div>


                    <div class="blog__details__comment">
                        <h4>Để lại đánh giá</h4>
                        <form action="#">
                            <div class="row">
                                <div class="col-lg-4 col-md-4">
                                    <input type="text" placeholder="Name">
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <input type="text" placeholder="Email">
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <input type="text" placeholder="Phone">
                                </div>
                                <div class="col-lg-12 text-center">
                                    <textarea placeholder="Comment"></textarea>
                                    <button type="submit" class="site-btn">Gửi đánh giá</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('css')
    <style>
        /* Thêm CSS cho bộ lọc */
        .comment-filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e1e1e1;
            flex-wrap: wrap;
        }

        .comment-filter-container h4 {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .comment-filter-options {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: #f8f8f8;
            color: #555;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background: #eee;
        }

        .filter-btn.active {
            background: #ca1515;
            color: white;
            border-color: #ca1515;
        }

        /* Style cho phần bình luận gần đây */
        .blog__details__recent-comments {
            margin-bottom: 50px;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .blog__details__recent-comments h4 {
            color: #1c1c1c;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e1e1e1;
        }

        .comment-list {
            /* Bỏ max-height và overflow-y ở đây */
            padding-right: 10px;
        }

        /* Class mới để kích hoạt thanh cuộn */
        .comment-list.scrollable {
            max-height: 500px;
            /* Chiều cao tối đa trước khi thanh cuộn xuất hiện */
            overflow-y: auto;
        }

        .comment-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #e1e1e1;
        }

        .comment-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .comment-avatar {
            margin-right: 15px;
        }

        .comment-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-content {
            flex: 1;
        }

        .comment-meta {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
            margin-right: 10px;
        }

        .comment-date {
            font-size: 13px;
            color: #888;
        }

        .comment-text {
            color: #555;
            line-height: 1.5;
            margin: 0;
            margin-bottom: 10px;
        }

        .comment-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
        }

        .comment-actions a {
            color: #555;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .comment-actions a:hover {
            color: #ca1515;
        }

        .comment-actions .like-btn i {
            margin-right: 5px;
        }

        .comment-replies {
            margin-top: 20px;
            padding-left: 25px;
            border-left: 2px solid #e9e9e9;
        }

        .comment-replies .comment-item {
            padding-bottom: 0;
            border-bottom: none;
            margin-bottom: 0;
        }

        /* --- CSS MỚI CHO NÚT XEM THÊM --- */
        .load-more-container {
            text-align: center;
            margin-top: 20px;
        }

        #loadMoreComments {
            display: none;
            /* Ẩn nút ban đầu */
            border: none;
            padding: 10px 25px;
            cursor: pointer;
        }

        /* Scrollbar style */
        .comment-list.scrollable::-webkit-scrollbar {
            width: 5px;
        }

        .comment-list.scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .comment-list.scrollable::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .comment-list.scrollable::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection

@section('js')
    <script>
        // Đảm bảo mã chạy sau khi trang đã tải xong
        $(document).ready(function() {
            const maxComments = 7; // Số lượng bình luận hiển thị tối đa ban đầu
            const $commentList = $('.comment-list');
            // Chỉ chọn các bình luận cấp cao nhất, không tính các bình luận trả lời
            const $commentItems = $commentList.children('.comment-item');
            const $loadMoreBtn = $('#loadMoreComments');

            // Kiểm tra xem số lượng bình luận có vượt quá giới hạn không
            if ($commentItems.length > maxComments) {
                // Ẩn tất cả các bình luận từ vị trí 'maxComments' trở đi
                $commentItems.slice(maxComments).hide();

                // Hiển thị nút "Xem thêm"
                $loadMoreBtn.show();
            }

            // Xử lý sự kiện khi nhấn nút "Xem thêm"
            $loadMoreBtn.on('click', function(e) {
                e.preventDefault(); // Ngăn hành vi mặc định của nút

                // Hiển thị tất cả các bình luận đã ẩn
                $commentItems.slice(maxComments).slideDown(); // Dùng slideDown() để có hiệu ứng mượt mà

                // Ẩn nút "Xem thêm" đi
                $(this).hide();

                // Thêm class 'scrollable' để kích hoạt thanh cuộn nếu danh sách quá dài
                $commentList.addClass('scrollable');
            });


            $('.filter-btn').on('click', function() {
                // Xóa class active khỏi tất cả các nút
                $('.filter-btn').removeClass('active');
                // Thêm class active vào nút được click
                $(this).addClass('active');

                const filterType = $(this).data('filter');

                // Ẩn tất cả bình luận trước khi sắp xếp lại
                $('.comment-item').hide();

                // Sắp xếp lại bình luận theo loại lọc
                switch (filterType) {
                    case 'recent':
                        // Mặc định đã sắp xếp theo thời gian (mới nhất trước)
                        $('.comment-item').show();
                        break;

                    case 'popular':
                        // Sắp xếp theo số lượt thích (giả sử có data-likes attribute)
                        $('.comment-item').sort(function(a, b) {
                            return $(b).data('likes') - $(a).data('likes');
                        }).appendTo('.comment-list').show();
                        break;

                    case 'relevant':
                        // Sắp xếp theo mức độ phù hợp (có thể kết hợp nhiều yếu tố)
                        $('.comment-item').sort(function(a, b) {
                            // Giả sử tính điểm phù hợp dựa trên lượt thích và thời gian
                            const aScore = $(a).data('likes') * 0.7 + $(a).data('relevance') * 0.3;
                            const bScore = $(b).data('likes') * 0.7 + $(b).data('relevance') * 0.3;
                            return bScore - aScore;
                        }).appendTo('.comment-list').show();
                        break;
                }

                // Reset hiển thị xem thêm nếu cần
                if ($('.comment-item').length > maxComments) {
                    $commentItems.slice(maxComments).hide();
                    $loadMoreBtn.show();
                } else {
                    $loadMoreBtn.hide();
                }
            });
        });
    </script>
@endsection
