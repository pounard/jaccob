<?php

namespace Jaccob\AccountBundle\Model;

use Jaccob\AccountBundle\Model\Structure\Account as AccountStructure;
use Jaccob\AccountBundle\Security\Crypt;

use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\ModelManager\Model\Projection;

/**
 * AccountModel
 *
 * Model class for table account.
 *
 * @see Model
 */
class AccountModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->structure = new AccountStructure;
        $this->flexible_entity_class = "\Jaccob\AccountBundle\Model\Account";
    }

    /**
     * Find a single user per its mail address
     *
     * @param string $mail
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    public function findUserByMail($mail)
    {
        $accounts = $this
            ->findWhere('mail = $*', [$mail])
        ;

        foreach ($accounts as $account) {
            return $account;
        }
    }

    /**
     * Update the user password, update salt accordingly
     *
     * @param \Jaccob\AccountBundle\Model\Account $account
     * @param string $password
     */
    public function updatePassword(Account $account, $password)
    {
        $salt = Crypt::createSalt();

        $account->setSalt($salt);
        $account->setPasswordHash(Crypt::getPasswordHash($password, $salt));

        $this->updateOne($account, ['password_hash', 'salt']);
    }

    /*
    public function getAnonymousAccount()
    {
        $account = new Account();
        $account->fromArray(array(
            'id'       => 0,
            'username' => "Anonymous",
        ));

        return $account;
    }

    public function createAccount($username, $displayName = null, $active = false, $validateToken = null)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT 1 FROM account WHERE mail = ?");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($username));

        foreach ($st as $exists) {
            throw new LogicError("User account with specified email already exists");
        }

        $st = $db->prepare("INSERT INTO account (mail, user_name, is_active, validate_token) VALUES (?, ?, ?, ?)");
        $st->execute(array(
            $username,
            $displayName,
            (int)$active,
            $validateToken,
        ));

        return $this->getAccount($username);
    }

    public function setAccountKeys($id, $privateKey, $publicKey, $type)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("UPDATE account SET key_public = ?, key_private = ?, key_type = ? WHERE id = ?");
        $st->execute(array(
            $publicKey,
            $privateKey,
            $type,
            $id
        ));
    }
     */
}
