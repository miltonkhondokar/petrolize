<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Services\RouteService;
use Illuminate\Support\Facades\App;
use App\Constants\UserType;

if (!function_exists('active_menu')) {
    function active_menu($routes, $subRoute = null, $type = 'menu') // $type: menu, submenu, link
    {
        $currentRoute = Request::route()->getName();
        $isActive = false;

        if (!is_array($routes)) {
            $routes = [$routes];
        }

        foreach ($routes as $route) {
            $pattern = preg_quote($route, '/'); // escape regex special characters
            $pattern = str_replace('\*', '.*', $pattern); // replace wildcard * with regex


            if (preg_match('/^' . $pattern . '$/', $currentRoute)) {
                $isActive = true;
                break;
            }

            // Optional: also check if current route starts with $subRoute
            if ($subRoute && Str::startsWith($currentRoute, $subRoute)) {
                $isActive = true;
                break;
            }
        }

        if (!$isActive) {
            return '';
        }

        return match ($type) {
            'menu'    => 'menu-item-active menu-item-open active hover show',   // For parent menu
            'submenu' => 'show',                                                // For submenus
            'link'    => 'active here',                                         // For active links
            default   => 'active'
        };
    }
}

if (!function_exists('getRandomQuote')) {
    function getRandomQuote()
    {
        $quotes = [
            // ⛽ Fuel Operations & Efficiency
            'Every liter counts — manage your fuel, master your flow.',
            'Smooth pumps, happy customers, and a well-oiled system.',
            'Fuel stock in check, business on track.',
            'Efficiency isn’t optional — it’s in every drop of fuel.',
            'Accurate readings, optimal flow, no surprises.',
            'Keep your tanks full and your operations flowing.',
            'Fuel management is the heart of a successful fuel station.',
            'Every drop of fuel tells a story of precision and care.',
            'The nozzle doesn’t lie — track it, control it, optimize it.',
            'From tanks to pumps, every number matters.',

            // 📊 Monitoring & Reporting
            'Real-time data keeps the pumps running and managers informed.',
            'Reports aren’t just numbers — they’re insight into performance.',
            'Know your sales, know your stock, know your business.',
            'Variance today, improvement tomorrow.',
            'Data-driven decisions keep the flow uninterrupted.',

            // 🔧 Maintenance & Reliability
            'Low stock alerts prevent high-stress surprises.',
            'Maintenance on time keeps the pumps online.',
            'Calibrated machines, smooth operations.',
            'Preventive care saves more than just fuel.',

            // 🏆 Motivation / Teamwork
            'A well-run fuel station is the product of a well-informed team.',
            'Managing fuel is easy when your system works for you.',
            'Every nozzle reading is a step towards operational excellence.',
            'Fuel Flow: turning data into efficiency, one drop at a time.',
            'Flow smoothly, manage smartly, serve confidently.',

            // ⚡ Efficiency & Performance
            'Fast pumps, accurate readings, smarter business.',
            'Every drop saved is profit earned.',
            'From tank to nozzle, optimize every step of the flow.',
            'Fuel efficiency begins with real-time awareness.',
        ];

        return $quotes[array_rand($quotes)];
    }
}

if (!function_exists('audit_log')) {
    function audit_log(string $action, ?int $itemId = null, string $type = 'other', ?Request $request = null, ?int $userId = null): void
    {
        $request = $request ?? request();

        AuditLog::create([
            'user_id'    => $userId ?? Auth::id(),
            'action'     => $action,
            'item_id'    => $itemId,
            'type'       => $type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
