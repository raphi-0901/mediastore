<?php

namespace App\Policies;

use App\Device;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DevicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function before(User $user)
    {
        if ($user->isAdmin())
            return Response::allow();
    }


    public function viewAny(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User $user
     * @param \App\Device $device
     * @return mixed
     */
    public function view(User $user, Device $device)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isTeacher() ? Response::allow() : Response::deny('Keine Berechtigung');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\Device $device
     * @return mixed
     */
    public function update(User $user, Device $device)
    {
        if ($user->isTeacher())
            return $user->belongingDevices()->contains($device) ? Response::allow() : Response::deny('Nicht zuständig für dieses Gerät');
        else
            return Response::deny('Keine Berechtigung');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\Device $device
     * @return mixed
     */
    public function delete(User $user, Device $device)
    {
        if ($user->isTeacher())
            return $user->belongingDevices()->contains($device) ? Response::allow() : Response::deny('Nicht zuständig für dieses Gerät');
        else
            return Response::deny('Keine Berechtigung');
    }

    public function restore(User $user, Device $device)
    {
        if ($user->isTeacher())
            return $user->belongingDeletedDevices()->contains($device) ? Response::allow() : Response::deny('Nicht zuständig für dieses Gerät');
        else
            return Response::deny('Keine Berechtigung');
    }

    public function forceDelete(User $user, Device $device)
    {
        if ($user->isTeacher())
            return $user->belongingDeletedDevices()->contains($device) ? Response::allow() : Response::deny('Nicht zuständig für dieses Gerät');
        else
            return Response::deny('Keine Berechtigung');
    }
}
