<?php

namespace Jaccob\AccountBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Security\Crypt;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * Change password action.
     */
    public function changePasswordAction(Request $request)
    {
        /** @var $account \Jaccob\AccountBundle\Model\Account */
        $account = $this->getUser()->getAccount();

        $this->denyAccessUnlessGranted('edit', $account);

        // Create the change password form.
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('password_old', PasswordType::class, [
                'label'     => "Your current password",
                'required'  => true,
            ])
            ->add('password1', PasswordType::class, [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add('password2', PasswordType::class, [
                'label'     => "Confirmation",
                'required'  => true,
            ])
            ->add('submit', SubmitType::class, [
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

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your password has been changed");

                return $this->redirectToRoute('jaccob_account.profile_view', [
                    'id' => $account->getId(),
                ]);

            } else {
                $this->addFlash('danger', "Please check that both passwords matches");
            }
        }

        return $this->render('JaccobAccountBundle:Security:changePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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
            ->add('email', EmailType::class, [
                'label' => "Email",
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Request new password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $model    = $this->getAccountModel();
                $account  = $model->findUserByMail($form['email']->getData());

                if ($account) {

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
            ->add('password1', PasswordType::class, [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add('password2', PasswordType::class, [
                'label'     => "Confirmation",
                'required'  => true,
            ])
            ->add('submit', SubmitType::class, [
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
