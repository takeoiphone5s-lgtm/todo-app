<?php

namespace App\Http\Controllers;

use App\Models\Task;  
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private array $rules = [
        'name' => 'required|max:100',
    ];

    private array $messages = [
        'name.required' => 'タスク名を入力してください。',
        'name.max'      => 'タスク名は100文字以内で入力してください。',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules, $this->messages);

        Task::create([
            'name'   => $validated['name'],
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
    public function edit($id)
    {
         $task = Task::findOrFail($id);

        return view('tasks.edit', compact('task'));//
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $validated = $request->validate($this->rules, $this->messages);
        $task = Task::findOrFail($id);
        $task->name = $validated['name'];
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
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()
        ->route('tasks.index')
        ->with('message2', 'タスクを削除しました。');
    }

    public function complete($id)
    {
        $task = Task::find($id);
        $task->status = true;
        $task->save();

       return redirect()
        ->route('tasks.index')
        ->with('message', 'タスクを完了にしました。');

    }

public function undo($id)
    {
        $task = Task::findOrFail($id);
        $task->status = false;
        $task->save();

        return redirect()->route('tasks.index');
    }
}
