<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UpdateLog extends Model
{
    use HasFactory;
    protected $with = ['user'];
    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'user_id',
        'field',
        'from',
        'to',
    ];

    protected $appends = [
        'user_name',
        'field_name',
        'formatted_date',
        'formatted_time',
    ];

    public function getFieldNameAttribute()
    {
        switch ($this->field) {

            default:
                return Str::title(str_replace('_', ' ', $this->field));
                break;
        }
    }

    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F d, Y') ?? '';
    }
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('h:iA') ?? '';
    }

    // Override the getLoggableTypeAttribute method to return the class name without the namespace
    public function getLoggableTypeAttribute()
    {
        return class_basename($this->attributes['loggable_type']);
    }
}
