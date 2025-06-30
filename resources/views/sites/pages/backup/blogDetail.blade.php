@extends('sites.master')
@section('title', 'Chi tiết bài viết')
@section('content')
    <!-- Phần hero blog -->
    <section class="blog-hero spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-9 text-center">
                    <div class="blog__hero__text">
                        <h2>{{ $blogDetail->title }}</h2>
                        <ul>
                            <li>By {{ $blogDetail->staff->name }}</li>
                            <li>{{ $blogDetail->created_at->format('d/m/Y H:i') }}</li>
                            <li>{{ $blogDetail->blogcommentsofblog()->count() }} Bình luận</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Phần nội dung chi tiết -->
    <section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-12">
                    <div class="blog__details__pic">
                        <img src="{{ $blogDetail->image }}" alt="{{ $blogDetail->title }}">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <!-- Nội dung bài viết -->
                        <div class="blog__details__text">
                            {!! $blogDetail->content !!}
                        </div>

                        <!-- Phần bình luận -->
                        <div class="blog__details__recent-comments">
                            <div class="comment-filter-container">
                                <h4>Bình luận (<span
                                        id="commentCount">{{ $blogDetail->blogcommentsofblog()->count() }}</span>)</h4>
                                <div class="comment-filter-options">
                                    <button class="filter-btn active" data-filter="recent">Mới nhất</button>
                                    <button class="filter-btn" data-filter="popular">Nhiều like nhất</button>
                                </div>
                            </div>

                            <!-- Danh sách bình luận -->
                            <div class="comment-list" id="commentList">
                                @foreach ($comments as $comment)
                                    <div class="comment-item" data-comment-id="{{ $comment->id }}"
                                        data-owner="{{ auth('customer')->check() && auth('customer')->id() == $comment->customer_id ? 'true' : 'false' }}"
                                        data-likes="{{ $comment->likecomments->count() }}">
                                        <div class="comment-avatar">
                                            <img src="{{ $comment->customer->avatar ?? 'https://i.pravatar.cc/80?img=' . rand(1, 70) }}"
                                                alt="{{ $comment->customer->name }}">
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-meta">
                                                <span class="comment-author">{{ $comment->customer->name }}</span>
                                                <span
                                                    class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                                                @if ($comment->created_at != $comment->updated_at)
                                                    <span class="comment-edited">(đã chỉnh sửa)</span>
                                                @endif
                                            </div>
                                            <p class="comment-text">{{ $comment->content }}</p>
                                            <div class="comment-actions">
                                                <a href="#"
                                                    class="like-btn {{ $comment->likecomments(auth('customer')->id()) ? 'liked' : '' }}"
                                                    data-comment-id="{{ $comment->id }}">
                                                    <i
                                                        class="fa {{ $comment->likecomments(auth('customer')->id()) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                                    <span class="like-count">{{ $comment->likecomments->count() }}</span>
                                                </a>
                                                <a href="#" class="reply-btn"
                                                    data-comment-id="{{ $comment->id }}">Trả lời</a>
                                                @if (auth('customer')->check() && auth('customer')->id() == $comment->customer_id)
                                                    <a href="#" class="edit-btn"
                                                        data-comment-id="{{ $comment->id }}">Sửa</a>
                                                    <a href="#" class="delete-btn"
                                                        data-comment-id="{{ $comment->id }}">Xóa</a>
                                                @endif
                                            </div>

                                            <!-- Form sửa bình luận (ẩn) -->
                                            <div class="edit-comment-form" style="display: none;">
                                                <textarea class="form-control edit-comment-content">{{ $comment->content }}</textarea>
                                                <button class="btn btn-primary btn-sm save-edit"
                                                    data-comment-id="{{ $comment->id }}">Lưu</button>
                                                <button class="btn btn-secondary btn-sm cancel-edit">Hủy</button>
                                            </div>

                                            <!-- Danh sách reply -->
                                            @if ($comment->blogcomments->count() > 0)
                                                <div class="comment-replies">
                                                    @foreach ($comment->blogcomments as $reply)
                                                        <div class="comment-item" data-comment-id="{{ $reply->id }}"
                                                            data-owner="{{ auth('customer')->check() && auth('customer')->id() == $reply->customer_id ? 'true' : 'false' }}"
                                                            data-likes="{{ $reply->likecomments->count() }}">
                                                            <div class="comment-avatar">
                                                                <img src="{{ $reply->customer->avatar ?? 'https://i.pravatar.cc/80?img=' . rand(1, 70) }}"
                                                                    alt="{{ $reply->customer->name }}">
                                                            </div>
                                                            <div class="comment-content">
                                                                <div class="comment-meta">
                                                                    <span
                                                                        class="comment-author">{{ $reply->customer->name }}</span>
                                                                    <span
                                                                        class="comment-date">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    @if ($reply->created_at != $reply->updated_at)
                                                                        <span class="comment-edited">(đã chỉnh sửa)</span>
                                                                    @endif
                                                                </div>
                                                                <p class="comment-text">{{ $reply->content }}</p>
                                                                <div class="comment-actions">
                                                                    <a href="#"
                                                                        class="like-btn {{ $comment->isLikedBy(auth('customer')->id()) ? 'liked' : '' }}"
                                                                        data-comment-id="{{ $comment->id }}">
                                                                        <i
                                                                            class="fa {{ $comment->isLikedBy(auth('customer')->id()) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                                                        <span
                                                                            class="like-count">{{ $comment->likecomments->count() }}</span>
                                                                    </a>
                                                                    @if (auth('customer')->check() && auth('customer')->id() == $reply->customer_id)
                                                                        <a href="#" class="edit-btn"
                                                                            data-comment-id="{{ $reply->id }}">Sửa</a>
                                                                        <a href="#" class="delete-btn"
                                                                            data-comment-id="{{ $reply->id }}">Xóa</a>
                                                                    @endif
                                                                </div>

                                                                <!-- Form sửa reply (ẩn) -->
                                                                <div class="edit-comment-form" style="display: none;">
                                                                    <textarea class="form-control edit-comment-content">{{ $reply->content }}</textarea>
                                                                    <button class="btn btn-primary btn-sm save-edit"
                                                                        data-comment-id="{{ $reply->id }}">Lưu</button>
                                                                    <button
                                                                        class="btn btn-secondary btn-sm cancel-edit">Hủy</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Nút xem thêm -->
                            @if ($comments->hasMorePages())
                                <div class="load-more-container mb-3">
                                    <button id="loadMoreComments" class="site-btn" data-page="2">Xem thêm bình
                                        luận</button>
                                </div>
                            @endif
                        </div>

                        <!-- Form bình luận -->
                        <div class="blog__details__comment">
                            <h4>Để lại bình luận</h4>
                            @auth('customer')
                                <form id="commentForm">
                                    @csrf
                                    <input type="hidden" id="blogId" value="{{ $blogDetail->id }}">
                                    <input type="hidden" id="parentCommentId" name="parent_id" value="">
                                    <div class="row">
                                        <div class="col-lg-12 text-center">
                                            <textarea id="commentContent" name="content" placeholder="Viết bình luận của bạn..." required></textarea>
                                            <button type="submit" class="site-btn">Gửi bình luận</button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-info">
                                    Vui lòng <a href="{{ route('user.login') }}">đăng nhập</a> để bình luận.
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa bình luận này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* CSS cho phần bình luận */
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
            padding-right: 10px;
        }

        .comment-list.scrollable {
            max-height: 500px;
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

        .comment-date,
        .comment-edited {
            font-size: 13px;
            color: #888;
            margin-right: 10px;
        }

        .comment-text {
            color: #555;
            line-height: 1.5;
            margin: 0;
            margin-bottom: 10px;
            white-space: pre-wrap;
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

        .comment-actions .like-btn.liked i {
            color: #ca1515;
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

        .load-more-container {
            text-align: center;
            margin-top: 20px;
        }

        #loadMoreComments {
            border: none;
            padding: 10px 25px;
            cursor: pointer;
        }

        /* Form chỉnh sửa */
        .edit-comment-form {
            margin-top: 15px;
            display: none;
        }

        .edit-comment-form textarea {
            width: 100%;
            min-height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Form reply */
        .reply-form {
            margin-top: 15px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .reply-form textarea {
            width: 100%;
            min-height: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        /* Scrollbar */
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
        $(document).ready(function() {
            // Biến kiểm tra đăng nhập
            const isAuthenticated = {{ auth('customer')->check() ? 'true' : 'false' }};
            const currentUserId = {{ auth('customer')->id() ?? 'null' }};

            // Xử lý gửi bình luận
            $('#commentForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');

                if (!isAuthenticated) {
                    alert('Vui lòng đăng nhập để bình luận');
                    window.location.href = "{{ route('user.login') }}";
                    return;
                }

                const formData = {
                    content: $('#commentContent').val(),
                    blog_id: $('#blogId').val(),
                    parent_id: $('#parentCommentId').val() || null,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                console.log('Form data:', formData);

                $.ajax({
                    url: "{{ route('blogcomments.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            if (formData.parent_id) {
                                addReplyToComment(response.comment, formData.parent_id);
                            } else {
                                addNewComment(response.comment);
                            }
                            $('#commentContent').val('');
                            $('#parentCommentId').val('');
                            $('.reply-form').remove();
                            updateCommentCount(1);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi gửi bình luận';
                        alert(errorMessage);
                    }
                });
            });

            // Xử lý gửi reply
            $(document).on('submit', '.comment-reply-form', function(e) {
                e.preventDefault();

                if (!isAuthenticated) {
                    alert('Vui lòng đăng nhập để trả lời bình luận');
                    return;
                }

                const form = $(this);
                const content = form.find('textarea[name="content"]').val();
                const parentId = form.find('input[name="parent_id"]').val();

                $.ajax({
                    url: "{{ route('blogcomments.store') }}",
                    method: "POST",
                    data: {
                        content: content,
                        blog_id: $('#blogId').val(),
                        parent_id: parentId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            addReplyToComment(response.comment, parentId);
                            form.closest('.reply-form').remove();
                            updateCommentCount(1);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi gửi trả lời';
                        alert(errorMessage);
                    }
                });
            });

            // Xử lý like bình luận
            $(document).on('click', '.like-btn', function(e) {
                e.preventDefault();

                if (!isAuthenticated) {
                    alert('Vui lòng đăng nhập để like bình luận');
                    window.location.href = "{{ route('user.login') }}";
                    return;
                }

                const commentId = $(this).data('comment-id');
                const $likeBtn = $(this);
                const $likeIcon = $likeBtn.find('i');
                const $likeCount = $likeBtn.find('.like-count');

                $.ajax({
                    url: `blogcomments/${commentId}/toggleLike`,
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Cập nhật trạng thái like
                            $likeBtn.toggleClass('liked', response.isLiked);
                            $likeCount.text(response.likeCount);

                            // Đổi icon
                            if (response.isLiked) {
                                $likeIcon.removeClass('fa-heart-o').addClass('fa-heart');
                            } else {
                                $likeIcon.removeClass('fa-heart').addClass('fa-heart-o');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi like bình luận';
                        alert(errorMessage);
                    }
                });
            });

            // Xử lý reply bình luận
            $(document).on('click', '.reply-btn', function(e) {
                e.preventDefault();

                if (!isAuthenticated) {
                    alert('Vui lòng đăng nhập để trả lời bình luận');
                    window.location.href = "{{ route('user.login') }}";
                    return;
                }

                const commentId = $(this).data('comment-id');
                $('.reply-form').remove();

                const replyForm = `
            <div class="reply-form">
                <form class="comment-reply-form">
                    <textarea name="content" placeholder="Viết trả lời của bạn..." required></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Gửi</button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-reply">Hủy</button>
                    <input type="hidden" name="parent_id" value="${commentId}">
                </form>
            </div>
        `;

                $(this).closest('.comment-actions').after(replyForm);
            });

            // Hủy reply
            $(document).on('click', '.cancel-reply', function() {
                $(this).closest('.reply-form').remove();
            });

            // Hiển thị form sửa bình luận
            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                const commentId = $(this).data('comment-id');
                const $commentItem = $(this).closest('.comment-item');
                const commentText = $commentItem.find('.comment-text').text();

                // Ẩn nội dung bình luận và hiển thị form sửa
                $commentItem.find('.comment-text').hide();
                $commentItem.find('.comment-actions').hide();
                $commentItem.find('.edit-comment-form').show().find('textarea').val(commentText);
            });

            // Hủy sửa bình luận
            $(document).on('click', '.cancel-edit', function() {
                const $commentItem = $(this).closest('.comment-item');
                $commentItem.find('.edit-comment-form').hide();
                $commentItem.find('.comment-text').show();
                $commentItem.find('.comment-actions').show();
            });

            // Lưu bình luận sau khi sửa
            $(document).on('click', '.save-edit', function() {
                const commentId = $(this).data('comment-id');
                const newContent = $(this).closest('.edit-comment-form').find('textarea').val();

                $.ajax({
                    url: `/blogcomments/${commentId}`,
                    method: "PUT",
                    data: {
                        content: newContent,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Cập nhật nội dung bình luận
                            const $commentItem = $(`[data-comment-id="${commentId}"]`);
                            $commentItem.find('.comment-text').text(newContent).show();
                            $commentItem.find('.comment-meta .comment-edited').remove();
                            $commentItem.find('.comment-meta').append(
                                '<span class="comment-edited">(đã chỉnh sửa)</span>');
                            $commentItem.find('.edit-comment-form').hide();
                            $commentItem.find('.comment-actions').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi sửa bình luận';
                        alert(errorMessage);
                    }
                });
            });

            // Xử lý xóa bình luận
            let commentToDelete = null;

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                commentToDelete = $(this).data('comment-id');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                if (!commentToDelete) return;

                $.ajax({
                    url: `/blogcomments/${commentToDelete}`,
                    method: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Xóa bình luận khỏi DOM
                            $(`[data-comment-id="${commentToDelete}"]`).remove();
                            $('#confirmDeleteModal').modal('hide');

                            // Cập nhật số lượng bình luận
                            updateCommentCount(-1);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi xóa bình luận';
                        alert(errorMessage);
                    }
                });
            });

            // Xử lý tải thêm bình luận
            $('#loadMoreComments').on('click', function() {
                const page = $(this).data('page');
                const blogId = $('#blogId').val();

                $.ajax({
                    url: `/blogcomments/blog/${blogId}?page=${page}`,
                    method: "GET",
                    success: function(response) {
                        if (response.success && response.comments.data.length > 0) {
                            // Thêm bình luận mới vào danh sách
                            response.comments.data.forEach(comment => {
                                $('#commentList').append(createCommentHtml(comment));
                            });

                            // Cập nhật trang tiếp theo
                            if (response.comments.next_page_url) {
                                $('#loadMoreComments').data('page', page + 1);
                            } else {
                                $('#loadMoreComments').remove();
                            }
                        } else {
                            $('#loadMoreComments').remove();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Có lỗi xảy ra khi tải thêm bình luận');
                    }
                });
            });

            // Lọc bình luận
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                const filterType = $(this).data('filter');
                const blogId = $('#blogId').val();

                $.ajax({
                    url: `/blogcomments/blog/${blogId}`,
                    method: "GET",
                    data: {
                        filter: filterType
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#commentList').empty();
                            response.comments.forEach(comment => {
                                $('#commentList').append(createCommentHtml(comment));
                            });

                            // Ẩn nút xem thêm khi lọc
                            $('#loadMoreComments').remove();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Có lỗi xảy ra khi lọc bình luận');
                    }
                });
            });

            // Hàm thêm bình luận mới vào đầu danh sách
            function addNewComment(comment) {
                $('#commentList').prepend(createCommentHtml(comment));
            }

            // Hàm thêm reply vào bình luận cha
            function addReplyToComment(reply, parentId) {
                const $parentComment = $(`[data-comment-id="${parentId}"]`);
                let $repliesContainer = $parentComment.find('.comment-replies');

                if ($repliesContainer.length === 0) {
                    $repliesContainer = $('<div class="comment-replies"></div>');
                    $parentComment.find('.comment-content').append($repliesContainer);
                }

                $repliesContainer.append(createCommentHtml(reply, true));
            }

            // Hàm tạo HTML cho bình luận
            function createCommentHtml(comment, isReply = false) {
                const isOwner = currentUserId !== null && currentUserId === comment.customer_id;
                const isLiked = comment.is_liked || false;
                const likeCount = comment.likecomments_count || (comment.likecomments ? comment.likecomments
                    .length : 0);

                return `
            <div class="comment-item" data-comment-id="${comment.id}" data-owner="${isOwner}" data-likes="${likeCount}">
                <div class="comment-avatar">
                    <img src="${comment.customer.avatar || 'https://i.pravatar.cc/80?img=' + Math.floor(Math.random() * 70) + 1}" alt="${comment.customer.name}">
                </div>
                <div class="comment-content">
                    <div class="comment-meta">
                        <span class="comment-author">${comment.customer.name}</span>
                        <span class="comment-date">${timeAgo(comment.created_at)}</span>
                        ${comment.created_at !== comment.updated_at ? '<span class="comment-edited">(đã chỉnh sửa)</span>' : ''}
                    </div>
                    <p class="comment-text">${comment.content}</p>
                    <div class="comment-actions">
                        <a href="#" class="like-btn ${isLiked ? 'liked' : ''}" data-comment-id="${comment.id}">
                            <i class="fa ${isLiked ? 'fa-heart' : 'fa-heart-o'}"></i>
                            <span class="like-count">${likeCount}</span>
                        </a>
                        ${!isReply ? `<a href="#" class="reply-btn" data-comment-id="${comment.id}">Trả lời</a>` : ''}
                        ${isOwner ? `
                                <a href="#" class="edit-btn" data-comment-id="${comment.id}">Sửa</a>
                                <a href="#" class="delete-btn" data-comment-id="${comment.id}">Xóa</a>
                            ` : ''}
                    </div>

                    <!-- Form sửa bình luận (ẩn) -->
                    <div class="edit-comment-form" style="display: none;">
                        <textarea class="form-control edit-comment-content">${comment.content}</textarea>
                        <button class="btn btn-primary btn-sm save-edit" data-comment-id="${comment.id}">Lưu</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Hủy</button>
                    </div>

                    ${comment.blogcomments && comment.blogcomments.length > 0 ? `
                            <div class="comment-replies">
                                ${comment.blogcomments.map(reply => createCommentHtml(reply, true)).join('')}
                            </div>
                        ` : ''}
                </div>
            </div>
        `;
            }

            // Hàm cập nhật số lượng bình luận
            function updateCommentCount(change) {
                const $commentCount = $('#commentCount');
                const currentCount = parseInt($commentCount.text()) || 0;
                $commentCount.text(currentCount + change);
            }

            // Hàm hiển thị thời gian dạng "x phút trước"
            function timeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);

                let interval = Math.floor(seconds / 31536000);
                if (interval >= 1) return interval + " năm trước";

                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) return interval + " tháng trước";

                interval = Math.floor(seconds / 86400);
                if (interval >= 1) return interval + " ngày trước";

                interval = Math.floor(seconds / 3600);
                if (interval >= 1) return interval + " giờ trước";

                interval = Math.floor(seconds / 60);
                if (interval >= 1) return interval + " phút trước";

                return "Vừa xong";
            }
        });
    </script>
@endsection
