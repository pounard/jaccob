<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Album;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ShareController extends AbstractUserAwareController
{
    use MediaModelAware;

    /**
     * Find album with matching token or 404
     *
     * @param int $albumId
     * @param string $token
     *
     * @return \Jaccob\MediaBundle\Model\Album
     */
    protected function checkTokenOr404($albumId, $token)
    {
        $album = $this->findAlbumOr404($albumId);

        if (!$album->share_token === $token) {
            $this->createNotFoundException();
        }

        return $album;
    }

    /**
     * Is the given album set in current user session
     *
     * @param Album $album
     */
    protected function isAlbumInSession(Album $album)
    {
        return $this
            ->getAlbumModel()
            ->isAlbumInSession(
                $album->id,
                $this->get('session')->getId()
            )
        ;
    }

    /**
     * Save album in the current user session
     *
     * @param Album $album
     */
    protected function saveAlbumInSession(Album $album)
    {
        return $this
            ->getAlbumModel()
            ->saveAlbumInSession(
                $album->id,
                $this->get('session')->getId()
            )
        ;
    }

    /**
     * Main token entry point
     */
    public function tokenAction($albumId, $token, Request $request)
    {
        $album = $this->checkTokenOr404($albumId, $token);

        if ($this->isAlbumInSession($album)) {
            return $this->redirectToRoute('jaccob_media.album.view', ['albumId' => $album->id]);
        }

        if ($album->share_password) {
            return $this->redirectToRoute('jaccob_media.share.token_password', ['albumId' => $album->id, 'token' => $token]);
        }

        $this->saveAlbumInSession($album);

        return $this->redirectToRoute('jaccob_media.album.view', ['albumId' => $album->id]);
    }

    /**
     * Ask for an album password form
     */
    public function tokenPasswordAction($albumId, $token, Request $request)
    {
        $album = $this->checkTokenOr404($albumId, $token);

        if ($this->isAlbumInSession($album)) {
            return $this->redirectToRoute('jaccob_media.album.view', ['albumId' => $album->id]);
        }

        if (!$album->share_password) {
            $this->saveAlbumInSession($album);
            return $this->redirectToRoute('jaccob_media.album.view', ['albumId' => $album->id]);
        }

        return $this->render('JaccobMediaBundle:Share:tokenPassword.html.twig', ['album' => $album]);
    }
}
