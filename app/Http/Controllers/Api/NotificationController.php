<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->user()->unreadNotifications;

        return NotificationResource::collection($notifications);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Mark a notification as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);

        if (! $notification) {
            return response()->json(['message' => __('messages.not_found')], Response::HTTP_NOT_FOUND);
        }

        $notification->markAsRead();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
