<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;  
use App\Http\Requests\TaskRequest;  
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {

        $tasks = Task::where('status', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $doneTasks = Task::where('status', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('tasks.index', compact('tasks', 'doneTasks'));
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
      public function store(TaskRequest $request): RedirectResponse
    {
        Task::create([
            'name'   => $request->validated('name'),
            'status' => false,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('message', 'タスクを追加しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));//
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $task->name = $request->validated('name');
        $task->save();

        return redirect()
            ->route('tasks.index')
            ->with('message', 'タスクを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
        ->route('tasks.index')
        ->with('message2', 'タスクを削除しました。');
    }

    public function complete(Task $task): RedirectResponse
    {
        $task->status = true;
        $task->save();

       return redirect()
        ->route('tasks.index','')
        ->with('message', 'タスクを完了にしました。');

    }

public function undo(Task $task)
    {
        $task->status = false;
        $task->save();

        return redirect()->route('tasks.index');
    }
}
