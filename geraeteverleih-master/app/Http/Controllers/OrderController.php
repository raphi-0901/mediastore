<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderAccepted;
use App\Jobs\SendOrderMade;
use App\Jobs\SendOrderDenied;
use App\Jobs\SendOrderNotificationTeacher;
use App\Mail\OrderAccepted;
use App\Mail\OrderDenied;
use App\Order;
use App\Type;
use App\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function index()
    {
        $user = User::all()->find(Auth::id());
        if ($user)
            return view('orders.index');
        else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }

    public function filter()
    {
        $request = $_GET;
        //in view: remove all existing orders and replace with new ones.
        $user = User::all()->find(Auth::id());
        if ($user) {
            $from = Carbon::parse($request["from"]);
            $to = Carbon::parse($request["to"]);
            switch (true) {
                //zeige alle Order
                case $user->isAdmin():
                    $orders = Order::where([
                        ['from', ">=", $from],
                        ['to', "<=", $to],
                    ])
                        ->orderBy('from')
                        ->orderBy('to')
                        ->get();

                    foreach ($orders as $order) {
                        $order->status = $order->status()[2];
                        $order->userDisplayName = $order->user->displayName();
                    }

                    return response()->json([
                        'success' => 'Erfolgreich!',
                        'orders' => $orders
                    ]);

                //zeige alle spezifischen Order
                case $user->isTeacher():
                    if ($request["onlyYours"]) {
                        $orders = Order::where([
                            ['from', ">=", $from],
                            ['to', "<=", $to],
                        ])
                            ->whereIn('id', $user->orders->pluck('id'))
                            ->orderBy('from')
                            ->orderBy('to')
                            ->get();
                    } else {
                        $allOrdersBetween = Order::where([
                            ['from', ">=", $from],
                            ['to', "<=", $to],
                        ])
                            ->orderBy('from')
                            ->orderBy('to')
                            ->get();

                        $belongingOrders = $user->belongingOrders()->pluck('id');
                        $orders = collect();
                        foreach ($allOrdersBetween as $order) {
                            if ($belongingOrders->contains($order->id))
                                $orders->add($order);
                        }
                    }
                    foreach ($orders as $order) {
                        $order->status = $order->status()[2];
                        $order->userDisplayName = $order->user->displayName();
                    }

                    return response()->json([
                        'success' => 'Erfolgreich!',
                        'orders' => $orders
                    ]);

                //zeige nur eigene Order
                case $user->isStudent():
                    $orders = Order::where([
                        ['from', ">=", $from],
                        ['to', "<=", $to],
                    ])
                        ->whereIn('id', $user->orders->pluck('id'))
                        ->orderBy('from')
                        ->orderBy('to')
                        ->get();

                    foreach ($orders as $order) {
                        $order->status = $order->status()[2];
                        $order->userDisplayName = $order->user->displayName();
                    }

                    return response()->json([
                        'success' => 'Erfolgreich!',
                        'orders' => $orders
                    ]);
            }
        } else return response()->json([
            'error' => 'Benutzer nicht gefunden.',
        ], 400);
    }

    public function show($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    $qrCodes = collect();
                    foreach ($order->devices as $device) {
                        $array = array();
                        array_push($array, $device->id);
                        array_push($array, base64_encode(QrCode::size(250)->margin(2)->encoding('UTF-8')->errorCorrection('H')->generate($device->qr_id)));
                        $qrCodes->add($array);
                    }

                    switch (true) {
                        //admins and teachers are seeing the same
                        case $user->isAdmin():
                            return view('teacher.orders.show')
                                ->with('qrCodes', $qrCodes)
                                ->with('order', $order);
                        case $user->isTeacher():
                            if ($user->belongingOrders()->pluck('id')->contains($order->id)) {
                                return view('teacher.orders.show')
                                    ->with('qrCodes', $qrCodes)
                                    ->with('order', $order);
                            } else
                                return view('student.orders.show')
                                    ->with('qrCodes', $qrCodes)
                                    ->with('order', $order);
                        case $user->isStudent():
                            return view('student.orders.show')
                                ->with('qrCodes', $qrCodes)
                                ->with('order', $order);
                    }
                } else
                    return redirect(route('index'))->withErrors($response->message());
            } else
                return redirect(route('index'))->withErrors('Keine Bestellung gefunden.');
        } else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }


    public function accept($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    if ($order->answer !== false) {
                        $order->answer = true;
                        $order->answered_by = $user->id;
                        $order->save();

                        //send mail
                        SendOrderAccepted::dispatch($order)->onConnection('database');
                        //Mail::to($order->user)->send(new OrderAccepted($order));
                        return redirect()->route('orders.show', $order->id);
                    } else
                        return redirect(route('orders.show', $id))->withErrors('Bestellung wurde bereits abgelehnt.');
                } else
                    return redirect(route('index'))->withErrors($response->message());
            } else
                return redirect(route('index'))->withErrors('Keine Bestellung gefunden.');
        } else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }

    public function deny(Request $request, $id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    try {
                        $this->validate($request,
                            [
                                'comment' => 'required',
                            ]
                        );

                        $order->answer = false;
                        $order->answered_by = $user->id;
                        $order->comment = $request->comment;
                        $order->save();
                        $order->refresh();

                        //falls manche geräte schon gescannt wurden, muss man diese wieder zurücksetzen.
                        foreach ($order->devices as $device) {
                            $device->pivot->out_scan = null;
                            $device->pivot->note_before = null;

                            //sollte sowieso null sein
                            $device->pivot->back_scan = null;
                            $device->pivot->note_after = null;
                            $device->pivot->save();
                        }

                        //send mail
                        SendOrderDenied::dispatch($order)->onConnection('database');
                        //Mail::to($order->user)->send(new OrderDenied($order));

                        return redirect()->route('orders.show', $id);
                    } catch (\Exception $ex) {
                        redirect(route('orders.show', $id))->withErrors("Sie müssen einen Kommentar abgeben!");
                    }
                } else
                    return redirect(route('index'))->withErrors($response->message());
            } else
                return redirect(route('index'))->withErrors('Keine Bestellung gefunden.');
        } else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }

    public function handleQRCodeScan(Request $request)
    {
        //find user
        $user = User::all()->find(Auth::id());
        if ($user) {
            //find order
            $order = Order::all()->find($request->order_id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    if ($order->answer == true) {
                        //find device in order
                        $device = $order->devices()->where('device_id', $request->device_id)->first();
                        if ($device) {
                            //order hat noch kein picked_at value -> wird jetzt ausgegeben
                            if (!$order->picked_at) {
                                $device->pivot->out_scan = Carbon::now();
                                $device->pivot->note_before = $request->note_before;
                            } else {
                                $device->pivot->back_scan = Carbon::now();
                                $device->pivot->note_after = $request->note_after;
                            }
                            $device->push();

                            return response()->json([
                                'success' => 'Gerät erfolgreich gescannt.',
                                'devices' => $order->devices
                            ]);
                        } else
                            return response()->json(['error' => 'Gerät in Bestellung nicht gefunden.'], 400);
                    } else
                        return response()->json(['error' => 'Bestellung zuerst bestätigen.'], 400);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Bestellung nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function undoScan(Request $request)
    {
        //find user
        $user = User::all()->find(Auth::id());
        if ($user) {
            //find order
            $order = Order::all()->find($request->order_id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    if ($order->answer == true) {
                        //find device in order
                        $device = $order->devices()->where('device_id', $request->device_id)->first();
                        if ($device) {
                            if ($request->isOutScan == "true") {
                                $device->pivot->out_scan = null;
                                $device->pivot->note_before = null;
                            } else if ($request->isOutScan == "false") {
                                $device->pivot->back_scan = null;
                                $device->pivot->note_after = null;
                            }
                            $device->push();

                            return response()->json([
                                'success' => 'Gerät erfolgreich entscannt.',
                                'devices' => $order->devices
                            ]);
                        } else
                            return response()->json(['error' => 'Gerät in Bestellung nicht gefunden.'], 400);
                    } else
                        return response()->json(['error' => 'Bestellung zuerst bestätigen.'], 400);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Bestellung nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function pick($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    foreach ($order->devices as $device) {
                        //nur wenn auch alle Geräte out_scanned sind
                        if (!$device->pivot->out_scan)
                            return redirect(route('orders.show', $order->id))->withErrors('Es sind noch nicht alle Geräte gescannt worden!');

                        //schreibe pivot kommentare zu Gerätenotizen..
                        $device->note = $device->pivot->note_before;
                        $device->save();
                    }

                    if (Carbon::today() < $order->from)
                        $order->from = Carbon::today();

                    $order->picked_at = Carbon::now();
                    $order->given_by = $user->id;
                    $order->save();
                    return redirect()->route('orders.show', $order->id);
                } else
                    return redirect(route('index'))->withErrors($response->message());
            } else
                return redirect(route('index'))->withErrors('Keine Bestellung gefunden.');
        } else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }

    public function return($id)
    {
        //todo sollte man den Wert "to" auf den returned wert setzen, falls man es schon früher zurückgibt?
        //wäre nämlich sinnvoll in Bezug auf die Filteroption, da die Gerät dann wieder freigegeben wären.
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    foreach ($order->devices as $device) {
                        //nur wenn auch alle Geräte out_scanned sind
                        if (!$device->pivot->back_scan)
                            return redirect(route('orders.show', $order->id))->withErrors('Es sind noch nicht alle Geräte gescannt worden!');

                        //schreibe pivot kommentare zu Gerätenotizen..
                        $device->note = $device->pivot->note_after;
                        $device->save();
                    }


                    if (Carbon::today() < $order->to)
                        $order->to = Carbon::today();

                    $order->returned_at = Carbon::now();
                    $order->returned_by = $user->id;
                    $order->save();
                    return redirect()->route('orders.show', $order->id);
                } else
                    return redirect(route('index'))->withErrors($response->message());
            } else
                return redirect(route('index'))->withErrors('Keine Bestellung gefunden.');
        } else
            return redirect(route('index'))->withErrors('Keinen Benutzer gefunden.');
    }

    public
    function removeDevice(Request $request)
    {
        //find user
        $user = User::all()->find(Auth::id());
        if ($user) {
            //find order
            $order = Order::all()->find($request->order_id);
            if ($order) {
                $response = Gate::inspect('view', $order);
                if ($response->allowed()) {
                    //nur ermöglichen wenn order noch nicht ausgegeben
                    if (!$order->picked_at) {
                        //find device in order
                        $device = $order->devices()->where('device_id', $request->device_id)->first();
                        if ($device) {
                            $order->devices()->detach($device->id);

                            $order->refresh();
                            //delete when no device is in order
                            if ($order->devices->count() == 0)
                                $order->delete();

                            return response()->json([
                                'success' => 'Gerät erfolgreich entfernt.',
                                'devices' => $order->devices
                            ]);
                        } else
                            return response()->json(['error' => 'Gerät in Bestellung nicht gefunden.'], 400);
                    } else
                        return response()->json(['error' => 'Entfernen nicht mehr möglich.'], 400);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Bestellung nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public
    function destroy($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $order = Order::all()->find($id);
            if ($order) {
                $response = Gate::inspect('delete', $order);
                if ($response->allowed()) {
                    $order->devices()->detach();
                    $order->delete();
                    return response()->json([
                        'success' => 'Bestellung erfolgreich gelöscht.',
                    ]);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Bestellung nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public
    function finish(Request $request)
    {
        //wird aufgerufen, wenn der Schüler den Kauf bestätigt.
        //Dann werden die Geräte vom Warenkorb  in den Order geladen.
        $user = User::all()->find(Auth::id());
        if ($user) {
            if ($request->checked == true) {
                $response = Gate::inspect('create', Order::class);
                if ($response->allowed()) {
                    if ($user->from == null || $user->to == null)
                        return redirect(route("orders.index"));
                    else {
                        $order = new Order();
                        $order->user_id = $user->id;
                        $order->from = $user->from;
                        $order->to = $user->to;
                        $order->save();

                        //reset users preferences
                        $user->from = null;
                        $user->to = null;
                        $user->save();

                        $order->devices()->attach($user->shoppingCart()->pluck('device_id'));
                        //clear shoppingCart
                        $user->clearShoppingCart();

                        $order->refresh();

                        //send mails
                        SendOrderMade::dispatch($order)->onConnection('database');
                        SendOrderNotificationTeacher::dispatch($order)->onConnection('database');;
                        //Mail::to($order->user)->send(new OrderMade($order));
                        //Mail::to($order->belongingTeachers())->send(new OrderNotificationTeacher($order));

                        return redirect()->route('orders.show', $order->id);
                    }
                } else
                    return redirect(route("index"))->withErrors($response->message());
            } else
                return redirect(route("index"))->withErrors('Richtlinien nicht aktzeptiert.');
        } else
            return redirect()->route('index')
                ->withErrors('Benutzer nicht gefunden');
    }
}
