<?php

namespace Jaccob\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

use PommProject\Foundation\Session\Session as PommSession;

use Jaccob\AccountBundle\Security\Crypt;

class SecurityController extends Controller
{
    /**
     * Get pomm session
     *
     * @return \Jaccob\AccountBundle\Model\Account\PublicSchema\AccountModel
     */
    protected function getAccountModel()
    {
        return $this
            ->get('pomm')
            ->getSession('account')
            ->getModel('\Jaccob\AccountBundle\Model\Account\PublicSchema\AccountModel');
    }

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

                $model    = $this->getAccountModel();
                $account  = $model->findUserByMail($form['email']->getData());

                if ($account) {
                    $password = Crypt::createPassword();
                    $model->updatePassword($account, $password);

                    if (true /* In devel mode */) {
                        $this->addFlash('success', "Password is: " . $password);
                    }
                }

                // Do never tell the user if the mail exist or not
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