<?php

namespace App\Http\Controllers;

use App\Models\Task;  
use Illuminate\Http\Request;

class TaskController extends Controller
{
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

        return view('tasks.index', compact('tasks'));
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
        $validated = $request->validate([
            'name' => 'required|max:100|unique:tasks,name',
        ], [
            'name.required' => 'タスク名を入力してください。',
            'name.max'      => 'タスク名は100文字以内で入力してください。',
            'name.unique' =>'同じ名前で登録できません',
        ]);

        Task::create([
            'name'   => $validated['name'],
            'status' => false,
        ]);

        return redirect()->route('tasks.index');
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
        //
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
        //
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
        $task->status = true;
        $task->save();

        return redirect()->route('tasks.index');
    }

    public function complete($id)
    {
        $task = Task::find($id);
        $task->status = true;
        $task->save();

        return redirect()->route('tasks.index');
    }

public function undo($id)
    {
        $task = Task::findOrFail($id);
        $task->status = false;
        $task->save();

        return redirect()->route('tasks.index');
    }
}
