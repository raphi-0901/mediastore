<?php

namespace App\Policies;

use App\Order;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User $user
     * @param \App\Order $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        if ($order->user->id == $user->id)
            return Response::allow();
        //student can only see his order
        else if ($user->isStudent())
            return Response::deny('Not your business');
        //teacher can see only orders which belong to him or are owned by him
        else if ($user->isTeacher()) {
            $types = $user->getRecursiveTypes()->pluck('id');
            $devices = $order->devices;
            foreach ($devices as $device) {
                if ($types->contains($device->type->id))
                    return Response::allow();
            }
            return Response::deny('Keine Berechtigung');
        } else if ($user->isAdmin())
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
        //admin/teacher cant create order
        return $user->isAdmin() ? Response::deny('Admin kann nichts bestellen.') : Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\Order $order
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        if ($user->isStudent())
            return Response::deny('Not your business');
        //teacher can see only orders which belong to him or are owned by him
        else if ($user->isTeacher()) {
            $types = $user->getRecursiveTypes()->pluck('id');
            $devices = $order->devices;
            foreach ($devices as $device) {
                if ($types->contains($device->type->id))
                    return Response::allow();
            }
            return Response::deny('Keine Berechtigung');
        } else if ($user->isAdmin())
            return Response::allow();
    }
}
