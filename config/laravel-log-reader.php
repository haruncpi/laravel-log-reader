<?php

return [
    'api_route_path'   => 'log/json',
    'view_route_path'  => 'admin/log-reader',
    'admin_panel_path' => 'admin',
    'middleware'       => ['web', 'auth']
];
