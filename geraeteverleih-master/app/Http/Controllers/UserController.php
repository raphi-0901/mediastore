<?php

namespace App\Http\Controllers;

use App\Device;
use App\Order;
use App\Setting;
use App\Type;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all()->find(Auth::id());

        if ($user->isAdmin()) {
            return view('admin.users.index')
                ->with('types', Type::all())
                ->with('students', User::students())
                ->with('teachers', User::teachers());
        } else {
            return redirect()->route('index')
                ->withErrors('Keine Berechtigung!');
        }
    }

    public function show($id)
    {
        $user = User::all()->find($id);
        if ($user) {
            $response = Gate::inspect('view', $user);
            if ($response->allowed()) {
                return view('admin.users.show')
                    ->with('user', $user);
            } else return redirect()->route('index')
                ->withErrors($response->message());
        } else return redirect()->route('index')
            ->withErrors('Benutzer nicht gefunden!');
    }

    public function showShoppingCart()
    {
        $user = User::all()->find(Auth::id());
        if ($user->isStudent() || $user->isTeacher()) {
            return view('student.shoppingCart.index')
                ->with('user', $user)
                ->with('devices', $user->shoppingCart);
        } else
            return redirect()->route('index')->withErrors('Keine Berechtigung zum Ausleihen.');
    }

    public function clearShoppingCart()
    {
        $user = User::all()->find(Auth::id());
        if ($user->isStudent()) {
            $user->shoppingCart()->detach();
            return redirect()->route('shoppingCart.show');
        } else
            return redirect()->route('index');
    }

    public function update(Request $request, $id)
    {
        $user = User::all()->find($id);
        if ($user) {
            $response = Gate::inspect('delete', $user);
            if ($response->allowed()) {
                try {
                    $user->types()->sync($request->types);
                } catch (\Exception $ex) {
                    return response()->json(['error' => 'Nicht erfolgreich.'], 400);
                }
                return response()->json([
                    'success' => 'Benutzer erfolgreich aktualisiert.',
                    'user' => $user
                ]);
            } else
                return response()->json(['error' => $response->message()], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function destroy($id)
    {
        $user = User::all()->find($id);
        if ($user) {
            $response = Gate::inspect('delete', $user);
            if ($response->allowed()) {
                $user->delete();
                return response()->json(['success' => 'Benutzer erfolgreich gelöscht']);
            } else
                return response()->json(['error' => $response->message()], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function deletedUsers()
    {
        $users = User::onlyTrashed()->get();
        if ($users) {
            $response = Gate::inspect('admin');
            if ($response->allowed())
                return view("admin.users.deletedUsers")->with('users', $users);
            else
                return redirect(route("index"))->withErrors($response->message());
        } else
            return redirect(route("index"))->withErrors("Benutzer nicht gefunden.");
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        if ($user) {
            $response = Gate::inspect('admin');
            if ($response->allowed()) {
                $user->restore();
                return response()->json(['success' => 'Benutzer erfolgreich wiederhergestellt.']);
            } else
                return response()->json(['error' => $response->message()], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->find($id);
        if ($user) {
            $response = Gate::inspect('delete', $user);
            if ($response->allowed()) {
                $user->types()->detach();
                $user->shoppingCart()->detach();
                foreach ($user->orders as $order) {
                    $order->devices()->detach();
                    $order->delete();
                }
                $user->forceDelete();
                return response()->json(['success' => 'Benutzer komplett gelöscht']);
            } else
                return response()->json(['error' => $response->message()], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public function storeDates(Request $request)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            if ($user->isStudent() || $user->isTeacher()) {
                try {
                    $this->validate($request,
                        [
                            'from' => 'required',
                            'to' => 'required',
                        ]
                    );

                    if ($user->shoppingCart->count() == 0) {
                        $user->from = Carbon::create($request->from);
                        $user->to = Carbon::create($request->to);
                        $user->save();

                        //return to categories.
                        //return redirect()->route('index');
                        return response()->json([
                            'success' => 'Erfolgreich hinzugefügt!'
                        ]);
                    } else
                        return response()->json([
                            'error' => 'Warenkorb nicht leer!'
                        ], 400);
                } catch
                (\Exception $ex) {
                    return response()->json([
                        'error' => 'Konnte nicht speichern!'
                    ], 400);
                }
            } else
                return response()->json(['error' => 'Admin kann nichts bestellen.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }

    public
    function addToShoppingCart(Request $request)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            if ($user->isStudent() || $user->isTeacher()) {
                if (Device::availableDevices($user->from, $user->to)->contains($request->id)) {
                    $maxDeviceCount = Setting::all()->find('maxDeviceCount')->value;
                    if ($user->shoppingCart->count() < $maxDeviceCount) {
                        try {
                            $user->shoppingCart()->attach($request->id, ['from' => $user->from, 'to' => $user->to]);
                        } //duplicate entry. passiert wenn ein user ungeduldig ist.
                        catch (\Exception $ex) {
                        }

                        $user->refresh(); //sonst zählt der shoppingcart counter nicht hoch.
                        return response()->json([
                            'success' => 'Gerät wurde erfolgreich hinzugefügt!',
                            'sCount' => $user->shoppingCart->count()
                        ]);
                    } else
                        return response()->json([
                            'error' => 'Maximale ' . $maxDeviceCount . ' Geräte erlaubt.',
                        ], 400);
                } else
                    return response()->json([
                        'error' => 'Gerät leider schon vergriffen.',
                    ], 400);
            } else
                return response()->json([
                    'error' => 'Nicht berechtigt zum Ausleihen.',
                ], 400);
        } else
            return response()->json(['error' => 'Keinen Benutzer gefunden.'], 400);
    }

    public
    function removeFromShoppingCart(Request $request)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            if ($user->isStudent() || $user->isTeacher()) {
                try {
                    $user->shoppingCart()->detach($request->id);
                } catch (\Exception $ex) {
                }
                return response()->json(['success' => 'Gerät wurde erfolgreich entfernt!',
                    'sCount' => $user->shoppingCart->count()]);
            } else
                return response()->json([
                    'error' => 'Nicht berechtigt zum Ausleihen.',
                ], 400);
        } else
            return response()->json(['error' => 'Keinen Benutzer gefunden.'], 400);
    }
}
