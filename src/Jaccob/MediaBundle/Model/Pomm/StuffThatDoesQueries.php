<?php

namespace Jaccob\MediaBundle\Model\Pomm;

use PommProject\Foundation\Client\ClientInterface;
use PommProject\Foundation\Client\ClientTrait;

/**
 * This actually implements the ClientInterface interface
 */
abstract class StuffThatDoesQueries implements ClientInterface
{
    use ClientTrait;

    /**
     * {@inheritdoc}
     */
    public function getClientIdentifier()
    {
        return trim(get_class($this), "\\");
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
        return $this
            ->getSession()
            ->getClientUsingPooler('prepared_query', $sql)
            ->execute($values)
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
