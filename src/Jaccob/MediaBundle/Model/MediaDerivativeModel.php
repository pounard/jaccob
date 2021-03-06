<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\MediaDerivative as MediaDerivativeStructure;

use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * Media model
 */
class MediaDerivativeModel extends Model
{
    use WriteQueries;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->structure = new MediaDerivativeStructure;
        $this->flexible_entity_class = '\Jaccob\MediaBundle\Model\MediaDerivative';
    }

    /**
     * Find by media
     *
     * @param int $mediaId
     *
     * @return \Jaccob\MediaBundle\Model\MediaDerivative[]
     */
    public function findByMedia($mediaId)
    {
        $where = (new Where())
            ->andWhere('id_media = $*', [$mediaId])
        ;

        return $this->findWhere($where);
    }

    /**
     * Find by media
     *
     * @param int $mediaId
     * @param string $mimetype
     *
     * @return \Jaccob\MediaBundle\Model\MediaDerivative
     */
    public function findOneByMediaAndMimetype($mediaId, $mimetype)
    {
        $where = (new Where())
            ->andWhere('id_media = $*', [$mediaId])
            ->andWhere('mimetype = $*', [$mimetype])
        ;

        return $this->findWhere($where)->current();
    }
}
