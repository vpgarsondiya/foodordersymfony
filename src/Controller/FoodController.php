<?php

namespace App\Controller;

use App\Entity\Food;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        return $this->render('index.html.twig');
    }


    /**
     * @Route("/all", name="all")
     */
    public function all( Request $request, PaginatorInterface $paginator): Response
    {
        $tasks = $this->getDoctrine()->getRepository(Food::class)->findBy([],['id'=>'DESC']);

        $paginatedTasks = $paginator->paginate($tasks, $request->query->getInt('page', 1), 5);

        return $this->render('all.html.twig', ['tasks' => $paginatedTasks]);
    }


     /**
     * @Route("/create", name="create-task", methods={"POST"})
     */
    public function create(Request $request) {

        $name = $request->request->get('task');

        $price = $request->request->get('price');

        $stock = $request->request->get('stock');

        $description = $request->request->get('description');



        $objectManager = $this->getDoctrine()->getManager();

        $lastTask = $objectManager->getRepository(Food::class)->findOneBy([], ['id' => 'desc']);

        $lastId = $lastTask->getId();

        $newId = $lastId + 1;

        $task = new Food;

        $task->setId($newId);

        $task->setName($name);

        $task->setPrice($price);

        $task->setStock($stock);

        $task->setDescription($description);

        $objectManager->persist($task);

        $objectManager->flush();

        $this->addFlash('success', 'You have created a new task!');

        return $this->redirectToRoute('all');

    }

     /**
     * @Route("/updateStatus/{id}", name="update-status")
     */
    
    // public function update(ManagerRegistry $doctrine, int $id, Request $request): Response
    // {
    //     $entityManager = $doctrine->getManager();
    //     $task = $entityManager->getRepository(Food::class)->find($id);

    //     if (!$task) {
    //         throw $this->createNotFoundException(
    //             'No product found for id '.$id
    //         );
    //     }



    //     $task->setName($name);
    //     $entityManager->flush($task);
    //     $this->addFlash('info', 'You have updated a task!');

    //     return $this->redirectToRoute('create', [
    //         'id' => $task->getId()
    //     ]);
    // }




    public function updateTaskStatus($id) {

        $objectManager = $this->getDoctrine()->getManager();

        $task = $objectManager->getRepository(Food::class)->find($id);

        $task->setStatus(!$task->isStatus());
        
        $objectManager->flush();

    

        return $this->redirectToRoute('all');

    }


    /**
     * @Route("/deleteTask/{id}", name="delete-task")
     */
    public function delete(Food $id) {

        $objectManager = $this->getDoctrine()->getManager();

        $objectManager->remove($id);

        $objectManager->flush();

        $this->addFlash('danger', 'You have deleted a task!');

        return $this->redirectToRoute('all');

    }


}
