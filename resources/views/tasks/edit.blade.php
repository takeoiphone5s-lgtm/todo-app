@extends('layouts.app')

@section('title', 'タスクの編集')

@section('content')
    <h1 class="page-title">タスクを編集する</h1>

    <div class="card">
        <P>登録日時 {{$task->created_at}}<P><P>最終更新日時 {{$task->updated_at}}<P>
        <form action="{{ route('tasks.update', $task->id) }}" method="post">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ old('name', $task->name) }}">
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        

            <div class="form-actions">
                <button type="submit" class="btn-accent">更新する</button>
                <a href="{{ route('tasks.index') }}" class="btn-link">一覧に戻る</a>
                
                <form action="{{ route('tasks.complete', $task->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-success btn-sm">完了</button>
                </form>
                
            </div>
        </form>
    </div>
@endsection