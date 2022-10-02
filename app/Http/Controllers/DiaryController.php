<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DiaryController extends Controller
{
    public function index(Request $request)
    {
        $diary = Diary::where('archive_status', 'No')->get();
        if($request->ajax()){
            return DataTables::of($diary)
            ->addIndexColumn()
            ->addColumn('action', function($data) {
                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="btn btn-primary btn-sm editDiary"><i class="fa fa-edit"> Edit </i></a>';
                $btn = $btn. '&nbsp<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="btn btn-info btn-sm archiveDiary"><i class="fa fa-archive"> Archive </i></a>';
                $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="btn btn-danger btn-sm deleteDiary"> <i class="fa fa-edit"> Delete </i></a>';
                return $btn;

            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('home', compact('diaries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'notes' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>400, 'errors'=>$validator->errors()]);
        }
        else{
            $getBy = Auth::user()->name;
            $getUtc = Carbon::now();

            $data = new Diary();
            $data -> title = $request->input('title');
            $data -> notes = $request->input('notes');
            $data -> archive_status = "No";
            $data -> createdBy = $getBy;
            $data -> createdUtc = $getUtc;
            $data -> save();

            return response()->json(['status' => 200, 'message' => 'Diary telah disimpan']);
        }
    }
    
    public function edit($id)
    {
        $diary = Diary::where('id', $id)->first();

        if($diary)
        {
            return response()->json(['status' => 200, 'diary' => $diary]);
        }
        else
        {
            return response()->json(['status' => 404, 'messages' => 'Tidak ada data ditemukan']);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'notes' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }
        else
        {
            $getBy = Auth::user()->name;
            $getUtc = Carbon::now();
            $diary = Diary::find($id);

            if ($diary)
            {
                $diary -> title = $request->input('title');
                $diary -> notes = $request->input('notes');
                $diary -> createdBy = $getBy;
                $diary -> createdUtc = $getUtc;
                $diary -> update();

                return response()->json(['status' => 200, 'messages' => 'Diary telah diperbaharui']);
            }
            else
            {
                return response()->json(['status' => 404, 'messages' => 'Ada kesalahan dalam penyimpanan']);
            }
        }
    }

    public function archive_update(Request $request, $id)
    {
        $getBy = Auth::user()->name;
        $getUtc = Carbon::now();
        $diary = Diary::find($id);

        if($diary)
        {
            $diary -> archive_status = "Yes"; 
            $diary -> createdBy = $getBy;
            $diary -> createdUtc = $getUtc;
            $diary -> update();

            return response()->json(['status' => 200, 'messages' => 'Diary telah di archive']);
        }
        else
        {
            return response()->json(['status' => 404, 'messages' => 'Ada kesalahan dalam penyimpanan']);
        }
    }

    public function destroy($id)
    {
        Diary::find($id)->delete();
      
        return Response()->json(['success'=>'Product deleted successfully.']);
    }}
