<?php

namespace App\Imports;

use App\Device;
use App\User;
use App\Type;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DevicesImport implements ToModel,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    SkipsOnFailure
{

    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        $device = new Device();
        $device->name = $row["name"];
        $device->description = $row["beschreibung"];
        $device->serial = $row["seriennummer"];
        $device->qr_id = $row["qr_id"];
        $device->type_id = Type::where('id', $row["kategorie"])->orWhere('name', $row["kategorie"])->first()->id;
        $device->save();
        return $device;
    }

    public function rules(): array
    {
        return [
            '*.name' => function ($attribute, $value, $onFailure) {
                if (!$value)
                    $onFailure("Name darf nicht leer sein!");
            },
            '*.qr_id' => function ($attribute, $value, $onFailure) {
                if (!$value)
                    $onFailure("QR-ID darf nicht leer sein!");
            },
            '*.kategorie' => function ($attribute, $value, $onFailure) {
                $type = Type::where('id', $value)->orWhere('name', $value)->first();
                if (!$type)
                    $onFailure("Kategorie nicht gefunden!");
                else
                {
                    $isAble = false;
                    $user = User::all()->find(Auth::id());

                    if ($user->isAdmin())
                        $isAble = true;

                    if ($user->isTeacher()) {
                        foreach ($user->types as $uType) {
                            if ($type->id == $uType->id || $type->isSubTypeOf($uType))
                            {
                                $isAble = true;
                                break;
                            }
                        }
                    }

                    if(!$isAble)
                        $onFailure("Keine Berechtigung für " . $type->name . "!");
                }
            },
        ];
    }
}
