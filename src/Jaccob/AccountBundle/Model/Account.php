<?php

namespace Jaccob\AccountBundle\Model;

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
     * Get identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->get('user_name');
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->set('user_name');
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

    /**
     * Get password hash
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->get('password_hash');
    }

    /**
     * Set password hash
     *
     * @param string $value
     */
    public function setPasswordHash($value)
    {
        return $this->set('password_hash', $value);
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->get('salt');
    }

    /**
     * Set salt
     *
     * @param string $value
     */
    public function setSalt($value)
    {
        $this->set('salt', $value);
    }

    /**
     * Is user admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->get('is_admin');
    }
}
