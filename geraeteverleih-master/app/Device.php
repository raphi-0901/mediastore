<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Device extends Model
{
    use SoftDeletes;

    //relations
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }

    public function isInShoppingCart()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->exists();
    }

    public function shoppingCart()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('from', 'to');;
    }

    public function isAvailable()
    {
        $from = Carbon::today();
        $to = Carbon::tomorrow();
        //alle Bestellungen in bestimmten Zeitraum
        $orders = Order::find(Order::
        //outer
        where(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '<=', $to],
            ]
        //inner
        )->orWhere(
            [
                ['from', '<=', $from],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inLeft
        )->orWhere(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inRight
        )->orWhere(
            [
                ['from', '<=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '<=', $to],
            ]
        )
            ->pluck('id'));

        //alle Geräte in den Bestellungen dürfen nicht angezeigt werden.
        foreach ($orders as $order) {
                foreach ($order->devices as $device)
                    if ($device->id == $this->id)
                        return false;
        }

        //die Geräte die zurzeit im Warenkorb sind und im bestimmten Zeitraum liegen, dürfen auch nicht angezeigt werden.
        $scDevices = DB::table('device_user')
            ->where(function ($query) use ($from, $to) {
                $query->where(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '<=', $to],
                    ]
                //inner
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inLeft
                )->orWhere(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inRight
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '<=', $to],
                    ]
                );
            })->pluck('device_id');

        foreach ($scDevices as $scDevice)
            if ($scDevice == $this->id)
                return false;
        return true;
    }

    public function isAvailableAt($from, $to)
    {
        //alle Bestellungen in bestimmten Zeitraum
        $orders = Order::find(Order::
        //outer
        where(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '<=', $to],
            ]
        //inner
        )->orWhere(
            [
                ['from', '<=', $from],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inLeft
        )->orWhere(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inRight
        )->orWhere(
            [
                ['from', '<=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '<=', $to],
            ]
        )
            ->pluck('id'));

        //alle Geräte in den Bestellungen dürfen nicht angezeigt werden.
        foreach ($orders as $order) {
            foreach ($order->devices as $device)
                if ($device->id == $this->id)
                    return false;
        }

        //die Geräte die zurzeit im Warenkorb sind und im bestimmten Zeitraum liegen, dürfen auch nicht angezeigt werden.
        $scDevices = DB::table('device_user')
            ->where(function ($query) use ($from, $to) {
                $query->where(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '<=', $to],
                    ]
                //inner
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inLeft
                )->orWhere(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inRight
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '<=', $to],
                    ]
                );
            })->pluck('device_id');

        foreach ($scDevices as $scDevice)
            if ($scDevice == $this->id)
                return false;
        return true;
    }

    public static function availableDevices($from, $to)
    {
        //alle Bestellungen in bestimmten Zeitraum
        $orders = Order::find(Order::
        //outer
        where(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '<=', $to],
            ]
        //inner
        )->orWhere(
            [
                ['from', '<=', $from],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inLeft
        )->orWhere(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inRight
        )->orWhere(
            [
                ['from', '<=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '<=', $to],
            ]
        )
            ->pluck('id'));

        $devices = collect();
        //alle Geräte in den Bestellungen dürfen nicht angezeigt werden.
        foreach ($orders as $order) {
            if ($order->answer !== false)
                foreach ($order->devices as $device)
                    $devices->add($device->id);
        }

        //die Geräte die zurzeit im Warenkorb sind und im bestimmten Zeitraum liegen, dürfen auch nicht angezeigt werden.
        $scDevices = DB::table('device_user')
            ->where('user_id', '!=', Auth::id())
            ->where(function ($query) use ($from, $to) {
                $query->where(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '<=', $to],
                    ]
                //inner
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inLeft
                )->orWhere(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inRight
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '<=', $to],
                    ]
                );
            })->pluck('device_id');

        foreach ($scDevices as $scDevice)
            $devices->add($scDevice);

        return Device::whereNotIn('id', $devices->unique()->toArray())->get();
    }

    public static function availableDevicesInTypes($from, $to, $types, $orderBy)
    {
        //alle Bestellungen in bestimmten Zeitraum
        $orders = Order::find(Order::
        //outer
        where(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '<=', $to],
            ]
        //inner
        )->orWhere(
            [
                ['from', '<=', $from],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inLeft
        )->orWhere(
            [
                ['from', '>=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '>=', $to],
            ]
        //inRight
        )->orWhere(
            [
                ['from', '<=', $from],
                ['from', '<=', $to],
                ['to', '>=', $from],
                ['to', '<=', $to],
            ]
        )
            ->pluck('id'));

        $devices = collect();
        //alle Geräte in den Bestellungen dürfen nicht angezeigt werden.
        foreach ($orders as $order) {
            if ($order->answer !== false)
                foreach ($order->devices as $device)
                    $devices->add($device->id);
        }

        //die Geräte die zurzeit im Warenkorb sind und im bestimmten Zeitraum liegen, dürfen auch nicht angezeigt werden.
        $scDevices = DB::table('device_user')
            ->where('user_id', '!=', Auth::id())
            ->where(function ($query) use ($from, $to) {
                $query->where(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '<=', $to],
                    ]
                //inner
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inLeft
                )->orWhere(
                    [
                        ['from', '>=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '>=', $to],
                    ]
                //inRight
                )->orWhere(
                    [
                        ['from', '<=', $from],
                        ['from', '<=', $to],
                        ['to', '>=', $from],
                        ['to', '<=', $to],
                    ]
                );
            })->pluck('device_id');

        foreach ($scDevices as $scDevice)
            $devices->add($scDevice);

        //wenn der Type ein Subtype hat, werden diese auch angezeigt.
        $typesColl = collect();
        foreach ($types as $t) {
            $type = Type::all()->find($t);
            foreach ($type->getAllSubtypes()->pluck('id') as $subType)
                $typesColl->add($subType);
        }

        $finalDevices = Device::whereNotIn('id', $devices->unique()->toArray())
            ->whereIn("type_id", $typesColl->unique()->toArray());

        switch ($orderBy) {
            case 'A-Z':
            default:
                $finalDevices->orderBy('name');
                break;
            case 'Z-A':
                $finalDevices->orderBy('name', 'desc');
                break;
        }
        return $finalDevices->get();
    }
}
