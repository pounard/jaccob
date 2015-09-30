<?php

namespace Jaccob\AccountBundle\Security;

final class Access
{
    /**
     * Security level private
     */
    const LEVEL_PRIVATE = 0;

    /**
     * Security level friends can see
     */
    const LEVEL_FRIEND = 50;

    /**
     * Security level public
     */
    const LEVEL_PUBLIC_HIDDEN = 100;

    /**
     * Security level public
     */
    const LEVEL_PUBLIC_VISIBLE = 200;

    /**
     * Security level private
     */
    const ACCESS_READ = 0x0002;

    /**
     * Security level friends can see
     */
    const ACCESS_WRITE = 0x0004;

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
