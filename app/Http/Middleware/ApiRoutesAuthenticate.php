<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

class ApiRoutesAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (class_exists("\Barryvdh\Debugbar\Facades\Debugbar") && is_a("\Barryvdh\Debugbar\Facades\Debugbar", Facade::class, true)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }
        $fudugo_key = $request->query("fudugo_key");
        $fudugo_password = $request->query("fudugo_password");
        if ($fudugo_key == config("app.fudugo_app_key") && $fudugo_password == config("app.fudugo_app_password")) {
            $json = file_get_contents('php://input');
            $request_data = json_decode($json, true);
            if ($request_data && is_array($request_data) && sizeof($request_data) > 0 && $this->is_array_accociative($request_data)) {
                $request->mergeIfMissing($request_data);
            }
            return $next($request);
        }
        return response("Unauthenticated", 401, ["Content-Type" => "text/plain"]);
    }

    private function is_array_accociative(array $array)
    {
        if (array() === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
