<?php
return [
    /*
    |--------------------------------------------------------------------------
    | List of dashboards
    |--------------------------------------------------------------------------
    |
    | Add dashboards in the list and they will be build dynamically
    | The different formats can be
    |
    | IFrame:
    |   'type' => 'i-frame',
    |   'title' => 'Google',
    |   'url' => 'https://google.com'
    |
    | Table count
    |   'type' => 'table-count',
    |   'title' => 'User count',
    |   'tables' => [
    |       'backend_users' => 'Backend users',
    |   ]
    |
    | Nodes statistics (url is from env)
    |   'type' => 'nodes-statistics-daily',
    |   'title' => 'Daily',
    |   'gaId' => 'UA-50813164-10'
    |
    |   'type' => 'nodes-statistics-monthly',
    |   'title' => 'Monthly',
    |   'gaId' => 'UA-50813164-10'
    */
    'list' => [
        [
            'type' => 'nodes-statistics-daily',
            'title' => 'Daily users',
            'gaId' => 'UA-50813164-10'
        ],
        [
            'type' => 'nodes-statistics-monthly',
            'title' => 'Monthly users',
            'gaId' => 'UA-50813164-10'
        ],
        [
            'type' => 'i-frame',
            'title' => 'New relic',
            'url' => 'https://rpm.newrelic.com/public/charts/jkxd5OwfxLt'
        ],
        [
            'type' => 'i-frame',
            'title' => 'NStack',
            'url' => 'https://nstack.io/admin'
        ],
        [
            'type' => 'table-count',
            'title' => 'Users',
            'tables' => [
                'backend_users' => 'Backend users',
                'backend_user_tokens' => 'Tokens'
            ]
        ],
        [
            'type' => 'table-count',
            'title' => 'Failed jobs',
            'tables' => [
                'failed_jobs' => 'Failed jobs',
            ]
        ],
    ],
];
