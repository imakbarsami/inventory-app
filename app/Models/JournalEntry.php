<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'date',
        'description',
        'reference_id' 
    ];

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class);
    }
}
