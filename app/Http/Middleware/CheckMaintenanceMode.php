<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceEnabled = (bool) $this->settingService->get('maintenance_mode_enabled', false);

        if (! $isMaintenanceEnabled) {
            return $next($request);
        }

        // 1. Allow authenticated administrators full bypass
        if ($request->user() && $request->user()->isAdmin()) {
            return $next($request);
        }

        // 2. Allow login/logout routes so administrators can authenticate
        if ($request->is('login') || $request->is('logout') || $request->is('admin*')) {
            return $next($request);
        }

        // 3. For all other requests, display the branded maintenance page with HTTP 503
        $maintenanceMessage = (string) $this->settingService->get(
            'maintenance_message',
            'ZebLearn is undergoing scheduled platform maintenance and upgrade. Our student learning systems will resume shortly.'
        );

        return response()->view('errors.maintenance', [
            'maintenanceMessage' => $maintenanceMessage,
        ], 503);
    }
}
