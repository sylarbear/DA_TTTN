<?php

/**
 * PHPStan bootstrap — khai báo class_alias để PHPStan hiểu các alias runtime
 */

// Helpers (PSR-4 namespaced → global alias)
class_alias(\App\Helpers\Request::class, 'Request');
class_alias(\App\Helpers\Validator::class, 'Validator');

// Services (PSR-4 namespaced → global alias)
class_alias(\App\Services\MembershipService::class, 'MembershipService');
