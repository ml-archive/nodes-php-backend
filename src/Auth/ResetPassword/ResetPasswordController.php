<?php

namespace Nodes\Backend\Auth\ResetPassword;

use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Facades\Request;
use Nodes\Backend\Auth\ResetPassword\Validation\ResetPasswordValidator;

/**
 * Class ResetPasswordController.
 */
class ResetPasswordController extends IlluminateController
{
    /**
     * Reset password model.
     *
     * @var \Nodes\Backend\Auth\ResetPassword\ResetPasswordRepository
     */
    protected $resetPasswordRepository;

    /**
     * Constructor.
     *
     * @param  \Nodes\Backend\Auth\ResetPassword\ResetPasswordRepository $resetPasswordRepository
     */
    public function __construct(ResetPasswordRepository $resetPasswordRepository)
    {
        $this->resetPasswordRepository = $resetPasswordRepository;

        // Set title of all views in this controller
        view()->share('title', 'Reset password');
    }

    /**
     * Form to request reset password token.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('nodes.backend::reset-password.form');
    }

    /**
     * Gemerate reset password token.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateResetToken()
    {
        // Retrieve received e-mail
        $email = Request::get('email');

        // Validate e-mail
        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->route('nodes.backend.reset-password.form')
                ->with('error', 'Missing or invalid e-mail address');
        }

        // Generate token and send e-mail
        $status = $this->resetPasswordRepository->sendResetPasswordEmail(['email' => $email]);

        if (empty($status)) {

            // Check if we should show error message if email does not exist, or just display
            // the same message as if the email was really sent
            $secureEmailCheck = config('nodes.backend.reset-password.secure_email_check', false);

            if (! $secureEmailCheck) {
                return redirect()
                    ->route('nodes.backend.reset-password.form')
                    ->with('error', 'Could not send reset password e-mail');
            }
        }

        return redirect()->route('nodes.backend.reset-password.sent')->with('info', 'Check your mailbox');
    }

    /**
     * Confirmation page of e-mail has been sent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\View\View
     */
    public function sent()
    {
        return view('nodes.backend::reset-password.sent');
    }

    /**
     * Reset password form.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $token
     * @return \Illuminate\View\View
     */
    public function resetForm($token)
    {
        // Validate token
        $resetToken = $this->resetPasswordRepository->getByToken($token);
        if (empty($resetToken) || $resetToken->isUsed()) {
            return view('nodes.backend::reset-password.invalid');
        }

        // Check if token's expiry date has been exceed
        if ($resetToken->isExpired()) {
            return view('nodes.backend::reset-password.expired');
        }

        return view('nodes.backend::reset-password.reset', compact('token'));
    }

    /**
     * Reset/Update user's password.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param \Nodes\Backend\Auth\ResetPassword\Validation\ResetPasswordValidator $validator
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(ResetPasswordValidator $validator)
    {
        $data = Request::all();

        // Validate data
        if (!$validator->with($data)->validate()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validator->errorsBag());
        }

        // Get token
        $resetToken = $this->resetPasswordRepository->getByUnexpiredToken($data['token']);
        if (empty($resetToken)) {
            return redirect()->back()->with('error', 'Could not retrieve valid reset password token');
        }

        // Validate e-mail address
        if ($resetToken->email != $data['email']) {
            return redirect()->back()->with(['email' => $data['email'], 'error' => 'Token does not belong to e-mail address']);
        }

        // All good! Update user's password
        $status = $this->resetPasswordRepository->updatePasswordByEmail($data['email'], $data['password']);
        if (empty($status)) {
            return redirect()->back()->with(['email' => $data['email'], 'error' => 'Could not change user\'s password']);
        }

        // Mark token as used
        $resetToken->markAsUsed();

        return redirect()->route('nodes.backend.reset-password.done')->with('success', 'Password was successfully changed');
    }

    /**
     * Reset password confirmation.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\View\View
     */
    public function done()
    {
        return view('nodes.backend::reset-password.done');
    }
}
