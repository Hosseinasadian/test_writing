<?php

namespace App\Models;

use App\Helpers\DurationOfReading;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id','title','image','description'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments():MorphMany
    {
        return $this->morphMany(Comment::class,'commentable');
    }

    public function readingDuration():Attribute
    {
        return new Attribute(get:function(mixed $value, array $attributes){
            return app(DurationOfReading::class)
                ->setText($attributes['description'])
                ->getTimePerMinute();
        });
    }
}
