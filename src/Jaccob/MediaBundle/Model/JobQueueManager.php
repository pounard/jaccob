<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Pomm\StuffThatDoesQueriesTrait;
use Jaccob\MediaBundle\Type\Job\JobFactoryAwareTrait;

use PommProject\Foundation\Where;

/**
 * Job handling goes throught this, this is not a model because we definitly
 * do not need to have a model class nor a structure object, we only need to
 * do very specific queries over this table.
 */
class JobQueueManager
{
    use JobFactoryAwareTrait;
    use MediaModelAware;
    use StuffThatDoesQueriesTrait;

    /**
     * Fetch next job to execute
     *
     * @return mixed[]
     *   Row containing job data
     */
    public function fetchNext()
    {
        $sql = "
            UPDATE
                media_job_queue
            SET
                is_running = true,
                ts_started = NOW()
            WHERE id IN (
                SELECT id
                FROM media_job_queue
                WHERE
                    is_running = false
                    AND is_failed = false
                ORDER BY
                    ts_added ASC,
                    id ASC
                LIMIT 1 OFFSET 0
            )
            RETURNING
                *
        ";

        $data = $this->query($sql)->current();

        if (!empty($data['data'])) {
            $data['data'] = unserialize($data['data']);
        }

        return $data;
    }

    /**
     * Run job with data
     *
     * @param mixed[] $data
     *   Row containing job data
     */
    public function run($data)
    {
        if (!isset($data['type'])) {
            throw new \InvalidArgumentException("Missing 'type' for job");
        }
        if (!isset($data['id_media'])) {
            throw new \InvalidArgumentException("Missing 'id_media' for job");
        }

        $media = $this->getMediaModel()->findByPK(['id' => $data['id_media']]);
        if (!$media) {
            throw new \InvalidArgumentException("Media '%d' does not exists", $data['id_media']);
        }

        if (!isset($data['data'])) {
            $data['data'] = [];
        }

        $runner = $this->jobFactory->createJob($data['type']);
        $runner->run($media, $data['data']);
    }

    /**
     * Run next in queue
     *
     * @return boolean
     */
    public function runNext()
    {
        $data = $this->fetchNext();

        if (!$data) {
            return false;
        }

        return $this->run($data);
    }

    /**
     * Push a job in the queue
     *
     * @param int $mediaId
     *   Media this job will operate on
     * @param string $type
     *   Job type
     * @param array $options
     *   Arbitrary set of options that will be given to the job when started
     */
    public function push($mediaId, $type, $options = [])
    {
        $values = [
            'id_media'  => $mediaId,
            'type'      => $type,
            'data'      => serialize($options),
        ];

        $sql = strtr(
            "insert into :relation (:fields) values (:values) returning 1",
            [
                ':relation'   => 'public.media_job_queue',
                ':fields'     => $this->getEscapedFieldList(array_keys($values)),
                ':values'     => join(',', array_fill(0, count($values), '$*')),
            ]
        );

        $this->query($sql, array_values($values));

        return $this;
    }
}
