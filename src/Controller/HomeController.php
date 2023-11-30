<?php
// src/Controller/VoyageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\NewCommentFormType;
use App\Form\ContactForm;
use App\Entity\Comment;
use App\Service\SendMailService;
use App\Repository\TravelRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{

    private $sendMailService;

    public function __construct(SendMailService $sendMailService)
    {
        $this->sendMailService = $sendMailService;
    }
    
    #[Route('/', name: 'app_home')]
    public function home(TravelRepository $travelRepo): Response
    {

        $travels = $travelRepo->findAll();
        $reversedTravels = array_reverse($travels);

        return $this->render('Home/index.html.twig', [
            'controller' => 'Home Controller',
            'travels' => $reversedTravels,
            'path' => 'src/Controller/HomeController.php',
            'message' => 'Welcome to my home page',
        ]);
    }

    #[Route("/travels", name: "app_list_travels")]
    public function listTravels(TravelRepository $travelRepo): Response
    {

        $travels = $travelRepo->findAll();

        return $this->render('Home/home.html.twig', [
            'controller' => 'list_travels',
            'travels' => $travels,
            'path' => 'src/Controller/HomeController.php',
            'message' => 'Welcome to my travels list',
        ]);
    }

    
    #[Route("/travel/{id}", name: "app_travel_detail")]
    public function travelDetail(int $id, TravelRepository $travelRepo, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $travel = $travelRepo->find($id);
        $comments = $travel->getComments();
    
        // Créer une nouvelle instance du formulaire
        $comment = new Comment();
        $comment->setTravel($travel);
        $form = $formFactory->create(NewCommentFormType::class, $comment, ['travel' => $travel->getId()]);
    
        // Gérer la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez le commentaire dans la base de données, en associant le voyage
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Rafraîchir la liste des commentaires après l'ajout d'un nouveau commentaire
            $comments = $travel->getComments();
        }
    
        return $this->render('Home/travel.html.twig', [
            'controller' => 'Home Controller',
            'travel' => $travel,
            'comments' => $comments,
            'path' => 'src/Controller/HomeController.php',
            'message' => 'Welcome to my travel details',
            'form' => $form->createView(),  // Passer le formulaire à la vue
        ]);
    }

    
    #[Route("/contact", name: "app_contact")]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactForm::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
    
            // Utilisez le service SendMailService pour envoyer l'e-mail
            $this->sendMailService->send(
                $data['sender'],
                $data['recipient'],
                $data['subject'],
                'contact', // Remplacez par le nom de votre template email
                ['message' => $data['message']]
            );
    
            $this->addFlash('success', 'L\'e-mail a été envoyé avec succès.');
    
            // Redirigez vers une autre page ou effectuez d'autres actions
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('Home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}