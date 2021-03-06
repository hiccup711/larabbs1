@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/simditor.css') }}">
@stop
@section('content')

<div class="container">
  <div class="col-md-10 offset-md-1">
    <div class="card ">
      <div class="card-body">
          <h2>
              <i class="far far-edit"></i>
              @if($topic->id)
                  编辑话题
              @else
                  新建话题
              @endif
          </h2>
          <hr>
        @if($topic->id)
          <form action="{{ route('topics.update', $topic->id) }}" method="POST" accept-charset="UTF-8">
          <input type="hidden" name="_method" value="PUT">
        @else
          <form action="{{ route('topics.store') }}" method="POST" accept-charset="UTF-8">
        @endif

          @include('shared._error')
          @csrf
              <div class="form-group">
                  <input class="form-control" type="text" name="title" value="{{ old('title', $topic->title ) }}" placeholder="请填写标题" required />
              </div>
              <div class="form-group">
                  <select name="category_id" required class="form-control">
                      @if( ! $topic->category_id)
                          <option value="" hidden disabled selected>请选择分类</option>
                      @endif
                      @foreach($categories as $category)
                              <option value="{{ $category->id }}" {{ $category->id == $topic->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group"><textarea name="body" id="editor" rows="6" placeholder="请至少输入3个字符"
                                                class="form-control" required>{{ old('body', $topic->body) }}</textarea></div>
              <div class="well well-sm">
                  <button type="submit" class="btn btn-primary"><i class="far fa-save mr-2" aria-hidden="true"></i> 保存</button>
              </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
{{--    editor --}}
<script type="text/javascript" src="{{ asset('js/module.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/hotkeys.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/uploader.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/simditor.min.js') }}"></script>
<script>
    $(document).ready(function (){
        var editor = new Simditor({
            'textarea':$("#editor"),
            'upload': {
                url: '{{ route('topics.upload_image') }}',
                params: {
                    _token: '{{ csrf_token() }}'
                },
                fileKey: 'upload_file',
                connectionCount: 3,
                leaveConfirm: "文件上传中，关闭页面将取消上传。"
            },
            pasteImage: true
        })
    });
</script>
@stop
