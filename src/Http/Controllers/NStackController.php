<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * Class NStackController
 * @author  Casper Rasmussen <cr@nodes.dk>
 *
 * @package Nodes\Backend\Http\Controllers
 */
class NStackController extends Controller
{
    /**
     * NStackController constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function __construct()
    {
        if (\Gate::denies('super-admin')) {
            abort(403);
        }
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hook()
    {
        $config = config('nodes.backend.nstack');

        // Guard config
        if(empty($config['url']) || empty($config['credentials']['appId']) || empty($config['credentials']['masterKey']) || empty($config['role'])) {
            return redirect()->back()->with('error', 'NStack hook is not configured');
        }

        // Create message
        $message = [
            'appId' => $config['credentials']['appId'],
            'masterKey' => $config['credentials']['masterKey'],
            'role' => $config['role'],
            'url' => url('/'),
            'user' => [
                'name' => backend_user()->name,
                'email' => backend_user()->email
            ]
        ];

        $encryptedMessage = $this->encrypt(json_encode($message));

        return redirect()->away($config['url'] . '?message=' . urlencode($encryptedMessage));
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $text
     * @return string
     */
    private function encrypt($text)
    {
        $salt = env('NODES_SALT', '0123456789012345');

        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
}
