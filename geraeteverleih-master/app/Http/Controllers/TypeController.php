<?php

namespace App\Http\Controllers;

use App\Device;
use App\Type;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;

class TypeController extends Controller
{
    public function index()
    {
        $user = User::all()->find(Auth::id());
        if ($user->isAdmin()) {
            $types = Type::orderBy('name')->get();
            return view('admin.types.index')->with('types', $types);
        } else {
            return redirect()->route('index')
                ->withErrors('Cant show Users!');
        }
    }

    public function store(Request $request)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $response = Gate::inspect('create', Type::class);
            if ($response->allowed()) {
                $isOk = false;
                //is new parentType
                if ($request->parent_id == null)
                    $isOk = true;
                else {
                    //find toptype
                    $pType = Type::all()->find($request->parent_id);
                    if ($pType)
                        $isOk = true;
                }

                if ($isOk) {
                    try {
                        $this->validate($request,
                            [
                                'name' => 'required',
                            ]
                        );
                        $type = new Type();
                        $type->name = $request->name;
                        $type->parent_id = $request->parent_id;
                        $type->save();
                        $type->refresh();

                        $types = Type::orderBy('name')->get();
                        return response()->json([
                            'success' => 'Erfolgreich erstellt!',
                            'type' => $type,
                            'types' => $types
                        ]);
                    } catch (\Exception $ex) {
                        return response()->json(['error' => 'Überprüfe Eingabe.'], 400);
                    }
                } else return response()->json(['error' => 'Überkategorie nicht gefunden.'], 400);
            } else return response()->json(['error' => $response->message()], 400);
        } else return response()->json(['error' => 'Benutzer nicht gefunden'], 400);
    }

    public
    function update(Request $request, $id)
    {
        $user = User::all()->find(Auth::id());

        if ($user) {
            $type = Type::all()->find($id);
            if ($type) {
                $response = Gate::inspect('update', $type);
                if ($response->allowed()) {
                    $isOk = false;

                    //is new parentType
                    if ($request->parent_id == null)
                        $isOk = true;
                    else {
                        $pType = Type::all()->find($request->parent_id);

                        //it is not allowed that the new parent_id is a subtype from actual type
                        //and parent id cannot be itself
                        if (!$pType->isSubTypeOf($type) && $request->parent_id != $type->id)
                            $isOk = true;
                    }

                    if ($isOk) {
                        try {
                            $this->validate($request,
                                [
                                    'name' => 'required',
                                ]
                            );
                            $type->name = $request->name;
                            $type->parent_id = $request->parent_id;
                            $type->save();

                            $types = Type::orderBy('name')->get();
                            return response()->json([
                                'success' => 'Erfolgreich aktualisiert!',
                                'type' => $type,
                                'types' => $types
                            ]);
                        } catch (\Exception $ex) {
                            return response()->json([
                                'error' => 'Überprüfe Eingabe!',
                            ], 400);
                        }
                    } else
                        return response()->json([
                            'error' => 'Falsche Eingaben!',
                        ], 400);
                } else
                    return response()->json([
                        'error' => $response->message(),
                    ], 400);
            } else
                return response()->json([
                    'error' => 'Kategorie nicht gefunden',
                ], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden',], 400);
    }

    public
    function destroy($id)
    {
        $user = User::all()->find(Auth::id());
        if ($user) {
            $type = Type::all()->find($id);
            if ($type) {
                $response = Gate::inspect('delete', $type);
                if ($response->allowed()) {
                    try {
                        if ($type->children->count() == 0 && $type->devices->count() == 0)
                            $type->delete();
                        else {
                            $errorMessage = '';
                            if ($type->devices->count() != 0)
                                $errorMessage .= 'Es gibt noch ' . $type->devices->count() . ' Geräte mit dieser Kategorie.<br>';

                            if ($type->children->count() != 0)
                                $errorMessage .= 'Es gibt noch ' . $type->children->count() . ' Kategorien als Unterkategorien.<br>';
                            $errorMessage .= '<br>Löschen Sie diese zuerst, bevor Sie die Kategorie löschen.';

                            return response()->json(['error' => $errorMessage], 400);
                        }
                    } catch (\Exception $exception) {
                        return response()->json(['error' => 'Kategorie konnte nicht gelöscht werden.'], 400);
                    }

                    $types = Type::orderBy('name')->get();
                    return response()->json(['success' => 'Kategorie erfolgreich gelöscht.',
                        'types' => $types]);
                } else
                    return response()->json(['error' => $response->message()], 400);
            } else
                return response()->json(['error' => 'Kategorie nicht gefunden.'], 400);
        } else
            return response()->json(['error' => 'Benutzer nicht gefunden.'], 400);
    }
}
