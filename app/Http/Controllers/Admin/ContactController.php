<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class ContactController extends Controller
{
    protected $layout = 'admin::pages.page.';
    function __construct()
    {
        $this->middleware('permission:contact-view', ['only' => ['Page']]);
    }
    public function index()
    {
        Log::info("Start: Admin/ContactController > index | admin: ");
        try{
            $data['data'] = Contact::first();
            return view($this->layout . 'contact', $data);
        }catch(Exception $e){
        Log::error("Error: Admin/ContactController > index | message: ". $e->getMessage());
        }
       
    }
    public function store(Request $req)
    {
        Log::info("Start: Admin/ContactController > store | admin: " .$req);
        $data = Contact::first();
        $items = [
            'phone' => $req->phone,
            'email' => $req->email,
            'address' => $req->address,
            'image' => $req->image,
        ];
        DB::beginTransaction();
        try {
            $data->update($items);
            $status = "Update success.";
            DB::commit();
            Session::flash('success', $status);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/ContactController > store | message: ". $e->getMessage());
            return redirect()->back();
        }
    }
}
