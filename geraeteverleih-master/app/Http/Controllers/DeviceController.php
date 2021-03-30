<?php

namespace App\Http\Controllers;

use App\Device;
use App\Imports\DevicesImport;
use App\Order;
use App\Type;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceController extends Controller
{
    public function index()
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            switch (true) {
                case $user->isAdmin():
                    return view('admin.devices.index')
                        ->with('types', Type::all())
                        ->with('allTypes', Type::all())
                        ->with('devices', Device::all());
                case $user->isTeacher():
                    if ($user->types->count() == 0)
                        return redirect()->route('index')->withErrors('Keine Rechte');
                    else {
                        $types = $user->getRecursiveTypes();
                        return view('admin.devices.index')
                            ->with('types', $types)
                            ->with('allTypes', Type::all())
                            ->with('devices', $user->belongingDevices());
                    }
                case $user->isStudent():
                    return redirect()->route('index')->withErrors('Keine Rechte');
            }
        } else
            return redirect()->route('index')->withErrors('Benutzer nicht gefunden');
    }

    public function filter()
    {
        $request = $_GET;
        //in view: remove all existing products and replace with new ones.
        $user = User::all()->find(Auth::id());
        if ($user) {
            $devices = Device::availableDevicesInTypes($user->from, $user->to, $request["filter"], $request["sortBy"]);
            return response()->json([
                'success' => 'Erfolgreich!',
                'devices' => $devices
            ]);
        } else return response()->json([
            'error' => 'Benutzer nicht gefunden.',
        ], 400);
    }

    public function show($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $device = Device::withTrashed()->find($id);
            $type = Type::all()->find($device->type_id);
            if ($device) {
                $ordersFrom = $device->orders()->pluck('from');
                $ordersTo = $device->orders()->pluck('to');

                //Daten, in denen das Gerät nicht verfügbar ist.
                $dates = collect();
                for ($i = 0; $i < count($ordersTo); $i++) {
                    $from = $ordersFrom[$i];
                    while (true) {
                        $from = Carbon::parse($from);
                        $dates->add($from->format('Y-m-d'));

                        if ($from >= $ordersTo[$i])
                            break;

                        $from->addDay();
                    }
                }

                //Daten, in denen das Gerät zurzeit im Warenkorb ist. (wird anders markiert)
                $shoppingCart = collect();
                foreach ($device->shoppingCart as $sc) {
                    $from = $sc->pivot->from;
                    while (true) {
                        $from = Carbon::parse($from);
                        $shoppingCart->add($from->format('Y-m-d'));

                        if ($from >= $sc->pivot->to)
                            break;

                        $from->addDay();
                    }
                }
                return view('admin.devices.show')->with('device', $device)->with('unavailableDays', $dates)->with('shoppingCart', $shoppingCart)->with('type', $type);
            } else
                return redirect()->route('index')->withErrors('Gerät nicht gefunden');
        } else
            return redirect()->route('index')->withErrors('Benutzer nicht gefunden');
    }

    public function store(Request $request)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $response = Gate::inspect('create', Device::class);
            if ($response->allowed()) {
                try {
                    $this->validate($request,
                        [
                            'name' => 'required',
                            'type_id' => 'required',
                            'qr_id' => 'required',
                        ]
                    );

                    if ($user->isTeacher()) {
                        $types = $user->getRecursiveTypes()->pluck('id');
                        if (!$types->contains($request->type_id))
                            return response()->json(['error' => 'Keine Berechtigung für Kategorie.'], 400);
                    }

                    $devices = collect();
                    $count = $request->count;
                    for ($i = 0; $i < $count; $i++) {
                        $device = new Device();
                        $device->name = $request->name;
                        $device->description = $request->description;
                        $device->serial = $request->serial;
                        $device->qr_id = $request->qr_id;
                        $device->note = $request->note;
                        $device->type_id = $request->type_id;
                        $device->save();

                        $devices->add($device);
                    }

                    return response()->json([
                        'success' => 'Erfolgreich erstellt!',
                        'devices' => $devices->toArray(),
                        'type' => $device->type
                    ]);
                } catch (\Exception $ex) {
                    return response()->json(['error' => 'Gerät erstellen fehlgeschlagen.'], 400);
                }
            }
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $device = Device::all()->find($id);
            if ($device) {
                $response = Gate::inspect('update', $device);
                if ($response->allowed()) {
                    try {
                        $this->validate($request,
                            [
                                'name' => 'required',
                                'type_id' => 'required',
                                'qr_id' => 'required',
                            ]
                        );

                        $device = Device::all()->find($id);
                        $device->name = $request->name;
                        $device->description = $request->description;
                        $device->serial = $request->serial;
                        $device->qr_id = $request->qr_id;
                        $device->note = $request->note;
                        $device->type_id = $request->type_id;
                        $device->save();
                        $device->status = $device->isAvailable() ? '<span class="badge badge-success text-white">Verfügbar</span>' : '<span class="badge badge-warning text-white">Nicht verfügbar</span>';

                        return response()->json([
                            'success' => 'Erfolgreich aktualisiert!',
                            'device' => $device,
                            'type' => $device->type
                        ]);
                    } catch (\Exception $ex) {
                        return response()->json([
                            'error' => 'Überprüfe Eingabe!',
                        ], 400);
                    }
                } else
                    return response()->json([
                        'error' => $response->message(),
                    ], 400);
            } else
                return response()->json([
                    'error' => 'Gerät nicht gefunden',
                ], 400);
        } else
            return response()->json([
                'error' => 'Benutzer nicht gefunden',
            ], 400);
    }

    public function createFromExcel(Request $request)
    {
        $response = Gate::inspect('create', Device::class);
        if ($response->allowed()) {
            //for multiple files
            $failures = array();
            try {
                foreach ($request->file('devices') as $file) {
                    $foodDaysImport = new DevicesImport();
                    $foodDaysImport->import($file);
                    if ($foodDaysImport->failures()->isNotEmpty())
                        foreach ($foodDaysImport->failures() as $failure)
                            array_push($failures, $failure);
                }
            } catch (\Exception $ex) {
                return back()->withErrors($ex->getMessage());
            }

            if (count($failures) != 0)
                return redirect(route("devices.index"))->with('importedExcel', true)->withFailures($failures);
            else
                return redirect(route("devices.index"))->with('importedExcel', true);
        } else
            return redirect(route("index"))->withErrors($response->message());
    }

    public function destroy($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $device = Device::all()->find($id);
            if ($device) {
                $response = Gate::inspect('delete', $device);
                if ($response->allowed()) {
                    $device->delete();
                    return response()->json(['success' => 'Gerät erfolgreich gelöscht.']);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Gerät nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function deletedDevices()
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            switch (true) {
                case $user->isAdmin():
                    $devices = Device::onlyTrashed()->get();
                    return view("admin.devices.deletedDevices")->with('devices', $devices);
                case $user->isTeacher():
                    $devices = $user->belongingDeletedDevices();
                    return view("admin.devices.deletedDevices")->with('devices', $devices);
                case $user->isStudent():
                    return redirect(route("index"))->withErrors('Keine Berechtigung');
            }
        } else
            return redirect(route("login"))->withErrors("Benutzer nicht gefunden.");
    }

    public function restore($id)
    {
        $user = User::all()->find(Auth::id());
        $device = Device::onlyTrashed()->find($id);
        if ($user) {
            if ($device) {
                $response = Gate::inspect('restore', $device);
                if ($response->allowed()) {
                    $device->restore();
                    return response()->json(['success' => 'Gerät erfolgreich wiederhergestellt.']);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Gerät nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function forceDelete($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $device = Device::onlyTrashed()->find($id);
            if ($device) {
                $response = Gate::inspect('forceDelete', $device);
                if ($response->allowed()) {
                    $orders = $device->orders;
                    $device->orders()->detach();
                    $device->forceDelete();

                    //delete order when no device
                    foreach ($orders as $order)
                        if ($order->devices->count() == 0)
                            $order->delete();
                    return response()->json(['success' => 'Gerät komplett gelöscht']);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Gerät nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function downloadQRCode($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $device = Device::all()->find($id);
            if ($device) {
                $response = Gate::inspect('forceDelete', $device);
                if ($response->allowed()) {
                    try {
                        $path = public_path("/qrCodes/");
                        if (!File::isDirectory($path))
                            File::makeDirectory($path, 0777, true, true);

                        if (!File::exists($path . 'qr_' . $device->qr_id . '.svg')) {
                            QrCode::size(250)
                                ->margin(2)
                                ->encoding('UTF-8')
                                ->errorCorrection('H')
                                ->generate($device->qr_id, $path . 'qr_' . $device->qr_id . '.svg');
                        }
                        return response()->download($path . 'qr_' . $device->qr_id . '.svg')->deleteFileAfterSend(true);
                    } catch (\Exception $ex) {
                    }
                }
            }
        }
    }

    public function downloadQRCodes()
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $path = public_path("/qrCodes/");
            if (!File::isDirectory($path))
                File::makeDirectory($path, 0777, true, true);

            $devices = null;
            switch (true) {
                case $user->isAdmin():
                    $devices = Device::all();
                    break;
                case $user->isTeacher():
                    $devices = $user->belongingDevices();
                    break;
                case $user->isStudent():
                    $devices = null;
                    break;
            }
            $zip = new \ZipArchive();
            $zipName = "qrcodes_" . Carbon::now()->format('His') . '.zip';
            if ($zip->open($path . $zipName, \ZipArchive::CREATE) === TRUE) {
                foreach ($devices as $device) {
                    if (!File::exists($path . 'qr_' . $device->qr_id . '.svg')) {
                        QrCode::size(250)
                            ->margin(2)
                            ->encoding('UTF-8')
                            ->errorCorrection('H')
                            ->generate($device->qr_id, $path . 'qr_' . $device->qr_id . '.svg');
                    }
                    $zip->addFile($path . 'qr_' . $device->qr_id . '.svg', 'qr_' . $device->qr_id . '.svg');
                }
                $zip->close();

                foreach ($devices as $device) {
                    if (File::exists($path . 'qr_' . $device->qr_id . '.svg'))
                        File::delete($path . 'qr_' . $device->qr_id . '.svg');
                }
            }
            return response()->download($path . $zipName)->deleteFileAfterSend(true);
        }
    }
}
