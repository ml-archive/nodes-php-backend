<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Class NStackController
 *
 * @package Nodes\Backend\Http\Controllers
 */
class NStackController extends Controller
{
    /**
     * NStackController constructor
     *
     * @access public
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function __construct()
    {
        if (Gate::denies('backend-super-admin')) {
            abort(403);
        }
    }

    /**
     * NStack hook
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hook()
    {
        // Retrieve NStack config
        $config = config('nodes.backend.nstack');

        $default = !empty($config['defaults']['application']) ? $config['defaults']['application'] : 'default';

        $application = \Input::get('application', $default);

        $credentials = !empty($config['credentials'][$application]) ? $config['credentials'][$application] : $config['credentials']; // For backwards compatibility

        // Validate NStack credentials
        if (empty($config['url']) || empty($credentials['appId']) || empty($credentials['masterKey']) ||
            empty($config['role'])
        ) {
            return redirect()->back()->with('error', 'NStack hook is not configured');
        }

        // Retrieve backend user
        $backendUser = backend_user();

        // Create message
        $encryptedMessage = $this->encrypt(json_encode([
            'appId'           => $credentials['appId'],
            'masterKey'       => $credentials['masterKey'],
            'role'            => $config['role'],
            'accessToAllApps' => isset($config['accessToAllApps']) ? filter_var($config['accessToAllApps'], FILTER_VALIDATE_BOOLEAN) : true, // For backwards compatibility
            'url'             => url('/'),
            'user'            => [
                'name'  => $backendUser->name,
                'email' => $backendUser->email,
            ],
        ]));

        return redirect()->away($config['url'] . '?message=' . urlencode($encryptedMessage));
    }

    /**
     * Encrypt message
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param  string $text
     * @return string
     */
    private function encrypt($text)
    {
        return trim(base64_encode(mcrypt_encrypt(
            MCRYPT_RIJNDAEL_256,
            env('NODES_SALT', '0123456789012345'),
            $text,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(
                MCRYPT_RIJNDAEL_256,
                MCRYPT_MODE_ECB
            ), MCRYPT_RAND)
        )));
    }
}
