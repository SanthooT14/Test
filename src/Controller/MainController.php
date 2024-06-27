<?php

namespace App\Controller;

use App\Form\EmployeeType;
use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $employee = $this->em->getRepository(Employee::class)->findAll();

        return $this->render('main/index.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/create-employee', name: 'create-employee')]
    public function createEmployee(Request $request)
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($employee);
            $this->em->flush();

            $this->addFlash('message', 'Employee creation successful');
            return $this->redirectToRoute('app_main');
        }
        return $this->render('main/employee.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('edit-employee/{id}', name:'edit-employee')]
    public function editEmployee( Request $request,$id)
    {
        $employee = $this->em->getRepository(Employee::class)->find($id);

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($employee);
            $this->em->flush();

            $this->addFlash('message', 'Employee update successful');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/employee.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('delete-employee/{id}', name:'delete-employee')]
    public function deleteEmployee($id)
    {
        $employee = $this->em->getRepository(Employee::class)->find($id);

        $this->em->remove($employee);
        $this->em->flush();

        $this->addFlash('message', 'Employee deleted successfully');
        return $this->redirectToRoute('app_main');
    }
}

