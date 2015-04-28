<?php

namespace Jaccob\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SecurityController extends Controller
{
    /**
     * Login form.
     */
    public function loginAction()
    {
        $error = $this
            ->get('security.authentication_utils')
            ->getLastAuthenticationError()
        ;

        if ($error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->render('JaccobAccountBundle:Security:login.html.twig');
    }

    /**
     * Request new password form.
     */
    public function requestPasswordAction(Request $request)
    {
        $form = $this
            ->createFormBuilder(null, [
                'method'    => Request::METHOD_POST,
                'attr'      => ['novalidate' => 'novalidate'],
            ])
            ->add('email', 'email', [
                'label'     => "Email",
                'required'  => true,
            ])
            ->add('submit', 'submit', [
                'label'     => "Request new password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                // @todo

                $this->addFlash('success', "A newly generated password has been sent to your e-mail address");

                return $this->redirectToRoute('_welcome');

            } else {
                $this->addFlash('error', "Invalid email address");
            }
        }

        return $this->render('JaccobAccountBundle:Security:requestPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
