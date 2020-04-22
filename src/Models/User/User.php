<?php

namespace Nodes\Backend\Models\User;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Nodes\Backend\Auth\Contracts\Authenticatable as AuthenticatableContract;
use Nodes\Backend\Auth\Contracts\CanResetPassword as CanResetPasswordContract;
use Nodes\Backend\Models\User\Token\Token;
use Nodes\CounterCache\CounterCacheable;
use Nodes\CounterCache\Traits\CounterCache;
use Nodes\Database\Eloquent\Model;
use Nodes\Database\Exceptions\SaveFailedException;

/**
 * Class User.
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, CounterCacheable
{
    use Authenticatable,
        CanResetPassword,
        CounterCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'backend_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'user_role',
        'change_password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'change_password' => 'boolean',
    ];

    /**
     * Defines if the user's password should be hashed automatically or not
     * when the password attribute is set. Hashing is enabled by default
     *
     * @var bool
     */
    protected $hashPassword = true;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * User has one access token.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function token()
    {
        return $this->hasOne(\Nodes\Backend\Models\User\Token\Token::class, 'user_id');
    }

    /**
     * User belongs to role.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(\Nodes\Backend\Models\Role\Role::class, 'user_role', 'slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieve token for user, if there is no token one will be created.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Nodes\Backend\Models\User\Token\Token
     * @throws \Nodes\Database\Exceptions\SaveFailedException
     */
    public function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        return $this->createToken();
    }

    /**
     * Delete all user tokens and create a new one.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Nodes\Database\Exceptions\SaveFailedException
     */
    public function deleteAllTokensAndCreateNew()
    {
        $this->token()->delete();

        return $this->createToken();
    }

    /**
     * Create new token for user.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Nodes\Database\Exceptions\SaveFailedException
     */
    public function createToken()
    {
        // Generate & assign access token
        $token = $this->token()->save(new Token([
            'token' => Hash::make(Str::random()),
        ]));

        if (empty($token)) {
            throw new SaveFailedException('Failed to create a token for user');
        }

        return $token;
    }

    /**
     * Retrieve users image or fallback image
     * The image can be resized if CDN has it supported.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  int $width
     * @param  int $height
     *
     * @return string
     */
    public function getImageUrl($width = 100, $height = 100)
    {
        // If user does not have an image,
        //we'll use a fallback one
        if (empty($this->image)) {
            $fallbackImageUrl = config('nodes.backend.general.user_fallback_image_url');

            return ! empty($fallbackImageUrl) ? assets_resize($fallbackImageUrl, $width, $height) : null;
        }

        // If user image is already an URL,
        // we'll return it untouched
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return assets_resize(assets_get($this->image), $width, $height);
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Automatically hash passwords.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @param  string $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $this->hashPassword ? Hash::make($value) : $value;
    }

    /**
     * Automatically convert boolean.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  string $value
     *
     * @return void
     */
    public function setChangePasswordPasswordAttribute($value)
    {
        $this->attributes['change_password'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /*
    |-----------------------------------------------------------
    | Counter cache
    |-----------------------------------------------------------
    */

    /**
     * Retrieve counter caches.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return array
     */
    public function counterCaches()
    {
        return ['user_count' => 'role'];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Disables the automatic password hashing when setting the attribute.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     * @access public
     * @return $this
     */
    public function disablePasswordHashing()
    {
        $this->hashPassword = false;

        return $this;
    }

    /**
     * Enables the automatic password hashing when setting the attribute.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     * @access public
     * @return $this
     */
    public function enablePasswordHashing()
    {
        $this->hashPassword = true;

        return $this;
    }
}
