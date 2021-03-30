<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public function parent()
    {
        return $this->belongsTo(Type::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Type::class, 'parent_id')->orderBy('name');
    }

    public function hasParent()
    {
        return $this->belongsTo(Type::class, 'parent_id')->exists();
    }

    public function hasChildren()
    {
        return $this->hasMany(Type::class, 'parent_id')->exists();
    }

    //a user can belong to several categories
    public function users()
    {
        return $this->belongsToMany(User::class, 'type_user')->withTimestamps();
    }

    //a category can have many devices
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public static function onlySubTypes()
    {
        //return Type::whereNotIn('id', self::onlyParentTypes()->pluck('id'))->get();
        return Type::where('parent_id', '!=', null)->orderBy('name')->get();
    }

    public static function findIdByName($name)
    {
        return Type::where('name', $name)->first()->id;
    }

    public static function onlyParentTypes()
    {
        return Type::where('parent_id', '=', null)->orderBy('name')->get();
    }

    public function isSubTypeOf($type)
    {
        if ($type->id == $this->id)
            return true;

        return $this->recursive($type);
    }

    private function recursive($type)
    {
        if ($type->id == $this->id)
            return true;

        foreach ($type->children as $child)
            if ($this->recursive($child))
                return true;
    }

    public function getTopType()
    {
        $type = $this;
        while (true)
        {
            if(!$type->parent)
                return $type;

            $type = $type->parent;
        }
    }

    public function getAllSubtypes()
    {
        $allTypes = Type::all();
        $types = collect();
        foreach ($allTypes as $allTypeKey => $allType) {
                if ($allType->id === $this->id || $allType->isSubTypeOf($this))
                    $types->add($allType);
            }
        return $types->unique();
    }
}
