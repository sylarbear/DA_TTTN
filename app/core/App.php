<?php

/**
 * App — Front Controller Dispatcher
 * Nhận route từ Router và dispatch đến Controller
 */
class App
{
    public function __construct()
    {
        $route = Router::resolve();

        call_user_func_array(
            [$route['controller'], $route['method']],
            $route['params']
        );
    }
}
