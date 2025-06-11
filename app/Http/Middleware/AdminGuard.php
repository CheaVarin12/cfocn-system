<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Session\Store;

class AdminGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $session;
    protected $timeout = 12;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }
        return redirect()->route('admin-login', ['returnUrl' => $request->url()]);
    }
    public function handleDB(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userExpiry = $request->user()?->userExpiry;
            if ($userExpiry?->expiry_time < Carbon::now()) {
                $randomString = Str::random(8);
                $userExpiry->pass = $randomString;
                $userExpiry->expiry_time = Carbon::now()->addSeconds(30);
                $userExpiry->update();
                Auth::logout();
                return redirect()->route('admin-login', ['returnUrl' => $request->url()]);
            }
            return $next($request);
        }
        return redirect()->route('admin-login', ['returnUrl' => $request->url()]);
    }
    public function handleSession($request, Closure $next)
    {
        $isLoggedIn = $request->path() != 'dashboard/logout';
        if (!session('lastActivityTime')) {
            $this->session->put('lastActivityTime', time());
        } elseif (time() - $this->session->get('lastActivityTime') > $this->timeout) {
            $this->session->forget('lastActivityTime');
            $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
            $email = $request->user()->email;
            auth()->logout();
            // return message('You had not activity in ' . $this->timeout / 60 . ' minutes ago.', 'warning', 'login')->withInput(compact('email'))->withCookie($cookie);
            return redirect()->route('admin-login', ['returnUrl' => $request->url()]);
        }
        $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');
        return $next($request);
    }
}
