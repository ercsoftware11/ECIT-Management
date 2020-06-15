<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
         'name', 'address', 'abn', 'phone', 'email', 'web', 'primary_contact',
    ];

    public function path()
    {
        return '/business' . '/' . $this->id;
    }

    public function primary_contact()
    {
        return $this->belongsTo('App\User', 'primary_contact_id');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

}
