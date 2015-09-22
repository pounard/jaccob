<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends AbstractUserAwareController
{
    use AccountModelAware;
    use MediaModelAware;

    /**
     * Get media importer
     *
     * @return \Jaccob\MediaBundle\Import\DefaultImporter
     */
    protected function getImporter()
    {
        return $this
          ->get('jaccob_media.importer')
          ->setOwner(
              $this->getCurrentUserAccount()
          )
        ;
    }

    /**
     * Album main view.
     */
    public function listAction($albumId, Request $request)
    {
        // @todo List (paginated) all medias with thumbnails
        // @todo Request for sorting and filtering

        $album = $this->findAlbumOr404($albumId);
        $owner = $this->getAccountModel()->findByPK(['id' => $album->id_account]);

        $mediaList = $this
            ->getMediaModel()
            ->findWhere(
                (new Where())
                    ->andWhere("id_album = $*", [$album->id])
            )
        ;

        return $this->render('JaccobMediaBundle:Album:list.html.twig', [
            'owner'     => $owner,
            'album'     => $album,
            'mediaList' => $mediaList,
        ]);
    }

    /**
     * Create the creation form
     *
     * @return \Symfony\Component\Form\Form
     *
    protected function createCreateForm()
    {
        return $this
            ->createFormBuilder()
            ->add('name', 'text', [
                'label'     => "Album name",
                'required'  => true,
            ])
            ->add('path', 'text', [
                'label'     => "Path",
                'required'  => true,
            ])
            ->add('Create', 'submit')
            ->getForm()
        ;
    }
     */

    /**
     * Create new album form.
     *
    public function createAction(Request $request)
    {
        $form = $this->createCreateForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $data = $form->getData();

                $album = $this
                    ->getAlbumModel()
                    ->createAndSave([
                        'id_account'  => $this->getCurrentUserAccount()->getId(),
                        // @todo access_level
                        // @todo share_*
                        // @todo better path handling
                        'path'        => $data['path'],
                        'user_name'   => $data['name'],
                    ])
                ;

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your album has been created");

                return $this->redirectToRoute('jaccob_media.album.list', [
                    'albumId' => $album->id,
                ]);

            } else {
                $this->addFlash('danger', "Please check the values you filled");
            }
        }

        return $this->render('JaccobMediaBundle:Album:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     */

    /**
     * Use the default importer to list available folders in current
     * upload folder
     *
     * @return mixed[]
     *   List of directories
     */
    protected function listUploadFolder()
    {
        $importer = $this->getImporter();

        $iterator = new \CallbackFilterIterator(
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $importer->getWorkingDirectory(),
                    \FilesystemIterator::KEY_AS_PATHNAME |
                    \FilesystemIterator::CURRENT_AS_FILEINFO |
                    \FilesystemIterator::SKIP_DOTS
               ),
               \RecursiveIteratorIterator::SELF_FIRST
            ),
            function (\SplFileInfo $current, $key, $iterator) {
                return $current->isDir();
            }
        );

        $directories = array();
        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo) {

                $files = new \CallbackFilterIterator(

                    new \FilesystemIterator(
                        $file->getPathname(),
                        \FilesystemIterator::CURRENT_AS_FILEINFO |
                        \FilesystemIterator::SKIP_DOTS
                    ),

                    function (\SplFileInfo $current, $key, $iterator) {
                        return !$current->isDir();
                    }
                );

                if ($count = iterator_count($files)) {
                    $directories[] = array(
                        'filename' => $file->getFilename(),
                        'path'     => substr($file->getPathname(), strlen($importer->getWorkingDirectory())),
                        'label'    => $file->getFilename() . ' (' . $count . ')',
                    );
                }
            }
        }

        return $directories;
    }

    /**
     * Create an album from user selection into his upload directory form
     */
    public function createFromAction(Request $request)
    {
        $directories = $this->listUploadFolder();

        if (empty($directories)) {
            $this->addFlash('danger', "Please upload folders in the server first.");

            return $this->redirectToRoute('jaccob_media.home');
        }

        $options = [];
        foreach ($directories as $data) {
            $options[$data['path']] = $data['label'];
        }

        $form = $this
            ->createFormBuilder()
            ->add('directory', 'choice', [
                'label'     => "Select a folder",
                'required'  => true,
                'choices'   => $options,
            ])
            ->add('Run the import', 'submit')
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $data     = $form->getData();
                $importer = $this->getImporter();

                $album = $importer->importFromFolder($data['directory']);

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your album has been created");

                return $this->redirectToRoute('jaccob_media.album.list', [
                    'albumId' => $album->id,
                ]);

            } else {
                $this->addFlash('danger', "Please check the values you filled");
            }
        }

        return $this->render('JaccobMediaBundle:Album:createFrom.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Import new media into the album form
     */
    public function importAction($albumId)
    {
        // @todo
        $album = $this->findAlbumOr404($albumId);

        return $this->render('JaccobMediaBundle:Album:import.html.twig', [
            /* 'form' => $form->createView(), */
            'album' => $album,
        ]);
    }
}
