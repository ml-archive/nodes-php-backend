<?php

namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Class NStackController.
 */
class NStackController extends Controller
{
    /**
     * getConfig.
     * This function can be overridden for changing configs in runtime
     *
     * @return array|null
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    protected function getConfig()
    {
        return config('nodes.backend.nstack');
    }

    /**
     * guardUserPermissions.
     * This function can be overridden for changing user permissions
     *
     * @return void
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    protected function guardUserPermissions()
    {
        if (Gate::denies('backend-super-admin')) {
            abort(403);
        }
    }

    /**
     * NStack hook.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function hook()
    {
        $this->guardUserPermissions();

        // Retrieve NStack config
        $config = $this->getConfig();

        $default = !empty($config['defaults']['application']) ? $config['defaults']['application'] : 'default';

        $application = \Request::get('application', $default);

        $credentials = !empty($config['credentials'][$application]) ? $config['credentials'][$application] : $config['credentials']; // For backwards compatibility

        // Validate NStack credentials
        if (empty($config['url']) || empty($credentials['appId']) || empty($credentials['masterKey'])) {
            return redirect()->back()->with('error',
                'NStack hook is not configured, setup keys in (config/nodes/backend/nstack.php)');
        }

        //http://nstack.test/deeplink/[APP_ID]/[MASTER_KEY]]
        return redirect()->away($config['url'] . '/' . $credentials['appId'] . '/' . $credentials['masterKey']);
    }
}
