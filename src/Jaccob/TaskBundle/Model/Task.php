<?php

namespace Jaccob\TaskBundle\Model;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Task entity
 */
class Task extends FlexibleEntity
{
    /**
     * {@inheritdoc}
     */
    public static $strict = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->get('id_account');
    }

    /**
     * @param int $accountId
     *
     * @return Task
     */
    public function setAccountId($accountId)
    {
        return $this->set('id_account', $accountId);
    }

    /**
     * @return boolean
     */
    public function isDone()
    {
        return (bool)$this->get('is_done');
    }

    /**
     * @param boolean $isDone
     *
     * @return Task
     */
    public function setIsDone($isDone)
    {
        return $this->set('is_done', $isDone);
    }

    /**
     * @return boolean
     */
    public function isStarred()
    {
        return (bool)$this->get('is_starred');
    }

    /**
     * @param boolean $isStarred
     *
     * @return Task
     */
    public function setIsStarred($isStarred)
    {
        return $this->set('is_starred', $isStarred);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @param string $title
     *
     * @return Task
     */
    public function setTitle($title)
    {
        return $this->set('title', $title);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        return $this->set('description', $description);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->get('priority');
    }

    /**
     * @param int $priority
     *
     * @return Task
     */
    public function setPriority($priority)
    {
        return $this->set('priority', $priority);
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->get('ts_added');
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->get('ts_updated');
    }

    /**
     * @param \DateTime $date
     *
     * @return Task
     */
    public function setUpdateDate(\DateTime $date)
    {
        return $this->set('ts_updated', $date);
    }

    /**
     * @return \DateTime
     *   Can be null
     */
    public function getDeadline()
    {
        return $this->get('ts_deadline');
    }

    /**
     * @param \DateTime $date
     *
     * @return Task
     */
    public function setDeadline(\DateTime $date)
    {
        return $this->set('ts_deadline', $date);
    }
}
