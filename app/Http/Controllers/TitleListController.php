<?php

namespace App\Http\Controllers;

use App\Models\TitleList;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class TitleListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function store(Request $request){

        $this->listValidation($request);

        $list = new TitleList();
        $list->title = $request->title;
        $list->author = $request->author;
        $list->save();
        return response()->json([
            "status" => "success",
            "action" => "create",
        ]);
    }

    public function fetch(){
        $lists = TitleList::all();
        return response()->json([
            "lists" => $lists
        ]);
    }

    public function destroy(Request $request){
        $list = TitleList::find($request->id);
        $list->delete();
        return response()->json([
            "status" => "success",
        ]);
    }

    public function edit(Request $request){
        $data = TitleList::find($request->id);
        return response()->json([
            "data" => $data
        ]);
    }

    public function update(Request $request){
        $this->listValidation($request);

        $list = TitleList::find($request->id);
        $list->title = $request->title;
        $list->author = $request->author;
        $list->save();
        return response()->json([
            "status" => "success",
            "action" => "update",
        ]);
    }

    private function listValidation($request){
        $request->validate([
            'title' => 'required|max:50',
            'author' => 'required|max:50',
        ]);
    }
}
