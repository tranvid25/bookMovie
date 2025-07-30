<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Services\BannerService;

class BannerController extends Controller
{
    protected $banner;

    public function __construct(BannerService $banner)
    {
        $this->banner = $banner;
    }

    public function index()
    {
        $banners = $this->banner->getAll();
        return response()->json([
            'status' => 200,
            'message' => 'Danh sách banner',
            'content' => $banners
        ]);
    }

    public function show($id)
    {
        $banner = $this->banner->getById($id);
        if (!$banner) {
            return response()->json(['status' => 404, 'message' => 'Banner not found']);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Chi tiết banner',
            'content' => $banner
        ]);
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = $this->banner->create($request->validated());
        return response()->json([
            'status' => 200,
            'message' => 'Banner created successfully',
            'data' => $banner
        ]);
    }

    public function update(UpdateBannerRequest $request, $id)
    {
        $banner = $this->banner->update($id, $request->validated());
        if (!$banner) {
            return response()->json(['status' => 404, 'message' => 'Banner not found']);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Banner updated successfully',
            'data' => $banner
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->banner->delete($id);
        if (!$deleted) {
            return response()->json(['status' => 404, 'message' => 'Banner not found']);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Banner deleted successfully'
        ]);
    }
}
