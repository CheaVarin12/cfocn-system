<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FTTHService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FTTHServiceController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->status) {
            return redirect()->route('admin-ftth-service-list', 1);
        }
        $service = new FTTHService();
        if ($request->status != 'trash') {
            $services = $service->where('status', $request->status);
        } else {
            $services = $service->onlyTrashed();
        }
        $data['data'] = $services->when(request('search'), function($q) {
                $q->where('name', 'like', '%'. request('search'). '%');
                $q->orWhere('description', 'like', '%'. request('search'). '%');
            })
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin::pages.ftth_service.index', $data);
    }

    public function create($id = null) 
    {
        $service = null;
        if($id) {
            $service = FTTHService::find($id);
        }
        return view('admin::pages.ftth_service.store', compact('service'));
    }

    public function store(Request $request, $id = null) 
    {
        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ];
            if (!$id) {
                FTTHService::create($data);
            } else {
                $service = FTTHService::find($id);
                $service->update($data);
            }
            Session::flash('success', $id ? 'Updated success!' : 'Created success!');
            return redirect()->route('admin-ftth-service-list', 1);
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
            return back();
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $service = FTTHService::find($request->id);
            $service->update(['status' => $request->status]);
            Session::flash('success', 'Updated status!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function delete($id)
    {
        try {
            $service = FTTHService::find($id);
            $service->delete();
            Session::flash('success', 'Service has been moved to trash!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function destroy($id)
    {
        try {
            $service = FTTHService::withTrashed()->find($id);
            $service->forceDelete();
            Session::flash('success', 'Service has been deleted!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function restore($id)
    {
        try {
            $service = FTTHService::withTrashed()->find($id);
            $service->restore();
            Session::flash('success', 'Service restored success!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }
}
