<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
    protected $guarded= [];
    //Add the below function
    public function user_to()
    {
        return $this->belongsTo(User::class,'to_id');
    }
    public function user_from()
    {
        return $this->belongsTo(User::class,'from_id');
    }
}
