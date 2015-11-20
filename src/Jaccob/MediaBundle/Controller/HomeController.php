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
        $currentAccount = $this->getCurrentUserAccount();

        // @todo List all seeable albums (paginated, sorted).
        // @todo Request for sorting and filtering

        $albumList = $this
            ->getAlbumModel()
            ->paginateAlbumsFor($currentAccount->getId())
            ->getIterator()
        ;

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
            "size"      => $this->getParameter('jaccob_media.size.thumbnail')
        ]);
    }
}
