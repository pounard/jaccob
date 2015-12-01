<?php

namespace Jaccob\TaskBundle\Controller;

use Jaccob\AccountBundle\SecurityAware;

use Jaccob\TaskBundle\Model\Task;
use Jaccob\TaskBundle\TaskModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    use SecurityAware;
    use TaskModelAware;

    /**
     * Get task or throw a 404 or 403 error depending on data
     *
     * @param int $taskId
     *   Task identifier
     *
     * @return \Jaccob\TaskBundle\Model\Task
     */
    protected function findTaskOr404($id)
    {
        /* @var $task \Jaccob\TaskBundle\Model\Task */
        $task = $this->getTaskModel()->findByPK(['id' => $id]);

        if (!$task) {
            throw $this->createNotFoundException(sprintf(
                "Task with id '%d' does not exists",
                $id
            ));
        }

        $account = $this->getCurrentUserAccount();

        if ($account->getId() !== $task->getAccountId()) {
            throw $this->createAccessDeniedException();
        }

        return $task;
    }

    /**
     * Get task form
     *
     * @param \Jaccob\TaskBundle\Model\Task $task
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function getTaskForm(Task $task)
    {
        $form = $this->createForm(
            'jaccob_taskbundle_task',
            null,
            [
                // Change the action when not the same page
                'action' => $this->generateUrl('jaccob_task.add', ['id' => $task->getId()]),
                // We also can change the request method
                'method' => Request::METHOD_POST,
                // Example of adding the HTML5 "novalidate" attribute on the form
                'attr'   => ['novalidate' => 'novalidate'],
            ]
        );

        // Let the submit be in the controller so the form type can be reused
        // in another more complex forms
        $form->add('submit', 'submit', [
            'label' => "Add",
        ]);

        return $form;
    }

    /**
     * Menu
     */
    public function menuAction()
    {
        $items = [];

        return $this->render('JaccobTaskBundle:Task:menu.html.twig', ['items' => $items]);
    }

    /**
     * View task
     */
    public function viewAction($id)
    {
        $task = $this->findTaskOr404($id);

        return $this->render('JaccobTaskBundle:Task:view.html.twig', ['task' => $task]);
    }

    /**
     * Edit task
     */
    public function editAction($id)
    {
        $task = $this->findTaskOr404($id);

        return $this->render('::notImplementedYet.html.twig');
    }

    /**
     * Add task
     */
    public function addAction(Request $request)
    {
        /* @var $task \Jaccob\TaskBundle\Model\Task */
        $task = $this->getTaskModel()->createEntity(['id' => null]);
        $form = $this->getTaskForm($task);

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $this->getTaskModel()->createAndSave([
                    'id_account' => $this->getCurrentUserAccount()->getId()
                ] + iterator_to_array($form->getData()));

                $this->addFlash('success', "Wesh cimère dude!");

                return $this->redirectToRoute('jaccob_task.list');

            } else {
                $this->addFlash('danger', "Something wrong happened, please check form data!");
            }
        }

        return $this->render('JaccobTaskBundle:Task:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * List
     */
    public function listAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }

    /**
     * Starred
     */
    public function listStarredAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
            ->andWhere("is_starred = $*", [1])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }

    /**
     * Deadline
     */
    public function listDeadlineAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
            ->andWhere("is_done = $*", [0])
            ->andWhere("ts_deadline < $*", [(new \DateTime())->format('Y-m-d H:i:s')])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }
}
