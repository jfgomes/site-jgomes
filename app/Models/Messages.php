<?php

namespace App\Models;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as Validatior2;

class Messages extends Model
{
    protected $table = 'messages';
    public $timestamps = false;
    protected $fillable = [
        "name",
        "email",
        "subject",
        "content",
        "created_at"
    ];

    /**
     * @param array $data
     * @return Validator
     */
    public static function validateData(array $data): Validator
    {
        // Define validation rules
        $rules = [
            'name'    => 'required|string|max:50',
            'email'   => 'required|email|max:50',
            'subject' => 'nullable|string|max:100',
            'content' => 'required|string|max:2000',
        ];

        return Validatior2::make($data, $rules);
    }
}
