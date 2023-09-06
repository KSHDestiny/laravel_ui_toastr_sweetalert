<?php

namespace App\Http\Controllers;

use App\Models\TitleList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $list->user_id = Auth::user()->id;
        $list->title = $request->title;
        $list->author = $request->author;
        $list->save();
        return response()->json([
            "status" => "success",
            "action" => "create",
        ]);
    }

    public function fetch(){
        $lists = TitleList::where('user_id',Auth::user()->id)->get();
        logger($lists);
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

    public function search(){
        $lists = TitleList::when((request('searchKey')), function($query){
                            $searchKey = request('searchKey');
                            $query->where(function($query) use ($searchKey){
                                $query->where('title','like','%'.$searchKey."%")
                                ->orWhere('author','like','%'.$searchKey."%");
                            });
                        })
                        ->where('user_id',Auth::user()->id)->get();

        return response()->json([
            "lists" => $lists
        ]);
    }

    private function listValidation($request){
        $request->validate([
            'title' => 'required|max:50',
            'author' => 'required|max:50',
        ]);
    }
}
