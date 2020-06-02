<?php

namespace App\Controller;

use App\Model\TodoModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("/todos", name="todo_list", methods={"GET"})
     */
    public function todoList()
    {
        $todos = TodoModel::findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/todo/{id}", name="todo_show", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function todoShow($id)
    {
        $todo = TodoModel::find($id);

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * Changement de statut
     *
     * @Route(
     *  "/todo/{id}/{status}", name="todo_set_status", requirements={"id": "\d+", "status": "done|undone"}, methods={"GET"})
     */
    public function todoSetStatus($id, $status)
    {
        // Change le statut d'une tâche
        TodoModel::setStatus($id, $status);

        // On ajoute un message flash, càd un message qu'on stocke dans la session et qui sera affiché juste quand on trouvera que ce sera le bon moment
        $this->addFlash('success', 'La tâche #' .$id. ' est bien marqué comme ' .$status);

        // On redirige l'utilisateur vers la liste des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/todo/add", name="todo_add", methods={"POST"})
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function todoAdd(Request $request)
    {
        // Teste l'ajout d'une nouvelle tâche
        // dump($request->request->get('task'));exit;

        //TodoModel::add($request->request->get('task')); 
        $task = new TodoModel();
        $task->add($request->request->get('task'));

        // on redirige l'utilisateur vers la liste des tâches.
        return $this->redirectToRoute('todo_list');

    }
}
