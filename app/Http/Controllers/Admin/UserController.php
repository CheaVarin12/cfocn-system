<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModelHasPermission;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\UploadFile;
use App\Models\User;
use App\Services\QueryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $queryService = null;
    public function __construct(QueryService $ser)
    {
        $this->middleware('permission:user-view', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:user-update', ['only' => ['onCreate', 'onSave', 'onUpdateStatus', 'onRestore']]);
        $this->middleware('permission:user-change-passowrd', ['only' => ['onChangePassword', 'onSavePassword']]);
        $this->middleware('permission:user-permission', ['only' => ['setPermission', 'savePermission']]);
        $this->middleware('permission:user-delete', ['only' => ['onDelete']]);
        $this->queryService = $ser;
    }

    public function login()
    {
        $this->getExchangeRate();
        if (Auth::check()) {
            return redirect()->route('admin-dashboard');
        }
        return view("admin::auth.sign-in");
    }

    public function forgotPassword()
    {
        return view("admin::auth.forgot-password");
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/UserController > index | admin: " . $req);
        try {
            if (!$req->id) {
                return redirect()->route('admin-user-list', 1);
            }
            $data['data'] = User::when(filled(request('keyword')), function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%' . request('keyword') . '%');
                    $q->orWhere('phone', 'like', '%' . request('keyword') . '%');
                    $q->orWhere('email', 'like', '%' . request('keyword') . '%');
                });
            })
                ->when(request('role'), function ($q) {
                    $q->where('role', request('role'));
                })
                ->where('type', 'admin')
                ->where('role', '!=', config('dummy.user.role.super_admin'))
                ->where("status", $req->id)
                ->orderByDesc("created_at")
                ->paginate(50);

            return view("admin::pages.user.index", $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/UserController > index | message: " . $error->getMessage());
        }
    }

    public function viewTrash(Request $req)
    {
        Log::info("Start: Admin/UserController > viewTrash | admin: " . $req);
        try {
            $data['id'] = $req->id;
            $data['data'] = User::onlyTrashed()
                ->when(filled(request('keyword')), function ($q) {
                    $q->where('name', 'like', '%' . request('keyword') . '%')
                        ->orWhere('phone', 'like', '%' . request('keyword') . '%')
                        ->orWhere('email', 'like', '%' . request('keyword') . '%');
                })
                ->where('role', '!=', config('dummy.user.role.super_admin'))
                ->orderBy("created_at", "desc")
                ->paginate(10);

            return view("admin::pages.user.index", $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/UserController > viewTrash | message: " . $error->getMessage());
        }
    }

    public function onCreate(Request $req)
    {
        Log::info("Start: Admin/UserController > onCreate | admin: " . $req);
        try {
            $data["data"] = User::where('type', 'admin')->where('id', $req->id)->first();
            return view("admin::pages.user.create", $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/UserController > onCreate | message: " . $error->getMessage());
        }
    }

    public function onSave(Request $req)
    {
        Log::info("Start: Admin/UserController > onSave | admin: " . $req);
        $id = $req->id;
        $item = [
            "username" => $req->name,
            "email" => $req->email,
            "phone" => $req->phone,
            "status" => $req->status,
            "avatar" => $req->avatar ?? $req->tmp_file ?? null,
            "remember_token" => $req->_token,
            "type" => "admin",
        ];

        $req->validate([
            "email" => "nullable|unique:users,email" . ($id ? ",$id" : ''),
            "phone" => "nullable|unique:users,phone" . ($id ? ",$id" : ''),
        ], [
            "email.unique" => "unique_email",
            "phone.unique" => "unique_phone",
        ]);
        $status = "Create success.";
        try {
            if (!$id) {
                $item["role"] = "admin";
                $item["password"] = bcrypt($req->password);
                User::create($item);
            } else {
                User::find($id)->update($item);
                $status = "Update success.";
            }
            Session::flash("success", $status);
            return redirect()->route("admin-user-list", 1);
        } catch (Exception $error) {
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/UserController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onChangePassword(Request $req)
    {
        Log::info("Start: Admin/UserController > onChangePassword | admin: " . $req);
        try {
            $user = User::where('type', 'admin')->where('id', $req->id)->first();
            if ($user->role == 'super_admin') {
                return redirect()->route("admin-user-list", 1);
            }
            return view("admin::pages.user.change-password", ['data' => $user]);
        } catch (Exception $error) {
            Log::error("Error: Admin/UserController > onChangePassword | message: " . $error->getMessage());
        }
    }

    public function onSavePassword(Request $req)
    {
        Log::info("Start: Admin/UserController > onSavePassword | admin: " . $req);
        $item = [
            "password" => bcrypt($req->password),
        ];
        try {
            $user = User::find($req->id);
            $user->update($item);
            $status = "change password success";
            Session::flash("success", $status);
        } catch (Exception $error) {
            Session::flash("warning", "change password unsuccess");
            Log::error("Error: Admin/UserController > onSavePassword | message: " . $error->getMessage());
        }
        return redirect()->route("admin-user-list", 1);
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/UserController > onUpdateStatus | admin: " . $req);
        $status = true;
        $item = [
            "status" => $req->status,
        ];
        try {
            $status = $req->status == 2 ? "Disable successful!" : "Enable successful!";
            User::where("id", $req->id)->update($item);
            Session::flash("success", $status);
        } catch (Exception $error) {
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/UserController > onUpdateStatus | message: " . $error->getMessage());
        }
        return redirect()->back();
    }

    public function onDelete(Request $req)
    {
        Log::info("Start: Admin/UserController > onDelete | admin: " . $req);
        $status = "Delete successful!";
        try {
            if ($req->to_trash) {
                User::find($req->id)->delete();
            }
            $status = true;
        } catch (Exception $error) {
            $status = "Delete unsuccess!";
            Log::error("Error: Admin/UserController > onDelete | message: " . $error->getMessage());
        }
        Session::flash("success", $status);
        return redirect()->back();
    }

    public function onRestore(Request $req)
    {
        Log::info("Start: Admin/UserController > onRestore | admin: " . $req);
        $status = "Restore successful!";
        try {
            User::withTrashed()->find($req->id)->restore();
            Session::flash("success", $status);
        } catch (Exception $error) {
            $status = "Restore unsuccess!";
            Session::flash("warning", $status);
            Log::error("Error: Admin/UserController > onRestore | message: " . $error->getMessage());
        }
        return redirect()->back();
    }

    public function setPermission()
    {
        Log::info("Start: Admin/UserController > onRestore | admin: ");
        try {
            // check user can't update yourself and super admin
            $user = User::find(request("id"));
            if ($user->role == "super_admin" || $user->id == Auth::user()->id) {
                return redirect()->back();
            }

            $data['user']   = User::find(request("id"));
            $authId = auth()->user()->role != "super_admin" ? auth()->user()->id : null;

            $data['ModulPermission'] =
                ModulePermission::with('permission')
                // get all permission of user login
                ->when($authId, function ($q) use ($authId) {
                    $q->whereHas('permission', function ($q) use ($authId) {
                        $q->whereHas('ModelHasPermission', function ($q) use ($authId) {
                            $q->where('model_id', $authId);
                        });
                    });
                })
                ->orderBy('sort_no')
                ->get();
            if (isset($data['ModulPermission']) && count($data['ModulPermission']) > 0)
                foreach ($data['ModulPermission'] as $module) {
                    $module->check = false;
                    if (isset($module->permission) && count($module->permission) > 0) {
                        foreach ($module->permission as $perItem)
                            if (in_array($perItem->id, $data['user']->ModelHasPermission->pluck('permission_id')->toArray())) {
                                $perItem->check = true;
                                $module->check = true;
                            } else {
                                $perItem->check = false;
                            }
                    }
                }
            $data['permission'] = $data["user"]->ModelHasPermission;

            return view("admin::pages.user.permission", $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/UserController > onRestore | message: " . $error->getMessage());
        }
    }

    public function savePermission(Request $req)
    {
        Log::info("Start: Admin/UserController > savePermission | admin: ");
        $req->validate([
            "permission" => "required",
        ], [
            "permission.required" => "Permission required",
        ]);
        if (!$req->permission) {
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            $data = User::find($req->id);
            $permissions = Permission::pluck('name')->toArray();
            $revoke = array_diff($permissions, $req->permission);
            $data->givePermissionTo($req->permission);
            $data->revokePermissionTo($revoke);
            DB::commit();
            Session::flash("success", 'Set permission successful!');
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            $status = "Permission unsuccess!";
            Session::flash("warning", $status);
            Log::error("Error: Admin/UserController > savePermission | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
