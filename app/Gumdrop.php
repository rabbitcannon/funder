<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(required={"name","color"}, type="object", @SWG\Xml(name="Gumdrop"))
 * @SWG\Property(format="int64", property="id", example=21, description="The gumdrop identifier.")
 * @SWG\Property(format="string", property="name", example="Bobby", description="Unique name of the gumdrop.")
 * @SWG\Property(format="string", property="color", example="red", description="The gumdrop color.")
 **/
class Gumdrop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color'
    ];
}

/**
 * just for Swagger
 * @SWG\Definition(required={"name","color"}, type="object", @SWG\Xml(name="GumdropAndUser"))
 * @SWG\Property(format="int64", property="user_id", example=3, description="The user to link to the gumdrop.")
 * @SWG\Property(format="string", property="name", example="Billy", description="Unique name of the gumdrop.")
 * @SWG\Property(format="string", property="color", example="blue", description="The gumdrop color.")
 **/
class GumdropAndUser {}