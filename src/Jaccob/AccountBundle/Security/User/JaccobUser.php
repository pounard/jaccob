<?php

namespace Jaccob\AccountBundle\Security\User;

use Jaccob\AccountBundle\Model\Account;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class JaccobUser implements AdvancedUserInterface
{
    /**
     * @var \Jaccob\AccountBundle\Model\Account
     */
    private $account;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var boolean
     */
    private $accountNonExpired;

    /**
     * @var boolean
     */
    private $credentialsNonExpired;

    /**
     * @var boolean
     */
    private $accountNonLocked;

    /**
     * @var string[]
     */
    private $roles;

    public function __construct(Account $account, array $roles = [])
    {
        $this->account = $account;

        $this->username = $account->getUsername();
        $this->password = $account->getPasswordHash();
        $this->salt = $account->getSalt();
        $this->enabled = true; // FIXME
        $this->accountNonExpired = true; // FIXME
        $this->credentialsNonExpired = true; // FIXME
        $this->accountNonLocked = true; // FIXME
        $this->roles = $roles;
    }

    /**
     * Get user account
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
