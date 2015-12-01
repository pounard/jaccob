<?php

namespace Jaccob\AccountBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Security\Crypt;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    use AccountModelAware;

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
            $this->addFlash('danger', $error->getMessage());
        }

        return $this->render('JaccobAccountBundle:Security:login.html.twig');
    }

    /**
     * Change password form
     *
    public function changePasswordAction(Request $request)
    {
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('email', 'email', [
                'label'     => "Email",
                'attr'      => ['placeholder' => "Please confirm your email address"],
                'required'  => true,
            ])
            ->add('pass_old', 'password', [
                'label'     => "Current password",
                'attr'      => ['placeholder' => "Please confirm your old password"],
                'required'  => true,
            ])
            ->add('pass_new1', 'password', [
                'label'     => "New password",
                'attr'      => ['placeholder' => "Please type in your new password"],
                'required'  => true,
            ])
            ->add('pass_new2', 'password', [
                'label'     => "Confirmation",
                'attr'      => ['placeholder' => "Confirm here the new password"],
                'required'  => true,
            ])
            ->add('submit', 'submit', [
                'label' => "Change password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {
/*
                $model    = $this->getAccountModel();
                $account  = $model->findUserByMail($form['email']->getData());

                if ($account) {
                    $password = Crypt::createPassword();
                    $model->updatePassword($account, $password);

                    if ($this->getParameter('jaccob_account.password_request_as_message')) {
                        $this->addFlash('danger', "Password is: " . $password);
                    }
                }

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "A newly generated password has been sent to your e-mail address");

                return $this->redirectToRoute('jaccob_account.login');

            } else {
                $this->addFlash('danger', "Please verify information");
            }
        }

        return $this->render('JaccobAccountBundle:Security:requestPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    } */

    /**
     * Request new password form.
     */
    public function requestPasswordAction(Request $request)
    {
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                'label' => "Email",
                'required' => true,
            ])
            ->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => "Request new password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $model    = $this->getAccountModel();
                $account  = $model->findUserByMail($form['email']->getData());

                if ($account) {

                    if ($this->getParameter('jaccob_account.password_request_as_message')) {
                        $password = Crypt::createPassword();
                        $model->updatePassword($account, $password);
                        $this->addFlash('danger', "Password is: " . $password);
                    }

                    $token = Crypt::createRandomPlainToken();

                    $queryManager = $this
                        ->get('pomm')
                        ->getSession('default')
                        ->getClientUsingPooler('query_manager', '\PommProject\Foundation\PreparedQuery\PreparedQueryManager')
                    ;
                    $queryManager
                        ->query("DELETE FROM account_onetime WHERE id_account = $*", [
                            $account->id,
                        ])
                    ;
                    $queryManager
                        ->query("INSERT INTO account_onetime (id_account, login_token, ts_expire) VALUES ($*, $*, $*)", [
                            $account->id,
                            $token,
                            (new \DateTime("now +1 hour"))->format('Y-m-d H:i:s'),
                        ])
                    ;

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom('media@processus.org')
                        ->setTo($account->mail)
                        ->setBody(
                            $this->renderView('JaccobAccountBundle:Email:requestPassword.html.twig', [
                                'account' => $account,
                                'token'   => $token,
                            ]),
                            'text/html'
                        )
                        /*
                         * If you also want to include a plaintext version of the message
                        ->addPart(
                            $this->renderView(
                                'Emails/registration.txt.twig',
                                array('name' => $name)
                            ),
                            'text/plain'
                        )
                        */
                    ;
                    $this->get('mailer')->send($message);
                }

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "A newly generated password has been sent to your e-mail address");

                return $this->redirectToRoute('jaccob_account.login');

            } else {
                $this->addFlash('danger', "Invalid email address");
            }
        }

        return $this->render('JaccobAccountBundle:Security:requestPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * One time login
     */
    public function oneTimeLoginAction(Request $request, $accountId, $token)
    {
        $queryManager = $this
            ->get('pomm')
            ->getSession('default')
            ->getClientUsingPooler('query_manager', '\PommProject\Foundation\PreparedQuery\PreparedQueryManager')
        ;

        $row = $queryManager
            ->query("SELECT id_account FROM account_onetime WHERE login_token = $* AND ts_expire > NOW()", [$token])
            ->current()
        ;

        if (!$row) {
            throw $this->createNotFoundException();
        }

        $loadedId = $row['id_account'];
        if ($loadedId != $accountId) {
            // @todo Invalidate the token ?
            throw $this->createNotFoundException();
        }

        $account = $this->getAccountModel()->findByPK(['id' => $accountId]);
        if (!$account) {
            throw $this->createNotFoundException();
        }

        // Create the change password form.
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('password1', 'password', [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add('password2', 'password', [
                'label'     => "Confirmation",
                'required'  => true,
            ])
            ->add('submit', 'submit', [
                'label' => "Change password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {

            $form->handleRequest($request);

            $data = $form->getData();
            $matches = !empty($data['password1']) && $data['password1'] === $data['password2'];

            if ($form->isValid() && $matches) {

                $this->getAccountModel()->updatePassword($account, $data['password1']);

                $row = $queryManager
                    ->query("DELETE FROM account_onetime WHERE id_account = $*", [$accountId])
                    ->current()
                ;

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your password has been changed, you may now login");

                return $this->redirectToRoute('jaccob_account.login');

            } else {
                $this->addFlash('danger', "Please check that both passwords matches");
            }
        }

        return $this->render('JaccobAccountBundle:Security:oneTimeLogin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
