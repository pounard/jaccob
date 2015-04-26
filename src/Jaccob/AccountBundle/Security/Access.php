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
}
