<?php

namespace Nodes\Backend\Auth\ResetPassword;

use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Facades\Request;

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
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateResetToken()
    {
        // Retrieve received e-mail
        $email = Request::get('email');

        // Validate e-mail
        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('nodes.backend.reset-password.form')->with('error', 'Missing or invalid e-mail address');
        }

        // Generate token and send e-mail
        $status = $this->resetPasswordRepository->sendResetPasswordEmail(['email' => $email]);
        if (empty($status)) {
            return redirect()->route('nodes.backend.reset-password.form')->with('error', 'Could not send reset password e-mail');
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
            return view('vendor.nodes.backend.reset-password.expired');
        }

        return view('nodes.backend::reset-password.reset', compact('token'));
    }

    /**
     * Reset/Update user's password.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword()
    {
        // Retrieve received token
        $token = Request::get('token');

        // Validate token
        $resetToken = $this->resetPasswordRepository->getByUnexpiredToken($token);
        if (empty($resetToken)) {
            return redirect()->back()->with('error', 'Could not retrieve valid reset password token');
        }

        // Retrieve received e-mail
        $email = Request::get('email');

        // Validate e-mail address
        if ($resetToken->email != $email) {
            return redirect()->back()->with(['email' => $email, 'error' => 'Token does not belong to e-mail address']);
        }

        // Retrieve received passwords
        $password = Request::get('password');
        $repeatPassword = Request::get('repeat-password');

        // Validate passwords
        if ($password != $repeatPassword) {
            return redirect()->back()->with(['email' => $email, 'error' => 'The two passwords does not match each other']);
        }

        // All good! Update user's password
        $status = $this->resetPasswordRepository->updatePasswordByEmail($email, $password);
        if (empty($status)) {
            return redirect()->back()->with(['email' => $email, 'error' => 'Could not change user\'s password']);
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
