@extends('sites.master')
@section('title', 'Bài viết')
@section('content')

<!-- Breadcrumb Section Begin -->
 <section class="breadcrumb-blog set-bg" data-setbg="{{ ('client/img/breadcrumb-bg.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Blog</h2>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Blog Section Begin -->
<section class="blog spad">
    <div class="container">
        <div class="row">
            @foreach ($data as $model)
            <div class="col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('sites.blogDetail', $model->slug) }}">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="{{ $model->image }}"></div>
                    <div class="blog__item__text">
                        <span><img src="{{ asset('client/img/icon/calendar.png') }}" alt="">{{$model->created_at}}</span>
                        <h5>{{$model->title}}</h5>
                          <a href="{{ route('sites.blogDetail', $model->slug)}}">Đọc thêm</a>
                    </div>
                </div>
                 </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Blog Section End -->

@endsection
