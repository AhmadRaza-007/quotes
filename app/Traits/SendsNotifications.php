<?php

namespace App\Traits;

use App\Services\NotificationService;

trait SendsNotifications
{
    /**
     * Send notification to user
     *
     * @param mixed $user
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    protected function sendNotification($user, string $title, string $body, array $data = []): array
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->sendToUser($user, $title, $body, $data);
    }

    /**
     * Send notification when user gets a new follower
     *
     * @param mixed $user
     * @param mixed $follower
     * @return array
     */
    protected function sendNewFollowerNotification($user, $follower): array
    {
        return $this->sendNotification(
            $user,
            'New Follower',
            "{$follower->name} started following you",
            [
                'type' => 'new_follower',
                'follower_id' => $follower->id,
                'follower_name' => $follower->name,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );
    }

    /**
     * Send notification when user's wallpaper gets a like
     *
     * @param mixed $user
     * @param mixed $liker
     * @param mixed $wallpaper
     * @return array
     */
    protected function sendWallpaperLikeNotification($user, $liker, $wallpaper): array
    {
        return $this->sendNotification(
            $user,
            'New Like',
            "{$liker->name} liked your wallpaper",
            [
                'type' => 'wallpaper_like',
                'liker_id' => $liker->id,
                'liker_name' => $liker->name,
                'wallpaper_id' => $wallpaper->id,
                'wallpaper_title' => $wallpaper->title,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );
    }

    /**
     * Send notification when user's wallpaper gets a comment
     *
     * @param mixed $user
     * @param mixed $commenter
     * @param mixed $wallpaper
     * @param string $comment
     * @return array
     */
    protected function sendWallpaperCommentNotification($user, $commenter, $wallpaper, string $comment): array
    {
        return $this->sendNotification(
            $user,
            'New Comment',
            "{$commenter->name} commented on your wallpaper",
            [
                'type' => 'wallpaper_comment',
                'commenter_id' => $commenter->id,
                'commenter_name' => $commenter->name,
                'wallpaper_id' => $wallpaper->id,
                'wallpaper_title' => $wallpaper->title,
                'comment_preview' => substr($comment, 0, 100),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );
    }

    /**
     * Send notification for new wallpaper upload
     *
     * @param mixed $user
     * @param mixed $wallpaper
     * @return array
     */
    protected function sendWallpaperUploadNotification($user, $wallpaper): array
    {
        return $this->sendNotification(
            $user,
            'Wallpaper Uploaded',
            "Your wallpaper '{$wallpaper->title}' has been uploaded successfully",
            [
                'type' => 'wallpaper_uploaded',
                'wallpaper_id' => $wallpaper->id,
                'wallpaper_title' => $wallpaper->title,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );
    }

    /**
     * Send notification for admin announcements
     *
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    protected function sendAdminAnnouncement(string $title, string $body, array $data = []): array
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->sendToAllUsers(
            $title,
            $body,
            array_merge($data, [
                'type' => 'admin_announcement',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ])
        );
    }
}
