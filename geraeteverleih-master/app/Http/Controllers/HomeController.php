<?php

namespace App\Http\Controllers;

use App\Order;
use App\Setting;
use App\Type;
use App\User;
use App\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       $user = User::all()->find(Auth::id());
        if (!$user)
            return redirect()->route('login');

        switch (true) {
            case $user->isAdmin():
                $pTypes = Type::onlyParentTypes();
                $customizedTypes = collect();
                foreach ($pTypes as $type) {
                    $workLoadType = collect();
                    $workLoadType->put('id', $type->id);
                    $workLoadType->put('name', $type->name);

                    $workLoadType->put('count', Device::whereIn('type_id', $type->getAllSubtypes()->pluck('id'))->count());
                    $customizedTypes->add($workLoadType);
                }

                $devicesPerDay = collect();
                $days = collect();
                for ($i = -15; $i < 30; $i++)
                {
                    $devicesPerDay->add(Device::availableDevices(Carbon::today()->addDays($i), Carbon::today()->addDays($i)));
                    $days->add(Carbon::today()->addDays($i));
                }
                $workLoad = collect();
                foreach ($customizedTypes as $type) {
                    $typeDetails = collect();
                    $typeDetails->put('name', $type["name"]);
                    $allDetails = collect();
                    $subTypes = Type::all()->find($type["id"])->getAllSubtypes()->pluck('id');

                    for ($i = 0; $i < 45; $i++) {
                        $details = array(
                            "y" => $type['count'] == 0 ? 0 : round(100 - $devicesPerDay[$i]->whereIn('id', Device::whereIn('type_id', $subTypes)->pluck('id'))->count() / $type['count'] * 100),
                            "x" => $days[$i]->format('Y-m-d')
                        );
                        $allDetails->add($details);
                    }

                    $typeDetails->put('data', $allDetails);
                    $workLoad->add($typeDetails);
                }

                return view('admin.dashboard')
                    ->with('workLoad', $workLoad)
                    ->with('userCount', User::students()->count())
                    ->with('deviceCount', Device::all()->count())
                    ->with('orderCount', Order::all()->count())
                    ->with('settings', Setting::all())
                    ->with('types', Type::orderBy('name')->get());
           /* case $user->isTeacher()
            :
                return view("teacher.dashboard")
                    ->with('ordersComingBackNextWeek', Order::ordersComingBackNextWeek())
                    ->with('ordersGoingOutNextWeek', Order::ordersGoingOutNextWeek());*/
            case $user->isStudent() || $user->isTeacher():
                return view("student.dashboard")
                    ->with('parentTypes', Type::onlyParentTypes())
                    ->with('subTypes', Type::onlySubTypes());
        }
    }

    public
    function showPolicies()
    {
        return view("policies");
    }

    public
    function showImpressum()
    {
        return view("impressum");
    }
}
