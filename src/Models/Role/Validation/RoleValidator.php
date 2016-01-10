<?php
namespace Nodes\Backend\Models\Role\Validation;

use Nodes\Validation\AbstractValidator;

/**
 * Class RoleValidator
 *
 * @package Nodes\Backend\Models\Role\Validation
 */
class RoleValidator extends AbstractValidator
{
    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'create' => [
            'slug' => ['required', 'unique:backend_roles,slug,{:id}'],
            'title' => ['required']
        ]
    ];
}
