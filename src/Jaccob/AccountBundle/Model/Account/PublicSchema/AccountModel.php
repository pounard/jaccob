<?php

namespace Jaccob\AccountBundle\Model\Account\PublicSchema;

use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\ModelManager\Model\Projection;

use Jaccob\AccountBundle\Model\Account\PublicSchema\AutoStructure\Account as AccountStructure;
use Jaccob\AccountBundle\Model\Account\PublicSchema\Account;
use Jaccob\AccountBundle\Security\Crypt;

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
        $this->flexible_entity_class = "\Jaccob\AccountBundle\Model\Account\PublicSchema\Account";
    }

    /*
    public function getAccount($username)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT * FROM account WHERE mail = :mail");
        $st->setFetchMode(\PDO::FETCH_OBJ);
        $st->execute(array(':mail' => $username));

        foreach ($st as $object) {
            $account = new Account();
            $account->fromArray(array(
                'id'          => $object->id,
                'username'    => $object->mail,
                'displayName' => $object->user_name,
                'salt'        => $object->salt,
                'publicKey'   => $object->key_public,
                'privateKey'  => $object->key_private,
                'keyType'     => $object->key_type
            ));

            return $account;
        }

        throw new NotFoundError(sprintf("Account with name '%s' does not exist", $username));
    }

    public function getAccountById($id)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT * FROM account WHERE id = :id");
        $st->setFetchMode(\PDO::FETCH_OBJ);
        $st->execute(array(':id' => $id));

        foreach ($st as $object) {
            $account = new Account();
            $account->fromArray(array(
                'id'          => $object->id,
                'username'    => $object->mail,
                'displayName' => $object->user_name,
                'salt'        => $object->salt,
                'publicKey'   => $object->key_public,
                'privateKey'  => $object->key_private,
                'keyType'     => $object->key_type
            ));

            return $account;
        }

        throw new NotFoundError(sprintf("Account with id '%s' does not exist", $id));
    }

    public function getAnonymousAccount()
    {
            $account = new Account();
            $account->fromArray(array(
                'id'       => 0,
                'username' => "Anonymous",
            ));

            return $account;
    }
     */

    /**
     * Find a single user per its mail address
     *
     * @param string $mail
     *
     * @return \Jaccob\AccountBundle\Model\Account\PublicSchema\Account
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

    public function updatePassword(Account $account, $password)
    {
        $salt = Crypt::createSalt();

        $account->setSalt($salt);
        $account->setPasswordHash(Crypt::getPasswordHash($password, $salt));

        $this->updateOne($account, ['password_hash', 'salt']);
    }

    /*
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

    public function setAccountPassword($id, $password, $salt = null)
    {
        $db = $this->getApplication()->getDatabase();

        if (null === $salt) {
            $account = $this->getAccountById($id);
            $st = $db->prepare("UPDATE account SET password_hash = ? WHERE id = ?");
            $st->execute(array(Crypt::getPasswordHash($password, $account->getSalt()), $id));
        } else {
            $st = $db->prepare("UPDATE account SET password_hash = ?, salt = ? WHERE id = ?");
            $st->execute(array(Crypt::getPasswordHash($password, $salt), $salt, $id));
        }
    }
     */
}
