<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'grade_level',
        'section',
        'capacity',
        'is_active',
        'academic_year_id',
        'class_teacher_id',
        'created_by',
    ];

    protected $casts = [
        'grade_level' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the academic year this class belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the class teacher (main teacher for this class).
     * @deprecated Use teachers() relationship for multiple teachers
     */
    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    /**
     * Get all teachers assigned to this class.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_teacher', 'class_id', 'teacher_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Get the primary teacher for this class.
     */
    public function primaryTeacher()
    {
        return $this->teachers()->wherePivot('is_primary', true)->first();
    }

    /**
     * Get the user who created this class.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }

    /**
     * Get all subjects taught in this class.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    /**
     * Get all exams for this class.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Scope to get only active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full class name with grade and section.
     */
    public function getFullNameAttribute(): string
    {
        return $this->section ? 
            "Grade {$this->grade_level}-{$this->section}" : 
            "Grade {$this->grade_level}";
    }
}