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

        // On récupère la tâche
        $tasks = trim($request->request->get('task'));

        // On vérifie si $tasks n'est pas un champ vide
        if (empty($tasks))  {

            // On ajoute un message pour dire que ça ne va pas fonctionner
            $this->addFlash('danger', 'La tâche ' .$tasks. 'est vide et n\'a pas été ajouté.');
        } else {

            //TodoModel::add($request->request->get('task')); 
            $task = new TodoModel();
            $task->add($tasks);
            
            $this->addFlash('success', 'La tâche "' .$tasks. '" a bien été ajoutée.');
        }

        // on redirige l'utilisateur vers la liste des tâches.
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Suppréssion d'une tâche
     *
     * @Route("/todo/delete", name="todo_delete", methods={"POST"})
     *
     */
    public function todoDelete(Request $request) {
        // On reçoit l'id depuis un formulaire
        $id = $request->request->get('task_id');

        // On instancie TodoModel pour récupérer l'id de la tâche
        $task = TodoModel::find($id);

        if ($id !== false && is_numeric($id) && $task != false) {
            // on supprime notre tâche
            TodoModel::delete($id);
            // on prévient que la suppresiion est faite
            $this->addFlash('success', 'La tâche a bien été supprimée');
        } else {
            $this->addFlash('danger', 'Une erreur est survenu, la tâche n\'a pas éte supprimée');
        }

        // La suppression est ok, on redirige l'utilisateur vers la page des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Liste des tâches
     *
     * @Route("/todos/reset", name="todos_reset", methods={"GET"})
     */
    public function resetMessage() 
    {
        // Appel à la méthode reset de TodoModel
        TodoModel::reset();

        // On ajoute un message flash qui sera affiché la prochaine fois qu'on voit la liste des tâches
        $this->addFlash('success', 'Les tâches ont été réinitialisées');

        // On redirige l'utilisateur sur la liste des tâches
        return $this->redirectToRoute('todo_list');   
    }
}
