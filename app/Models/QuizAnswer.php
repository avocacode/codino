<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['submission_id', 'question_id', 'answer_text', 'selected_option', 'file_path', 'points_awarded'])]
class QuizAnswer extends Model
{
    public function submission(): BelongsTo
    {
        return $this->belongsTo(QuizSubmission::class, 'submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
