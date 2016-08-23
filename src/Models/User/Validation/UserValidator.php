<?php

namespace Nodes\Backend\Models\User\Validation;

use Nodes\Validation\AbstractValidator;

/**
 * Class UserValidation.
 */
class UserValidator extends AbstractValidator
{
    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'create' => [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:backend_users,email,{:id}', 'max:190'],
            'password' => ['required_without:id', 'min:6', 'confirmed'],
            'user_role' => ['required', 'exists:backend_roles,slug'],
            'image' => ['mimes:jpeg,png']
        ],
        'update-password' => [
            'password' => ['required_without:id', 'min:6', 'confirmed'],
            'image' => ['mimes:jpeg,png']
        ],
    ];
}
