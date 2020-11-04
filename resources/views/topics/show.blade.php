@extends('layouts.app')
@section('title', $topic->title)
@section('description', $topic->excerpt)
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/atwho.min.css') }}">
@stop
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-3 hidden-sm hidden-xs author-info">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    作者：{{ $topic->user->name }}
                </div>
                <hr>
                <div class="media">
                    <div class="center">
                        <a href="{{ route('users.show', $topic->user->id) }}">
                            <img src="{{ $topic->user->avatar }}" alt="{{ $topic->user->name }}" class="thumbnail img-fluid">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 topic-content">
        <div class="card">
            <div class="card-body">
                <h1 class="text-center mt-3 mb-3">
                    {{ $topic->title }}
                </h1>
                <div class="article-meta text-center text-secondary">
                    {{ $topic->created_at->diffForHumans() }}
                    ⋅
                    <i class="far fa-comment"></i>
                    {{ $topic->reply_count }}
                </div>
                <div class="topic-body mt-4 mb-4">
                    {!! $topic->body !!}
                </div>
                @can('update', $topic)
                <div class="operate">
                    <hr>
                    <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-outline-secondary btn-sm" role="button">
                        <i class="far fa-edit"></i>
                        编辑
                    </a>
                    <form action="{{ route('topics.destroy', $topic->id) }}" onsubmit="confirm('确定删除吗？')" method="POST" class="d-inline-block" role="button">
                        @csrf
                        {{ method_field('DELETE') }}
                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                            <i class="far fa-trash-alt"></i> 删除
                        </button>
                    </form>
                </div>
                @endcan
            </div>
        </div>

{{--        用户回复列表--}}
        <div class="card topic-reply mt-4">
            <div class="card-body">
                @includeWhen(Auth::check(), 'topics._reply_box', ['topic' => $topic])
                @include('topics._reply_list', ['replies' => $topic->replies()->with('user')->get()])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    {{-- at someone --}}
    <script type="text/javascript" src="{{ asset('js/atwho.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/create.min.js') }}"></script>
    <script>
        $('#reply_box').atwho({
            at: "@",
            callbacks: {
                remoteFilter: function(query, callback) {
                    $.getJSON("/atusers", {q: query}, function(data) {
                        callback(data)
                    });
                }
            }
        });
        {{--var at_config = {--}}
        {{--    at: "@",--}}
        {{--    data: '{{ asset('/atusers') }}',--}}
        {{--}--}}
        {{--$('#reply_box').atwho(at_config);--}}
    </script>
@stop
