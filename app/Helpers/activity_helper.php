<?php

// namespace App\Helpers\activity_helper;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

function log_activity($action, $details = null, $source = 'laravel')
{
    Activity::create([
        'user_id' => Auth::id(),
        'action' => $action,
        'details' => is_array($details) ? json_encode($details) : $details,
        'source' => $source,
    ]);
}
