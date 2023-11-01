<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Thread extends Model
{
    use HasFactory, HasUuids;

    // public $incrementing = false;
    // protected $keyType = 'string';

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread_category()
    {
        return $this->belongsTo(ThreadCategory::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/threads/' . $image),
        );
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($thread) {
            $thread->id = Str::uuid()->toString(); // Membuat UUID baru
        });
    }

}
