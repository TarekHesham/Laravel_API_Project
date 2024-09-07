<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jobs\Job;


class JobPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->id === $job->employer_id || $job->status === 'open';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployer();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Job $job): bool
    {
        //
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can cancel the job
     */
    public function cancel(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can close the job
     */
    public function close(User $user, Job $job): bool
    {
        return $user->isAdmin() || $user->isEmployer() && $user->id === $job->employer_id;
    }

    /**
     * Determine whether the user can accept/reject the job
     */
    public function acceptReject(User $user, Job $job): bool
    {
        return $user->isAdmin();
    }
}
