<?php

namespace App\Controller;

use App\Entity\ToDoItem;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(ToDoItem::class);
        $items = $repo->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'items' => $items
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $item = new ToDoItem();
        $iduser = $request->request->get('iduser');
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($iduser);
        $item->setText($request->request->get('text'))
            ->setCreatedAt(new \DateTime())
            ->setIsDone(false)
            ->setCreatedby($user);

        $manager->persist($item);
        $manager->flush();
        $lastid = $item->getId();
        return new JsonResponse($lastid);
    }

    /**
     * @Route("/delete", name="delete")
     */
    public function delete(Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(ToDoItem::class);
        $idItem = $request->request->get('id');
        $itemToRemove = $repo->find($idItem);
        $manager->remove($itemToRemove);
        $manager->flush();
        return new JsonResponse('ok');
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(ToDoItem::class);
        $idItem = $request->request->get('id');
        $text = $request->request->get('text');
        $itemToEdit = $repo->find($idItem);
        $itemToEdit->setText($text);
        $manager->flush();
        return new JsonResponse($itemToEdit->getIsDone());
    }

    /**
     * @Route("/setisdone", name="setisdone")
     */
    public function setisdone(Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(ToDoItem::class);
        $idItem = $request->request->get('id');
        $isDone = $request->request->get('isdone');
        $itemToEdit = $repo->find($idItem);
        if ($isDone == "true") {
            $itemToEdit->setIsDone(1);
        } else {
            $itemToEdit->setIsDone(0);
        }

        $manager->flush();
        return new JsonResponse('ok');
    }

    /**
     * @Route("/showall", name="showall")
     */
    public function showall(Request $request, EntityManagerInterface $manager)
    {
        $listItem = $manager->createQuery('SELECT item FROM App\Entity\ToDoItem item WHERE item.createdby=:userid')
            ->setParameter('userid', $this->getUser()->getId())
            ->getArrayResult();
        return new JsonResponse($listItem);
    }

    /**
     * @Route("/showactive", name="showactive")
     */
    public function showactive(Request $request, EntityManagerInterface $manager)
    {
        $listItem = $manager->createQuery('SELECT item FROM App\Entity\ToDoItem item WHERE item.createdby=:userid AND item.isDone=0')
            ->setParameter('userid', $this->getUser()->getId())
            ->getArrayResult();
        return new JsonResponse($listItem);
    }

    /**
     * @Route("/showcompleted", name="showcompleted")
     */
    public function showcompleted(Request $request, EntityManagerInterface $manager)
    {
        $listItem = $manager->createQuery('SELECT item FROM App\Entity\ToDoItem item WHERE item.createdby=:userid AND item.isDone=1')
            ->setParameter('userid', $this->getUser()->getId())
            ->getArrayResult();
        return new JsonResponse($listItem);
    }
}
