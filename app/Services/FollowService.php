<?php

namespace App\Services;

use App\Models\User;
use App\Models\Follow;

class FollowService
{
    /**
     * Follow an author.
     */
    public function follow(User $follower, User $following): bool
    {
        if ($follower->id === $following->id) {
            return false;
        }

        if (!$following->isAuthor()) {
            return false;
        }

        $follower->following()->syncWithoutDetaching([$following->id]);
        return true;
    }

    /**
     * Unfollow an author.
     */
    public function unfollow(User $follower, User $following): bool
    {
        $follower->following()->detach($following->id);
        return true;
    }

    /**
     * Check if a user is following another user.
     */
    public function isFollowing(User $follower, User $following): bool
    {
        return $follower->following()->where('following_id', $following->id)->exists();
    }

    /**
     * Get follower count for a user.
     */
    public function getFollowerCount(User $user): int
    {
        return $user->followers()->count();
    }

    /**
     * Get following count for a user.
     */
    public function getFollowingCount(User $user): int
    {
        return $user->following()->count();
    }
}
