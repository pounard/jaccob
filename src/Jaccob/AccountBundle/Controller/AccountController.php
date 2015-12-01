<?php

namespace Jaccob\AccountBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    use AccountModelAware;

    /**
     * Get task or throw a 404 or 403 error depending on data
     *
     * @param int $id
     *   Account identifier
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    protected function findAccountOr404($id)
    {
        /* @var $account \Jaccob\AccountBundle\Model\Account */
        $account = $this->getAccountModel()->findByPK(['id' => $id]);

        if (!$account) {
            throw $this->createNotFoundException(sprintf(
                "Task with id '%d' does not exists",
                $id
            ));
        }

        // @todo Permissions somewhere?

        return $account;
    }

    /**
     * View account
     */
    public function viewAction($id)
    {
        $account = $this->findAccountOr404($id);

        return $this->render('JaccobAccountBundle:Account:view.html.twig', ['account' => $account]);
    }
}
