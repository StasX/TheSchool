<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $table = 'courses';

    protected $primaryKey = 'Course_ID';

    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Description',
        'Image',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'school',
            'Course_ID',
            'Student_ID'
        );
    }
}
