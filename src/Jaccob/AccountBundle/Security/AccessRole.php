<?php

namespace Jaccob\AccountBundle\Security;

final class AccessRole
{
    /**
     * Simple visitor
     */
    const ROLE_VISITOR = 'visitor';

    /**
     * Normal user
     */
    const ROLE_NORMAL = 'user';

    /**
     * Admin user
     */
    const ROLE_ADMIN = 'admin';
}
