<?php

/**
 * Backward Compatibility Aliases
 * Tạo class alias từ App namespace → global cho các class mới (dùng PSR-4)
 * Khi migrate hoàn toàn sang namespace, xóa file này.
 */

// Services (PSR-4 namespaced)
class_alias(\App\Services\MembershipService::class, 'MembershipService');

// Helpers (PSR-4 namespaced)
class_alias(\App\Helpers\Request::class, 'Request');
class_alias(\App\Helpers\Validator::class, 'Validator');
