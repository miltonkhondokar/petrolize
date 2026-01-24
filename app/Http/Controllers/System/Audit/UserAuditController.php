<?php

namespace App\Http\Controllers\System\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\AuditLog;

class UserAuditController extends Controller
{
    public function index()
    {

        $auditLogs = AuditLog::with('user')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(10);

        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];

        return view('application.pages.system.audit.user.index', compact('breadcrumb', 'auditLogs'));
    }
}
