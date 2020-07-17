<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * Liste toutes les tâches
     * @Route("/task", name="task")
     */
    public function index()
    {
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Affichage du formulaire
     * @Route("/task/new", name="task_new")
     */
    public function new(Request $request)
    {
        // Utilisation d'un formulaire à partir de TaskType
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        // Traitement des informations reçus en POST
        $form->handleRequest($request);

        // On vérifie si les informations on été envoyées et si elles sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            // Une fois les infos récupérés on peut persister et flush
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
        }

        return $this->render('task/new.html.twig', [
            'taskForm' => $form->createView()
        ]);
    }   
}
