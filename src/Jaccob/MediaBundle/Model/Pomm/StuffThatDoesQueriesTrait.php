<?php

namespace Jaccob\MediaBundle\Model\Pomm;

use PommProject\Foundation\Session;
use PommProject\Foundation\Where;

trait StuffThatDoesQueriesTrait
{
    /**
     * @var \PommProject\Foundation\Session
     */
    private $session;

    /**
     * Set session
     *
     * @param \PommProject\Foundation\Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Get pomm account session
     *
     * @return \PommProject\Foundation\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Execute the given query and return a Collection iterator on results. If
     * no projections are passed, it will use the default projection using
     * createProjection() method.
     *
     * @todo Pomm design architecture does not allow us to reuse their
     * implementation so we had to copy/paste it
     *
     * @param string             $sql
     * @param array              $values
     *
     * @return \Iterator
     */
    protected function query($sql, array $values = [])
    {
        if ($values instanceof Where) {
            $values = $values->getValues();
        }

        return $this
            ->getSession()
            ->getClientUsingPooler('query_manager', '\PommProject\Foundation\PreparedQuery\PreparedQueryManager')
            ->query($sql, $values)
        ;
    }

    /**
     * Handy method to escape strings
     *
     * @todo Pomm design architecture does not allow us to reuse their
     * implementation so we had to copy/paste it
     *
     * @param string $string
     *
     * @return string
     */
    protected function escapeLiteral($string)
    {
        return $this
            ->getSession()
            ->getConnection()
            ->escapeLiteral($string)
        ;
    }

    /**
     * Handy method to escape strings
     *
     * @todo Pomm design architecture does not allow us to reuse their
     * implementation so we had to copy/paste it
     *
     * @param string $string
     *
     * @return string
     */
    protected function escapeIdentifier($string)
    {
        return $this
            ->getSession()
            ->getConnection()
            ->escapeIdentifier($string)
        ;
    }

    /**
     * Return a comma separated list with the given escaped field names
     *
     * @todo Pomm design architecture does not allow us to reuse their
     * implementation so we had to copy/paste it
     *
     * @param array $fields
     *
     * @return string
     */
    protected function getEscapedFieldList(array $fields)
    {
        return join(
            ', ',
            array_map(
                function ($field) {
                    return $this->escapeIdentifier($field);
                },
                $fields
            )
        );
    }
}
