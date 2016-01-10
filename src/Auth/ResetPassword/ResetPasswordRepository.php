<?php
namespace Nodes\Backend\Auth\ResetPassword;

use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Nodes\Database\Eloquent\Repository as NodesRepository;

/**
 * Class ResetPasswordRepository
 *
 * @package Nodes\Backend\Auth\ResetPassword
 */
class ResetPasswordRepository extends NodesRepository
{
    /**
     * User model used by Nodes Auth
     * @var mixed
     */
    protected $userModel;

    /**
     * Error bag
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Constructor
     *
     * @access public
     * @param  \Illuminate\Contracts\Container\Container            $container
     * @param  \Nodes\Backend\Auth\ResetPassword\ResetPasswordModel $model
     */
    public function __construct(Container $container, ResetPasswordModel $model)
    {
        $this->setupRepository($model);
        $this->userModel = $container['nodes.backend.auth.model'];
        $this->errors = new MessageBag;
    }

    /**
     * Retrieve by token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $token
     * @return \Nodes\Backend\Auth\ResetPassword\Model
     */
    public function getByToken($token)
    {
        return $this->where('token', '=', $token)->first();
    }

    /**
     * Retrieve by - unexpired - token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $token
     * @return \Nodes\Backend\Auth\ResetPassword\Model
     */
    public function getByUnexpiredToken($token)
    {
        return $this->where('token', '=', $token)
                    ->where('expire_at', '>', Carbon::now()->format('Y-m-d H:i:s'))
                    ->first();
    }

    /**
     * Generate and send a email with reset password instructions
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $conditions WHERE conditions to locate user. Format: ['column' => 'value']
     * @return boolean
     * @throws \Nodes\Backend\Auth\Exception\ResetPasswordNoUserException
     */
    public function sendResetPasswordEmail(array $conditions)
    {
        // Validate conditions
        if (empty($conditions)) {
            return false;
        }

        // Add conditions to query builder
        foreach ($conditions as $column => $value) {
            $this->userModel = $this->userModel->where($column, '=', $value);
        }

        // Retrieve user with conditions
        $user = $this->userModel->first();
        if (empty($user)) {
            $this->errors->add('no-user-found', 'Could not find any user with those credentials.');
            return false;
        }

        // Generate reset password token
        $token = $this->generateResetPasswordToken($user);

        // Send e-mail with instructions on how to reset password
        $status = \Mail::send([
            'html' => config('nodes.backend.reset-password.views.html', 'nodes.backend::reset-password.emails.html'),
            'text' => config('nodes.backend.reset-password.views.text', 'nodes.backend::reset-password.emails.text')
        ], [
            'user' => $user,
            'domain' => config('app.url'),
            'token' => $token,
            'expire' => config('nodes.backend.reset-password.expire', 60),
            'project' => config('nodes.project.name')
        ], function($message) use ($user) {
            $message->to($user->email)
                ->from(config('nodes.backend.reset-password.from.email', 'no-reply@nodes.dk'), config('nodes.backend.reset-password.from.name', 'Nodes'))
                ->subject(config('nodes.backend.reset-password.subject', 'Reset password request'));
        });

        return (bool) $status;
    }

    /**
     * Generate reset token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  \Nodes\Backend\Database\Model $user
     * @return string
     */
    protected function generateResetPasswordToken(Model $user)
    {
        // Generate new token using Laravel's encryption key
        $token = hash_hmac('sha256', str_random(40), config('app.key'));

        // Expire timestamp
        $expire = Carbon::now()->addMinutes(config('nodes.backend.reset-password.expire', 60));

        // If user has previously tried to reset his/her password
        // we should just update the token of the previous entry
        // instead of creating a new one
        $resetToken = $this->where('email', '=', $user->email)->first();
        if (!empty($resetToken)) {
            $resetToken->update(['token' => $token, 'used' => 0, 'expire_at' => $expire->format('Y-m-d H:i:s')]);
        } else {
            $this->insert(['email' => $user->email, 'token' => $token, 'expire_at' => $expire->format('Y-m-d H:i:s')]);
        }

        return $token;
    }

    /**
     * Update user's password by e-mail
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $email
     * @param  string $password
     * @return boolean
     */
    public function updatePasswordByEmail($email, $password)
    {
        // Retrieve user by e-mail
        $user = $this->userModel->where('email', '=', $email)->first();
        if (empty($user)) {
            $this->errors('no-user-found-by-email', 'Could not find any user with e-mail: [' . $email . ']');
            return false;
        }

        // Update user with new password
        return (bool) $user->update([
            'password' => $password
        ]);
    }
}
