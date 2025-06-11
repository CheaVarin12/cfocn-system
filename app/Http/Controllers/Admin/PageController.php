<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\PageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    protected $layout = 'admin::pages.page.';
    private $PageService;
    function __construct(PageService $itemSer)
    {
        $this->middleware('permission:about-view', ['only' => ['Page']]);
        $this->middleware('permission:contact-view', ['only' => ['Page']]);
        $this->PageService = $itemSer;
    }
    public function Page(Request $req)
    {
        Log::info("Start: Admin/PageController > Page | admin: ". $req);
        try{
              if (!$req->type) {
            return redirect()->route('admin-dashboard');
        }
        $data['data'] = Page::where('type', $req->type)->first();
        return view($this->layout . $req->type, $data);
        }catch(Exception $error){
            Log::error("Error: Admin/PageController > Page | message: ". $error->getMessage());
        }
    }

    public function onSave(Request $req)
    {
        Log::info("Start: Admin/PageController > onSave | admin: ". $req);
        $req->type = $req->type == 'term-condition' ? 'term_condition' : $req->type;
        $data = Page::where('type', $req->type)->where('id', $req->id)->first();
        $items = [
            'title' => $req->title,
            'content' => $req->content,
            'type' => $req->type,
            'image' => $req->image,
        ];

        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($req->id && $data) {
                $data->update($items);
                $status = "Update success.";
            } else {
                $data = $data->create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->back();
        } catch (Exception  $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/PageController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}
