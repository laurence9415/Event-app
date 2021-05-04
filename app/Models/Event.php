<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'from',
        'to',
        'days'
    ];

    public function setDaysAttribute($value)
    {
        return $this->attributes['days'] = json_encode($value);
    }

    public function getDaysAttribute($value)
    {
        return $this->attributes['days'] = json_decode($value);
    }
}
