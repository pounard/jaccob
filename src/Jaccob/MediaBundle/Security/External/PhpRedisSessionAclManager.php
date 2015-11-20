<?php

namespace Jaccob\MediaBundle\Security\External;

class PhpRedisSessionAclManager implements SessionAclManagerInterface
{
    /**
     * @var \Redis
     */
    protected $redisClient;

    /**
     * Set Redis client
     *
     * @param \Redis $redisClient
     */
    public function setRedisClient(\Redis $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * {intheritdoc}
     */
    public function addAlbumAuthorization($sessionId, $albumIdList, $lifetime = self::DEFAULT_LIFETIME)
    {
        $pipe = $this->redisClient->multi(\Redis::PIPELINE);

        foreach ($albumIdList as $albumId) {
            /* @var $pipe \Redis */
            $pipe->setex($sessionId  . ':' . $albumId, $lifetime, 1);
        }

        $pipe->exec();
    }

    /**
     * {intheritdoc}
     */
    public function removeAlbumAuthorization($sessionId, $albumIdList)
    {
        $pipe = $this->redisClient->multi(\Redis::PIPELINE);

        foreach ($albumIdList as $albumId) {
            /* @var $pipe \Redis */
            $pipe->del($sessionId  . ':' . $albumId);
        }

        $pipe->exec();
    }

    /**
     * {intheritdoc}
     */
    public function deleteForSession($sessionId)
    {
        // @todo Is there a proper way to do this without EVAL?
        //   Or maybe I should use sets instead, but we can't EXPIRE single set values
    }

    /**
     * {intheritdoc}
     */
    public function deleteExpired()
    {
        // There is nothing to do here since the Redis server will handle
        // expiration for himself.
    }

    /**
     * {intheritdoc}
     */
    public function deleteAll()
    {
        // @todo Is there a proper way to do this without EVAL?
    }
}
