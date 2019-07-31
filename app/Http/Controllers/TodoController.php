<?php

namespace App\Http\Controllers;

use App\Category;
use App\Todo;
use Illuminate\Http\Request;
use Validator;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'todo' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Lütfen gerekli alanları doldurunuz.'], 400);
        }
        $input = $request->all();
        $input['category_id'] = 1;
        $input['ip'] = request()->ip();
        $todo = Todo::create($input);
        if ($todo->save()) {
            return response()->json(['message' => 'Todo başarıyla eklendi.'], 200);
        } else {
            return response()->json(['message' => 'Todo eklenirken bir sorun oluştu.'], 400);
        }
    }

    public function index()
    {
        $ip = request()->ip();
        $todos = Todo::select('id', 'todo', 'category_id')->where('ip', $ip)->get();
        $pending = array();
        $doing = array();
        $done = array();
        foreach ($todos as $item) {
            $obj = (object)array(
                'id' => $item->id,
                'todo' => $item->todo
            );
            if ($item->category_id == 1) array_push($pending, $obj);
            else if ($item->category_id == 2) array_push($doing, $obj);
            else array_push($done, $obj);
        }
        $response['pendings'] = $pending;
        $response['inProgress'] = $doing;
        $response['done'] = $done;
        return response()->json($response, 200);
    }

    public function show($id)
    {
        $todo = Todo::find($id);
        return response()->json($todo, 200);
    }

    public function update(Request $request)
    {
        $ip = request()->ip();
        $todos = Todo::where('ip', $ip)->get();
        foreach ($todos as $item) {
            $item->delete();
        }

        $input = $request->all();

        $pendings = $input['pendings'];
        $inProgress = $input['inProgress'];
        $done = $input['done'];
        foreach ($pendings as $item) {
            $todo = new Todo;
            $todo->ip = $ip;
            $todo->todo = $item['todo'];
            $todo->category_id = 1;
            $todo->save();
        }
        foreach ($inProgress as $item) {
            $todo = new Todo;
            $todo->ip = $ip;
            $todo->todo = $item['todo'];
            $todo->category_id = 2;
            $todo->save();
        }
        foreach ($done as $item) {
            $todo = new Todo;
            $todo->ip = $ip;
            $todo->todo = $item['todo'];
            $todo->category_id = 3;
            $todo->save();
        }
        return response()->json(['message' => 'Todo başarıyla düzenlendi.'], 200);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        if ($todo->delete()) {
            return response()->json(['message' => 'Todo başarıyla silindi.'], 200);
        } else {
            return response()->json(['message' => 'Todo silinirken bir sorun oluştu.'], 400);
        }
    }

    public function category()
    {
        $categories = Category::select('id', 'name', 'slug')->get();
        $items = array();
        array_push($items, 'yapılacak test');
        foreach ($categories as $item) {
            $item['items'] = $items;
        }
        return response()->json($categories, 200);
    }
}
