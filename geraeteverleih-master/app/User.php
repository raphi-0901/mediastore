<?php

namespace App;

use App\Jobs\SendResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName', 'lastName', 'class', 'email', 'password', 'role_id',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        if(!$this->microsoftId)
            SendResetPassword::dispatch($this, $token)->onConnection('database');
        else
            return redirect(route('index'))->withErrors("Du kannst dein Passwort nicht zurücksetzen.");
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shoppingCart()
    {
        return $this->belongsToMany(Device::class)->withTimestamps()->withPivot('from', 'to');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class)->withTimestamps();
    }

    public function answerings()
    {
        return $this->hasMany(Order::class, 'answered_by');
    }

    public function denies()
    {
        return $this->hasMany(Order::class, 'answered_by')->where('answer', false);
    }

    public function accepts()
    {
        return $this->hasMany(Order::class, 'answered_by')->where('answer', true);
    }

    public function givens()
    {
        return $this->hasMany(Order::class, 'given_by');
    }

    public function returnings()
    {
        return $this->hasMany(Order::class, 'returned_by');
    }

    //functions
    public static function students()
    {
        return User::where("role_id", 3)
            ->orderBy("class")
            ->orderBy("lastName")
            ->orderBy("firstName")
            ->get();
    }

    public static function teachers()
    {
        return User::where("role_id", 2)
            ->orderBy("class")
            ->orderBy("lastName")
            ->orderBy("firstName")
            ->get();
    }

    public static function admins()
    {
        return User::where("role_id", 1)
            ->orderBy("class")
            ->orderBy("lastName")
            ->orderBy("firstName")
            ->get();
    }

    public function isStudent()
    {
        return $this->role()->where("name", "Student")->exists();
    }

    public function isTeacher()
    {
        return $this->role()->where("name", "Teacher")->exists();
    }

    public function isAdmin()
    {
        return $this->role()->where("name", "Admin")->exists();
    }

    public function belongingOrders()
    {
        $orders = Order::all();
        $belonging = collect();
        $recursiveTypes = $this->getRecursiveTypes()->pluck('id');

        //gehe alle Devices durch
        foreach ($orders as $order) {
            foreach ($order->devices as $device) {
                //sobald ein Device in dem Type vorhanden ist
                //wird der Order angezeigt und die Schleife wird gebrochen.
                    if ($recursiveTypes->contains($device->type->id)) {
                        $belonging->add($order);
                        break;
                }
            }
        }
        return $belonging->unique();
    }

    public function clearShoppingCart()
    {
        $this->shoppingCart()->detach();
    }

    public function belongingDevices()
    {
        return Device::whereIn('type_id', $this->getRecursiveTypes()->pluck('id'))->get();
    }

    public function belongingDeletedDevices()
    {
        return Device::onlyTrashed()->whereIn('type_id', $this->getRecursiveTypes()->pluck('id'))->get();
    }

    public function displayName()
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    public function getRecursiveTypes()
    {
        $allTypes = Type::all();
        $yourTypes = $this->types;
        $types = collect();
        foreach ($allTypes as $allTypeKey => $allType) {
            foreach ($yourTypes as $yourTypeKey => $yourType)
            {
                if ($allType->id === $yourType->id || $allType->isSubTypeOf($yourType)) {
                    $types->add($allType);
                    break;
                }
            }
        }
        return $types->unique();
    }
}
