<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ThreadCategory extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = ['id'];

    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }


    protected static function booted()
    {
        parent::booted();

        static::creating(function ($thread) {
            $thread->id = Str::uuid()->toString(); // Membuat UUID baru
        });
    }

    public function threads()
    {
        $this->hasMany(Thread::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/thread_categories/' . $image),
        );
    }
}
