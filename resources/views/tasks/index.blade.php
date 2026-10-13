@extends('layouts.app')

@section('title', 'タスク一覧')

@section('content')
    <h1 class="page-title">今日は何をする？</h1>

    {{-- 追加フォーム --}}
    <div class="card">
        <form action="{{ route('tasks.store') }}" method="post">
            @csrf
            <div class="form-row">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="洗濯物をたたむ...">
                <button type="submit">追加する</button>
            </div>
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </form>
    </div>

    {{-- 未完了タスク一覧 --}}
    <table>
        <thead>
            <tr>
                <th>タスク</th>
                <th>登録日時</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->name }}</td>
                    <td class="muted">{{ $task->created_at->format('Y/m/d H:i') }}</td>
                    <td class="actions">
                        <form action="{{ route('tasks.complete', $task->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-success btn-sm">完了</button>
                        </form>
                        <form action="{{ route('tasks.undo', $task->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-success btn-sm">未完了に戻る</button>
                        </form>
                    </td>
                </tr>
               
            @endforeach
        </tbody>
    </table>
@endsection