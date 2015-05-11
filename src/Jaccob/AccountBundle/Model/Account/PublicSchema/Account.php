<?php

namespace Jaccob\AccountBundle\Model\Account\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Account
 *
 * Flexible entity for relation
 * public.account
 *
 * @see FlexibleEntity
 */
class Account extends FlexibleEntity
{
    /**
     * Get account username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->get('user_name');
    }

    /**
     * Get mail address
     *
     * @return string
     */
    public function getMail()
    {
        return $this->get('mail');
    }

    public function getPasswordHash()
    {
        return $this->get('password_hash');
    }

    public function setPasswordHash($value)
    {
        return $this->set('password_hash', $value);
    }

    /**
     * Get account salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->get('salt');
    }

    public function setSalt($value)
    {
        return $this->set('salt', $value);
    }
}
