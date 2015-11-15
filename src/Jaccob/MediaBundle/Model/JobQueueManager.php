<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Pomm\StuffThatDoesQueries;

use PommProject\Foundation\Where;

/**
 * Job handling goes throught this, this is not a model because we definitly
 * do not need to have a model class nor a structure object, we only need to
 * do very specific queries over this table.
 */
class JobQueueManager extends StuffThatDoesQueries
{
    /**
     * {@inheritdoc}
     */
    public function getClientType()
    {
        // And why not ?
        return 'query_helper';
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

        /*
        $result = $this->getSession()
            ->getQueryManager()
            ->query('select * from media_job_queue where my_field = $*', ['some value'])
        ;

        if ($result->isEmpty()) {
          printf("There are no results with the given parameter.\n");
        } else {
          foreach ($result as $row) { // ← note 4
            printf(
                "field1 = '%s', field2 = '%s'.\n",
                $row['field1'],     // ← note 5
                $row['field2'] === true ? 'OK' : 'NO'
                );
          }
        }
         */

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
