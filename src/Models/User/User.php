<?php
namespace Nodes\Backend\Models\User;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Nodes\Backend\Auth\Contracts\Authenticatable as AuthenticatableContract;
use Nodes\Backend\Auth\Contracts\CanResetPassword as CanResetPasswordContract;
use Nodes\Backend\Models\User\Token\Token;
use Nodes\CounterCache\CounterCacheable;
use Nodes\CounterCache\Traits\CounterCache;
use Nodes\Database\Exceptions\SaveFailedException;

/**
 * Class User
 *
 * @package Nodes\Backend\Models\User
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, CounterCacheable
{
    use Authenticatable,
        CanResetPassword,
        CounterCache;

    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'backend_users';

    /**
     * Fillable columns
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
     * Sensitive columns
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Typecast columns
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'change_password' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * User has one access token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function token()
    {
        return $this->hasOne(\Nodes\Backend\Models\User\Token\Token::class, 'user_id');
    }

    /**
     * User belongs to role
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
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
     * Retrieve token for user, if there is no token one will be created
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
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
     * Delete all user tokens and create a new one
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Nodes\Database\Exceptions\SaveFailedException
     */
    public function deleteAllTokensAndCreateNew()
    {
        $this->token()->delete();

        return $this->createToken();
    }

    /**
     * Create new token for user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Nodes\Database\Exceptions\SaveFailedException
     */
    public function createToken()
    {
        // Generate & assign access token
        $token = $this->token()->save(new Token([
            'token' => Hash::make(str_random())
        ]));

        if (!$token) {
            throw new SaveFailedException('Failed to create a token for user');
        }

        return $token;
    }

    /**
     * Retrieve users image or fallback image
     * The image can be resized if CDN has it supported
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer $width
     * @param  integer $height
     * @return string|null
     */
    public function getImageUrl($width = 100, $height = 100)
    {
        $imageUrl = $this->getImageUrlOrNull($width, $height);
        if (!$imageUrl) {
            return assets_resize(config('nodes.backend.general.user_fallback_image_url'), $width, $height);
        }

        return $imageUrl;
    }

    /**
     * Retrieve users image or null if not set
     *
     * The image can be resized if CDN has it supported
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer $width
     * @param  integer $height
     * @return string|null
     */
    public function getImageUrlOrNull($width = 100, $height = 100)
    {
        if (empty($this->image)) {
            return null;
        }

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
     * Automatically hash passwords
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Automatically convert boolean
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string $value
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
     * Retrieve counter caches
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function counterCaches()
    {
        return [
            'user_count' => 'role',
        ];
    }
}
