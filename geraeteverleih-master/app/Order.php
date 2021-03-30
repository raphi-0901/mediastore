<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Foreach_;

class Order extends Model
{
    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'picked_at' => 'datetime',
        'returned_at' => 'datetime',
        'answer' => 'boolean',
    ];

    //a order belongs to many devices
    public function devices()
    {
        return $this->belongsToMany(Device::class)
            ->withPivot('out_scan', 'back_scan', 'note_before', 'note_after')
            ->withTimestamps()
            ->withTrashed();
    }

    //one order belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    //one teacher answers the order
    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by')->withTrashed();
    }

    //one teacher gives the devices
    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by')->withTrashed();
    }

    //one teacher returns the order
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by')->withTrashed();
    }

    public function status()
    {
        //first value in array can be neglected
        if ($this->answer === false) {
            return [0, "Bestellung abgelehnt", '<span class="badge badge-danger text-white">Bestellung abgelehnt</span>'];
        }

        //Geräte sind nicht ausgegeben und das from datum ist noch nicht überschritten: Bestellung aufgebeben.
        if (!$this->picked_at && $this->from > Carbon::now() && $this->answer === null) {
            return [1, "Bestellung aufgegeben", '<span class="badge badge-primary text-white">Bestellung aufgegeben</span>'];
        }

        //Geräte sind nicht ausgegeben und das from datum ist noch nicht überschritten und es wurde bestätigt: Bestellung akzeptiert.
        if (!$this->picked_at && $this->from > Carbon::now() && $this->answer) {
            return [2, "Bestellung aktzeptiert", '<span class="badge badge-warning text-white">Bestellung aktzeptiert</span>'];
        }

        //Geräte sind nicht ausgegeben und das from datum ist überschritten und es wurde bestätigt: Geräte nicht ausgegeben.
        if (!$this->picked_at && $this->from < Carbon::now() && $this->answer) {
            return [2, "Geräte nicht ausgegeben", '<span class="badge badge-danger text-white">Geräte nicht ausgegeben</span>'];
        }

        //Geräte sind nicht ausgegeben und das from datum ist überschritten und es wurde nicht beantwortet: Bestellung unbeantwortet.
        if (!$this->picked_at && $this->from < Carbon::now() && $this->answer === null) {
            return [2, "Bestellung unbeantwortet", '<span class="badge badge-danger text-white">Bestellung unbeantwortet</span>'];
        }

        //aktzeptiert, ausgegeben und Datum ist kleiner als to.
        if ($this->givenBy && $this->picked_at && !$this->returned_by && !$this->returned_at && $this->to > Carbon::now()) {
            return [3, "Bestellung ausgegeben", '<span class="badge badge-success text-white">Bestellung ausgegeben</span>'];
        }

        //aktzeptiert, ausgegeben und Datum ist größer als to.
        if ($this->givenBy && $this->picked_at && !$this->returned_by && !$this->returned_at && $this->to < Carbon::now()) {
            return [3, "Dauer überzogen", '<span class="badge badge-danger text-white">Dauer überzogen</span>'];
        }

        //zurückgegeben.
        if ($this->givenBy && $this->picked_at && $this->returned_by && $this->returned_at) {
            return [4, "Bestellung zurückgegeben", '<span class="badge badge-secondary text-white">Bestellung zurückgegeben</span>'];
        }

        return [-1, "undefined", '<span class="badge badge-danger text-white">undefined</span>'];
    }

    public static function ordersComingBackNextWeek()
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $orders = Order::whereIn('id', $user->belongingOrders()->pluck('id'))
                ->whereDate('to', '<=', Carbon::today()->addWeeks(1))
                ->get();
            return $orders;
        }
        return null;
    }

    public static function ordersGoingOutNextWeek()
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $orders = Order::whereIn('id', $user->belongingOrders()->pluck('id'))
                ->whereDate('from', '<=', Carbon::today()->addWeeks(1))
                ->get();
            return $orders;
        }
        return null;
    }

    public function belongingTeachers()
    {
        $teachers = collect();
        $devices = $this->devices;
        foreach (User::teachers() as $teacher) {
            foreach ($devices as $device) {
                foreach ($teacher->types as $type) {
                    if ($device->type->id === $type->id || $device->type->isSubTypeOf($type)) {
                        $teachers->add($teacher->id);
                        break 2;
                    }
                }
            }
        }
        return User::all()->find($teachers->toArray());
    }

    public function isAbleBetween($from, $to)
    {
        $disabledDates = collect();
        while (true) {
            if ($from >= $to)
                break;

            foreach ($this->devices as $device) {
                if (!$device->isAvailableAt($from, $from)) {
                    $disabledDates->add($from->format('Y-m-d'));
                    break;
                }
            }
            $from->addDay();
        }

        foreach ($disabledDates as $key => $value) {
            if (Carbon::parse($value)->betweenIncluded($this->from, $this->to))
                $disabledDates->forget($key);
        }
        return $disabledDates;
    }
}
