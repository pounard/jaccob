<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\SecurityAware;
use Jaccob\AccountBundle\Security\Access;
use Jaccob\AccountBundle\Security\Crypt;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AlbumController extends Controller
{
    use AccountModelAware;
    use MediaModelAware;
    use SecurityAware;

    /**
     * Album main view.
     */
    public function listAction($albumId, Request $request)
    {
        // @todo List (paginated) all medias with thumbnails
        // @todo Request for sorting and filtering

        $album      = $this->findAlbumOr404($albumId);
        $owner      = $this->findAccountOr404($album->id_account);

        $this->denyAccessUnlessGranted('view', $album);

        $page = $request->get('page', 1);

        $mediaPager = $this->getMediaModel()->findByAlbum($albumId, 100, $page);
        $mediaList  = $mediaPager->getIterator();

        return $this->render('JaccobMediaBundle:Album:view.html.twig', [
            'canEdit'   => $this->isGranted('edit', $album),
            'owner'     => $owner,
            'album'     => $album,
            'mediaList' => $mediaList,
            'pager'     => $mediaPager,
            'size'      => $this->getParameter('jaccob_media.size.thumbnail.grid')
        ]);
    }

    /**
     * Edit form
     */
    public function editFormAction($albumId, Request $request)
    {
        $album = $this->findAlbumOr404($albumId);

        $this->denyAccessUnlessGranted('edit', $album);

        $form = $this
            ->createFormBuilder($album)
            ->add('user_name', TextType::class, [
                'label'     => "Album title",
                'required'  => false,
                'attr'      => ['placeholder' => $album->getDisplayName()],
            ])
//             ->add('fix_dates', CheckboxType::class, [
//                 'label'     => "Fix dates from media list",
//                 'required'  => false,
//             ])
            ->add('access_level', ChoiceType::class, [
                'label'     => "Visibility",
                'choices'   => [
                    "Private" => Access::LEVEL_PRIVATE,
                    "Public (hidden in lists)" => Access::LEVEL_PUBLIC_HIDDEN,
                    "Public" => Access::LEVEL_PUBLIC_VISIBLE,
                ],
                'multiple'  => false,
                'required'  => true,
            ])
            ->add("Update", SubmitType::class, [
                'attr' => ['class' => 'pull-right btn-primary'],
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $data = $form->getData();
//                if ($data['fix_dates']) {
                    $ret = $this
                        ->getMediaSession()
                        ->getQueryManager()
                        ->query(
                            "select min(ts_user_date), max(ts_user_date) from media where id_album = $* group by id_album",
                            [$albumId]
                        )
                    ;

                    $values = $ret->current();
                    if ($values) {
                        $album->ts_user_date_begin = $values['min'];
                        $album->ts_user_date_end = $values['max'];
                    }
//                }

                $this->getAlbumModel()->updateOne($album, ['user_name', 'access_level', 'ts_user_date_begin', 'ts_user_date_end']);
                $this->addFlash('success', "Changes have been successfully saved");

                return $this->redirectToRoute('jaccob_media.album.view', [
                    'albumId' => $album->id,
                ]);

            } else {
                $this->addFlash('danger', "Please check the values you filled");
            }
        }

        return $this->render('JaccobMediaBundle:Album:editForm.html.twig', [
            'form'  => $form->createView(),
            'album' => $album,
        ]);
    }

    /**
     * Share form
     */
    public function shareFormAction($albumId, Request $request)
    {
        $album = $this->findAlbumOr404($albumId);

        $this->denyAccessUnlessGranted('share', $album);

        $alreadyEnabled = (bool)$album->share_enabled;

        if ($alreadyEnabled) {
            $saveLabel = "Update";
        } else {
            $saveLabel = "Share";
        }

        $form = $this
            ->createFormBuilder($album)
            ->add('share_enabled', CheckboxType::class, [
                'label'     => "Share this album",
                'required'  => false,
            ])
            ->add('share_password', TextType::class, [
                'label'     => "Set a password",
                'required'  => false,
                'attr'      => ['placeholder' => "Password"],
            ])
            ->add($saveLabel, SubmitType::class, [
                'attr' => ['class' => 'pull-right btn-primary'],
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                // Generate token if necessary
                if (!$album->share_token && $album->share_enabled) {
                    $album->share_token = substr(preg_replace('/[^a-zA-Z0-9]/', '', Crypt::createRandomToken()), 0, 32);
                }

                $this
                    ->getAlbumModel()
                    ->updateOne($album, ['share_enabled', 'share_password', 'share_token'])
                ;

                if ($album->share_enabled) {
                    $url = $this->generateUrl('jaccob_media.share.token', [
                        'albumId' => $albumId,
                        'token'   => $album->share_token,
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                }

                // Do never tell the user if the mail exist or not
                if ($alreadyEnabled === (bool)$album->share_enabled) {
                    if ($alreadyEnabled) {
                        $this->addFlash('success', "Share settings have been updated, copy-paste then share this link: <a href=\"" . $url . "\">" . $url . "</a>");
                    } else {
                        $this->addFlash('info', "Share is disabled");
                    }
                } else if (!$alreadyEnabled) {
                    $this->addFlash('success', "Album has been shared, copy-paste then share this link: " . $url);
                } else {
                    $this->addFlash('info', "Share has been disabled");
                }

                return $this->redirectToRoute('jaccob_media.album.view', [
                    'albumId' => $album->id,
                ]);

            } else {
                $this->addFlash('danger', "Please check the values you filled");
            }
        }

        return $this->render('JaccobMediaBundle:Album:shareForm.html.twig', [
            'form'  => $form->createView(),
            'album' => $album,
        ]);
    }
}
