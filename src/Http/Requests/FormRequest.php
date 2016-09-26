<?php

namespace Nodes\Backend\Http\Requests;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class FormRequest
 *
 * @package Nodes\Backend\Http\Requests
 */
class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Overriding the failedAuthorization to keep the look at feel for the backend
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return void
     */
    public function failedAuthorization()
    {
        abort(403);
    }

    /**
     * Overriding response to make add the flash message also
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors) : Response
    {
        return $this->redirector->to($this->getRedirectUrl())
                                ->with('error', $this->getValidatorInstance()->getMessageBag())
                                ->withErrors($errors, $this->errorBag);
    }
}
