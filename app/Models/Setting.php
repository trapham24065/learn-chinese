<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Setting types
    const TYPE_TEXT     = 'text';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_INTEGER  = 'integer';
    const TYPE_DATETIME = 'datetime';
    const TYPE_SELECT   = 'select';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
        'is_public',
        'is_editable',
        'sort_order',
    ];

    protected $casts = [
        'is_public'   => 'boolean',
        'is_editable' => 'boolean',
        'sort_order'  => 'integer',
    ];
}
