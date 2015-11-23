<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\Event\AlbumAuthEvent;
use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractUserAwareController
{
    use MediaModelAware;

    /**
     * List of user visible albums
     */
    public function homeAction(Request $request)
    {
        // @todo List all seeable albums (paginated, sorted).
        // @todo Request for sorting and filtering

        if ($this->isCurrentUserAnonymous()) {

            $albumList = $this
                ->getAlbumModel()
                ->paginateAlbumsForSession(
                    $this
                        ->get('session')
                        ->getId()
                )
                ->getIterator()
            ;

            if (!count($albumList)) {
                throw $this->createAccessDeniedException();
            }

        } else {
            $albumList = $this
                ->getAlbumModel()
                ->paginateAlbumsFor(
                    $this
                        ->getCurrentUserAccount()
                        ->getId()
                )
                ->getIterator()
            ;
        }

        $authMap = [];

        // Fetch media previews
        $mediaIdList = [];
        $previewMap = [];
        foreach ($albumList as $album) {
            $authMap[] = $album->id;
            if ($album->id_media_preview) {
                $mediaIdList[] = $album->id_media_preview;
            }
        }
        if (!empty($mediaIdList)) {
            foreach ($this->getMediaModel()->findAllByPK($mediaIdList) as $media) {
                $previewMap[$media->id_album] = $media;
            }
        }

        $this->get('event_dispatcher')->dispatch(
            AlbumAuthEvent::AUTH,
            new AlbumAuthEvent($authMap, $this->get('session')->getId(), true)
        );

        return $this->render('JaccobMediaBundle:Home:home.html.twig', [
            'albums'    => $albumList,
            'previews'  => $previewMap,
            'canAdd'    => !$this->isCurrentUserAnonymous(),
            "size"      => $this->getParameter('jaccob_media.size.thumbnail')
        ]);
    }
}
