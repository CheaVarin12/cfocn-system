<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlideRequest;
use App\Models\Slide;
use App\Services\SlideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SlideController extends Controller
{
    protected $layout = 'admin::pages.slide.';
    private $SlideService;
    function __construct(SlideService $itemSer)
    {
        $this->middleware('permission:slide-view', ['only' => ['index']]);
        $this->middleware('permission:slide-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:slide-update', ['only' => ['onEdit', 'onSave', 'onUpdateStatus', 'restore']]);
        $this->middleware('permission:slide-delete', ['only' => ['delete', 'restore', 'destroy']]);
        $this->SlideService = $itemSer;
    }
    public function index(Request $req)
    {
        $data['status'] = $req->status;
        $search = $req->search ? $req->search : '';
        if (!$req->status) {
            return redirect()->route('admin-slide-list', 1);
        }
        if ($req->status != 'trash') {
            $query = Slide::where('status', $req->status);
        } else {
            $query = Slide::onlyTrashed();
        }
        $data['data'] = $query->where(function ($q) use ($search) {
            if ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            }
        })
        ->orderBy('ordering', 'asc')->paginate(50);

        return view($this->layout . 'index', $data);
    }
    public function onCreate()
    {
        $data['ordering'] = $this->SlideService->sumLevel();
        return view($this->layout . 'create', $data);
    }
    public function onEdit($id)
    {
        $data["data"] = Slide::find($id);
        if ($data['data']) {
            return view($this->layout . 'edit', $data);
        }
        return redirect()->route('admin-slide-list');
    }
    public function onSave(SlideRequest $req, $id = null)
    {
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Slide::find($id);
                $this->SlideService->update($data, $req);
                $status = "Update success.";
            } else {
                $data = $this->SlideService->create($req);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-slide-list', 1);
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            return redirect()->back();
        }
    }
    public function onUpdateStatus(Request $req)
    {
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Slide::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status !== '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (\Exception $error) {
            DB::rollback();
            $status = false;
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        DB::beginTransaction();
        $data = Slide::withTrashed()->where('id', $id)->first();
        $data->restore();
        DB::commit();
        Session::flash('success', 'Restore move to trash!');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $item = Slide::withTrashed()->where('id', $request->id)->first();
        DB::beginTransaction();
        if ($item) {
            $item->forceDelete();
        }
        DB::beginTransaction();
        Session::flash('success', 'Delete success!');
        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $item = Slide::where('id', $request->id)->first();
        DB::beginTransaction();
        if ($item) {
            $item->delete();
        }
        DB::commit();
        Session::flash('success', 'Move to trash success!');
        return redirect()->back();
    }
}
