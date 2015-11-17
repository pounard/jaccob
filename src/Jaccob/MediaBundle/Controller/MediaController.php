<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Component\HttpFoundation\Request;

class MediaController extends AbstractUserAwareController
{
    use MediaModelAware;
    use AccountModelAware;

    public function viewAction($mediaId)
    {
        $media    = $this->findMediaOr404($mediaId);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);

        $this->denyAccessUnlessGranted('view', $album);

        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $previous = $this->getMediaModel()->findPreviousInAlbum($media->id_album, $media->id);
        $next     = $this->getMediaModel()->findNextInAlbum($media->id_album, $media->id);
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:view.html.twig', [
            'canEdit'   => $this->isGranted('edit', $album),
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => $this->getParameter('jaccob_media.size.default'),
        ]);
    }

    public function viewFullscreenAction($mediaId)
    {
        $media    = $this->findMediaOr404($mediaId);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);

        $this->denyAccessUnlessGranted('view', $album);

        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $previous = $this->getMediaModel()->findPreviousInAlbum($media->id_album, $media->id);
        $next     = $this->getMediaModel()->findNextInAlbum($media->id_album, $media->id);
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:viewFullscreen.html.twig', [
            'canEdit'   => $this->isGranted('edit', $album),
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => $this->getParameter('jaccob_media.size.fullscreen'),
        ]);
    }

    /**
     * Edit form
     */
    public function editFormAction($mediaId, Request $request)
    {
        $media    = $this->findMediaOr404($mediaId);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);

        $this->denyAccessUnlessGranted('view', $album);

        $form = $this
            ->createFormBuilder($media)
            ->add('user_name', 'text', [
                'label'     => "Media title",
                'required'  => false,
                'attr'      => ['placeholder' => $media->getDisplayName()],
            ])
            ->add("update", 'submit', [
                'attr' => [
                    'class' => 'pull-right btn-primary',
                    'value' => 'Update',
                ],
            ])
            ->add("set_cover", 'submit', [
                'attr' => [
                    'class' => 'pull-right btn-success',
                    'value' => "Set as cover",
                ],
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                if ($form->get('update')->isClicked()) {
                    $this->getMediaModel()->updateOne($media, ['user_name']);
                    $this->addFlash('success', "Changes have been successfully saved");
                }
                if ($form->get('set_cover')->isClicked()) {
                    $album->id_media_preview = $media->id;
                    $this->getAlbumModel()->updateOne($album, ['id_media_preview']);
                    $this->addFlash('success', "Album cover has been changed");
                }

                return $this->redirectToRoute('jaccob_media.media.view', [
                    'mediaId' => $media->id,
                ]);

            } else {
                $this->addFlash('danger', "Please check the values you filled");
            }
        }

        return $this->render('JaccobMediaBundle:Media:editForm.html.twig', [
            'form'  => $form->createView(),
            'album' => $album,
            'media' => $media,
        ]);
    }
}
