<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Album;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ShareController extends Controller
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

        $form = $this
            ->createFormBuilder()
            ->add('password', TextType::class, [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add("Okay", SubmitType::class)
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {
                $data = $form->getData();
                if ($data['password'] === $album->share_password) {

                    $this->addFlash('success', "Okay you're granted !");
                    $this->saveAlbumInSession($album);

                    return $this->redirectToRoute('jaccob_media.album.view', ['albumId' => $album->id]);
                } else {
                    $this->addFlash('danger', "Wrong password, sorry.");
                }
            }
        }

        return $this->render('JaccobMediaBundle:Share:tokenPassword.html.twig', ['album' => $album, 'form' => $form->createView()]);
    }
}
