<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TinTuc;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function PHPUnit\Framework\fileExists;

class newsController extends Controller
{
    public function index(){
        $news=TinTuc::all();

        if($news->isEmpty()){
            return response()->json([
                'status'=>404,
                'message'=>'No news found'
            ],404);
        }
        else{
            return response()->json([
                'status'=>200,
                'content'=>$news
            ]);
        }
    }
    public function show(string $id){
        try {
            $news=TinTuc::findOrFail($id);
            return response()->json([
                'status'=>200,
                'content'=>$news
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'=>404,
                'message'=>'New not found'
            ],404);
        }
    }
    public function store(Request $request){
    $validator = Validator::make($request->all(), [
        'tieuDe' => 'required|string|max:255',
        'tacGia' => 'required|string',
        'noiDungPhu' => 'nullable|string',
        'noiDung' => 'required|string|max:255',
        'hinhAnh' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'fileName' => 'nullable|string|max:255',
        'theLoai' => 'required|string|max:255',
        'maPhim' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()
        ]);
    }
    try {
        $hinhAnhUrl = null;
    $imageName = null;

    if ($request->hasFile('hinhAnh')) {
        $file = $request->file('hinhAnh');
        $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $imageStore = 'images/tintuc/';
        $file->move(public_path($imageStore), $imageName);
        $hinhAnhUrl = url($imageStore . $imageName);
    }

    TinTuc::create([
        'tieuDe' => $request->tieuDe,
        'tacGia' => $request->tacGia,
        'noiDungPhu' => $request->noiDungPhu,
        'noiDung' => $request->noiDung,
        'hinhAnh' => $hinhAnhUrl,
        'fileName' => $imageName,
        'theLoai' => $request->theLoai,
        'maPhim' => $request->maPhim
    ]);

    return response()->json([
        'status' => 200,
        'message' => 'Create new Successfully!'
    ]);
    } catch (Exception $e) {
    return response()->json([
        'status'=>500,
        'message'=>'Server error: ' . $e->getMessage()
    ],500);
    }

}



    public function update(Request $request, string $id)
{
    $tintuc = TinTuc::find($id);

    if (!$tintuc) {
        return response()->json([
            'status' => 404,
            'message' => 'Not found news'
        ]);
    }

    $validator = Validator::make($request->all(), [
        'tieuDe' => 'required|string|max:255',
        'tacGia' => 'required|string',
        'noiDungPhu' => 'nullable|string',
        'noiDung' => 'required|string|max:255',
        'hinhAnh' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'fileName' => 'nullable|string|max:255',
        'theLoai' => 'required|string|max:255',
        'maPhim' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()
        ]);
    }

   try {
    $hinhAnhUrl = $tintuc->hinhAnh;
    $imageName = $tintuc->fileName;

    if ($request->hasFile('hinhAnh')) {
        $file = $request->file('hinhAnh');
        // Xóa ảnh cũ nếu tồn tại
        if ($hinhAnhUrl) {
            $oldPath = public_path(parse_url($hinhAnhUrl, PHP_URL_PATH));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $imageDirectory = 'images/tintuc/';
        $file->move(public_path($imageDirectory), $imageName);
        $hinhAnhUrl = url($imageDirectory . $imageName);
    }

    // Cập nhật dữ liệu
    $tintuc->tieuDe = $request->tieuDe;
    $tintuc->tacGia = $request->tacGia;
    $tintuc->noiDungPhu = $request->noiDungPhu;
    $tintuc->noiDung = $request->noiDung;
    $tintuc->hinhAnh = $hinhAnhUrl;
    $tintuc->fileName = $imageName;
    $tintuc->theLoai = $request->theLoai;
    $tintuc->maPhim = $request->maPhim;

    $tintuc->save();

    return response()->json([
        'status' => 200,
        'message' => 'Updated news successfully!',
        'data' => $tintuc
    ]);
   } catch (Exception $e) {
    return response()->json([
        'status'=>500,
        'message'=>'Server error: '. $e->getMessage()
    ],500);
   }
}

    public function destroy($id){
        $tintuc=TinTuc::findOrFail($id);
        if(!$tintuc){
            return response()->json([
                'status'=>404,
                'message'=>'News not found'
            ],404);
        }
        try {
            if($tintuc->fileName){
                $oldPath=public_path('images/tintuc/' . $tintuc->fileName);
                if(fileExists($oldPath)){
                    @unlink($oldPath);
                }
            }
            $tintuc->delete();
            return response()->json([
                'status'=>200,
                'message'=>'New deleted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'=>500,
                'message'=>'Server error: ' . $e->getMessage()
            ],500);
        }
    }

}

