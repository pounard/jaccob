<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Pomm\StuffThatDoesQueriesTrait;
use Jaccob\MediaBundle\Type\Job\JobFactoryAwareTrait;

use PommProject\Foundation\Where;

use Symfony\Component\Console\Output\OutputInterface;

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
     * @param OutputInterface $output
     */
    public function run($data, OutputInterface $output)
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
        return $runner->run($media, $data['data'], $output);
    }

    /**
     * Run next in queue
     *
     * @param OutputInterface $output
     *
     * @return boolean
     */
    public function runNext(OutputInterface $output)
    {
        $data = $this->fetchNext();

        if (!$data) {
            return false;
        }

        try {
            $ret = $this->run($data, $output);

            $this->delete($data['id']);

            return $ret;

        } catch (\Exception $e) {
            // Any kind of exception
            $this->markAsFailed($data['id']);

            throw $e;
        }
    }

    /**
     * Mark job as failed
     */
    public function delete($id)
    {
        $this->query("DELETE FROM media_job_queue WHERE id = $*", [$id]);
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed($id)
    {
        $sql = "
            UPDATE
                media_job_queue
            SET
                is_running = false,
                is_failed = true
            WHERE id = $*
        ";

        $this->query($sql, [$id]);
    }

    /**
     * List all jobs
     *
     * @param mixed[] $conditions
     * @param int $limit
     * @param int $page
     *
     * @return mixed[][]
     */
    public function listAll($conditions = [], $limit = 100, $page = 1)
    {
        $where = new Where();
        if (empty($conditions)) {
            $where->andWhere('1 = 1');
        } else {
            foreach ($conditions as $key => $value) {
                $where->andWhere($this->escapeIdentifier($key) . ' = $*', $value);
            }
        }

        $sql = strtr("
            SELECT *
            FROM media_job_queue
            WHERE
                :conditions
            ORDER BY
                ts_added ASC,
                id ASC
            LIMIT :limit OFFSET :offset
        ", [
            ':conditions' => (string)$where,
            ':limit'      => (int)$limit,
            ':offset'     => (int)(($page - 1) * $limit),
        ]);

        return $this->query($sql, $where->getValues());
    }

    /**
     * Count amongst all jobs
     *
     * Alias of listAll() but returning only the count
     *
     * @param mixed[] $conditions
     *
     * @return int
     */
    public function countAll($conditions = [])
    {
        $where = new Where();
        if (empty($conditions)) {
            $where->andWhere('1 = 1');
        } else {
            foreach ($conditions as $key => $value) {
                $where->andWhere($this->escapeIdentifier($key) . ' = $*', $value);
            }
        }

        $sql = strtr("
            SELECT COUNT(*)
            FROM media_job_queue
            WHERE
                :conditions
        ", [
            ':conditions' => (string)$where,
        ]);

        return $this->query($sql, $where->getValues())->current()['count'];
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
            "INSERT INTO :relation (:fields) VALUES (:values) RETURNING 1",
            [
                ':relation'   => 'public.media_job_queue',
                ':fields'     => $this->getEscapedFieldList(array_keys($values)),
                ':values'     => join(',', array_fill(0, count($values), '$*')),
            ]
        );

        $this->query($sql, array_values($values));
    }
}
