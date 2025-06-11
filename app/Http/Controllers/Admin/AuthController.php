<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthChangePasswordRequest;
use App\Http\Requests\Admin\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $messageSuccess = '';
        $messageError = '';
        try {
            DB::table('rates')
                ->orderByDesc('id')
                ->limit(1)
                ->update(['rate' => $request->exchange_rate]);

            if ($request->exchange_rate) {
                $messageSuccess = 'Exchange rate today (1 USD = ' . $request->exchange_rate . ' KHR)';
            } else {
                $messageError  = 'Exchange rate update failed';
            }
        } catch (\Exception $ex) {
            $messageError  = 'Exchange rate update failed';
        }

        Log::info("Start: Admin/AuthController > login | admin: " . $request?->email);
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        DB::beginTransaction();
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1], $request->remember)) {
                DB::commit();
                if ($messageSuccess) {
                    Session::flash("success", $messageSuccess);
                } elseif ($messageError) {
                    Session::flash("error", $messageError);
                }

                return request()->returnUrl ? redirect()->to(request()->returnUrl) : redirect()->route('admin-dashboard');
            } else {
                return Redirect::back()->with('status', false);
            }
        } catch (Exception $e) {
            if ($messageSuccess) {
                Session::flash("success", $messageSuccess);
            } elseif ($messageError) {
                Session::flash("error", $messageError);
            }

            DB::rollback();
            Log::error("Error: Admin/AuthController > login | message: " . $e->getMessage());
            return Redirect::back()->with('status', false);
        }
    }
    public function save(AuthRequest $req)
    {
        $item = $req->all();
        $item['avatar'] = $req->image;
        try {
            $data = $this->updateWithResetPassword($item);
            return response()->json([
                "data" => $data,
                "message" => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                "data" => null,
                "message" => false
            ]);
        }
    }
    public function changePassword(AuthChangePasswordRequest $req)
    {
        $item = $req->all();
        $item["password"] = bcrypt($req->password);
        try {
            $data = $this->updateWithResetPassword($item);
            return response()->json([
                "data" => $data,
                "message" => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                "data" => null,
                "message" => false
            ]);
        }
    }

    public function updateWithResetPassword($item)
    {
        $data = User::find($item['id']);
        $data->update($item);
        return $data;
    }

    public function signOut()
    {
        Auth::logout();
        session()->forget('current_admin_login');
        return redirect()->route('admin-login');
    }
}
