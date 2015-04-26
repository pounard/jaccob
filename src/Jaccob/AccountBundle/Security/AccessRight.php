<?php

namespace Jaccob\AccountBundle\Security;

final class AccessRight
{
    /**
     * Security level private
     */
    const ACCESS_READ = 0x0002;

    /**
     * Security level friends can see
     */
    const ACCESS_WRITE = 0x0004;
}
