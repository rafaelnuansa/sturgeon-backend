<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScientificWork extends Model
{
    use HasFactory, HasUuids;

    protected $guraded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function attachment(): Attribute
    {
        return Attribute::make(
            get: fn ($attachment) => url('/storage/files/' . $attachment),
        );
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }


}
