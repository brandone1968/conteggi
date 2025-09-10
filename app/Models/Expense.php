<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'amount', 'date', 'user_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
