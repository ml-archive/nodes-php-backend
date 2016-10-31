<?php


namespace Nodes\Backend\Auth\ResetPassword\Validation;

use Nodes\Validation\AbstractValidator;

/**
 * Class ResetPasswordValidator
 *
 */
class ResetPasswordValidator extends AbstractValidator
{
    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'create' => [
            'token'    => ['required', 'exists:backend_reset_password_tokens,token,used,0'],
            'email'    => ['required', 'exists:backend_users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ],
    ];
}
