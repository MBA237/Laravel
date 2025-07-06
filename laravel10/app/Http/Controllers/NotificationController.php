<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
        }
        return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
    }
}
