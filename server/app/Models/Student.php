<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $table = 'students';

    protected $primaryKey = 'Student_ID';

    public $timestamps = false;

    protected $fillable = [
        'Email',
        'Name',
        'Phone',
        'Image',
    ];

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'school',
            'Student_ID',
            'Course_ID'
        );
    }
}
