<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Tag;
use App\Entity\TagTest;
use App\Entity\Test;
use App\Entity\User;
use App\Form\EditType;
use App\Form\PlayersByTeam;
use App\Form\PlayersTeamType;
use App\Form\ResultCurrentUserType;
use App\Form\ResultType;
use App\Form\UserType;
use App\Repository\ActivityRepository;
use App\Repository\ResultRepository;
use App\Repository\TagRepository;
use App\Repository\TagTestRepository;
use App\Repository\TeamRepository;
use App\Repository\TestRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class CommonController extends AbstractController
{
    /**
     * @Route("/home/{slug}", name="user_home")
     * 
     * @return Response
     */
    public function home(User $user): Response
    {
        return $this->render('common/home.html.twig',['user'=> $user]);
    }

    /**
     * @Route("/history/{slug}", name="history")
     *
     * @return Response
     */
    public function history(User $user) : Response
    {   

        return $this->render('common/test-history.html.twig',['user'=> $user]);
    }

    /**
     * @Route("/tests", name="tests")
     *
     * @return Response
     */
    public function tests() : Response
    {
        return $this->render('common/all_tests.html.twig');
    }

    /**
     * @Route("/test/detail/{slug}", name="detail_test")
     *
     * @return Response
     */
    public function detailTest(Test $test) : Response
    {
        
        return $this->render('common/detail_test.html.twig', ['test' => $test]);
    }

    /**
     * @Route("/tests/list", name="list_tests")
     *
     * @return Response
     */
    public function listTests(TestRepository $testRepository) : Response
    {

        $listTests = $testRepository->findAll();

        return $this->render('common/list_tests.html.twig', ['listTests' => $listTests]);
    }
    /**
     * @Route("/tests/physique", name="test_physique")
     *
     * @return Response
     */
    public function testPhysique(TagRepository $tagRepository) : Response
    {

        $allTestByTag = $tagRepository->allTestForPrimaryTag("Physique");
        
        $tabAllTags = [];
        foreach ($allTestByTag as $allTag) {
            foreach ($allTag as $tags) {                
                $tabAllTags [] = $tagRepository->allTagForPrimaryTag("Physique", $tags); 
            }
        }
        
        $tabTags = [];
        foreach ($tabAllTags as $tags) {
            foreach ($tags as $tag) {
                $tabTags [] =($tag['name']);
            }
        }

       $ListTag = array_unique($tabTags);

        return $this->render('common/physical_tests.html.twig',[
            'tags'=>$tagRepository->findAll(),
             'listTag' => $ListTag 
            ]);
    }

    /**
     * @Route("/tests/technique", name="test_technique")
     *
     * @return Response
     */
    public function testTechnique(TagRepository $tagRepository) : Response
    {   
        $allTestByTag = $tagRepository->allTestForPrimaryTag("Technique");
        //$tag->allTagForPrimaryTag("Technique", $tag);  

        $tabAllTags = [];
        foreach ($allTestByTag as $allTag) {
            foreach ($allTag as $tags) {                
                $tabAllTags [] = $tagRepository->allTagForPrimaryTag("Technique", $tags); 
            }
        }
        
        $tabTags = [];
        foreach ($tabAllTags as $tags) {
            foreach ($tags as $tag) {
                $tabTags [] =($tag['name']);
            }
        }

        $ListTag = array_unique($tabTags);
      
        return $this->render('common/technical_tests.html.twig',[
                        'tags'=>$tagRepository->findAll(),
                        "listTag" => $ListTag ]);
    }

    

    /**
     * @Route("/tests/{id}", name="one_test",  requirements={"page"="\d+"}, methods={"GET", "POST"})
     *
     * @return Response
     */
    public function registerTest(Request $request, Test $test, UserInterface $userInterface, EntityManagerInterface $manager) : Response
    {       
        $result = new Result();
        $form = $this->createForm(ResultType::class, $result);
        
        if (!empty($_POST["result"])) {
            dd($_POST["result"]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result->setTest($test);
            $result->setDoneAt( new DateTime('now'));
            $post = $request->get('result');
            $userFromRequest = $post['user'];
            if(in_array("ROLE_COACH",$userInterface->getRoles()) && $userFromRequest != $userInterface->getId()){
                $result->setStatus(1);
            }else{
                $result->setStatus(0);
            }

       
            
            $manager->persist($result);
            $manager->flush();
            $this->addFlash('success', 'Résultat enregistré.');
            return $this->redirectToRoute('one_test',['id'=>$test->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('common/one_test.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }

    /**
     * Function editUser
     *
     * @Route("/{slug}/profil", name="profilpage", methods = {"GET", "POST"})
     */
    public function editUser(Request $request, EntityManagerInterface $entityManager, User $user, UserInterface $userInterface): Response
    {
        $user = $this->getUser();

        /* We create an object $filesystem which allowed us all the function of this class, as remove() */
        $filesystem = new Filesystem();        

        $form = $this->createForm(EditType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            
            $avatarFile = $form->get('picture')->getData();
            

            //If there is some data in the field picture, we treat them
            if($avatarFile != null){

                /* https://symfony.com/doc/current/components/filesystem.html#remove */
             
                /* If there was already a picture, we removed it*/
                if ($user->getPicture() != null) {
                    /*we get all the path of the picture*/
                $file = new File($this->getParameter('uploads_directory') .'/'. $user->getPicture());
                $filesystem->remove($file);
                }

                $newFilename = 'user'.uniqid().'.'.$avatarFile->guessExtension();
                
                // Move the file to the directory where avatars are stored
                 try {
                    $avatarFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    /*If a error occurs*/
                    } catch (FileException $e) { 
                        $this->addFlash('error', 'Une erreur est survenue. Essayer à nouveau');
                        return $this->redirectToRoute('user_home', ['slug'=>$userInterface->getSlug()], Response::HTTP_SEE_OTHER); 
                    }

                $user->setPicture($newFilename);
            
                /* $entityManager->persist($user); */
                        
            }


            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a été modifié avec succès.');
            return $this->redirectToRoute('user_home', ['slug'=>$userInterface->getSlug()], Response::HTTP_SEE_OTHER); 
        }


        /*display the form*/
        return $this->renderForm('home/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);

    }

    /**

     * Function editPassword
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     * @param UserPasswordHasherInterface $encoder
     * @param UserInterface $userInterface
     * @return void
     * @Route("/{slug}/profil/password", name ="editpassword", methods = {"GET", "POST"})
     */
    public function editPassword(Request $request, EntityManagerInterface $entityManager, User $user, UserPasswordHasherInterface $encoder, UserInterface $userInterface)
    {
        /* $user = $this->getUser(); */
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Is there a new password ?
            if ($form-> get('password')->getData()) {
                // if yes, we hashe the new password
                $hashedPassword = $encoder->hashPassword($user, $form->get('password')->getData());
                // we set the new password
                $user->setPassword($hashedPassword);

                $this->addFlash('success', 'Le mot de passe vient d\'être modifié avec succès.');
            }
            /* $entityManager->persist($user); */
            $entityManager->flush();

            return $this->redirectToRoute('profilpage', ['slug'=>$userInterface->getSlug()], Response::HTTP_SEE_OTHER);
        }
        /*display the form*/
        return $this->renderForm('home/password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/teamFromResult/{id}", name="team_result", methods={"GET","POST"})
     *
     * @return void
     */
    public function TeamFromResult(Request $request,ActivityRepository $activityRepository)
    {      
        $id = $request->attributes->get('id');
        $players = $activityRepository->findBy(['team' => $id]);
        foreach($players as $player){
            if($player->getRole() != 1){
                $data [] = $player->getUser();
            }
            
            
        }
        try {
            return $this->json(
                    // les données à transformer en JSON
                    $data,
                    // HTTP STATUS CODE
                    200,
                    // HTTP headers supplémentaires, dans notre cas : aucune
                    [],
                    // Contexte de serialisation, les groups de propriété que l'on veux serialise
                    ['groups' => ['show_users']]
            );
    
         } catch (Exception $e){ // si une erreur est LANCE, je l'attrape
            // je gère l'erreur
            // par exemple si tu me file un genre ['3000'] qui n existe pas...
             return new JsonResponse("Hoouuu !! Ce qui vient d'arriver est de votre faute : JSON invalide", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    /**
     * @Route("testFromResult/{id}", name="test_result", methods={"GET", "POST"},requirements={"id" = "\d+"})
     *
     * @return void
     */
    public function testFromResult(Request $request, TestRepository $testRepository)
    {
        $id = $request->attributes->get('id');
        $test = $testRepository->findBy(['id'=> $id]);
        try {
            return $this->json(
                    // les données à transformer en JSON
                    $test,
                    // HTTP STATUS CODE
                    200,
                    // HTTP headers supplémentaires, dans notre cas : aucune
                    [],
                    // Contexte de serialisation, les groups de propriété que l'on veux serialise
                    ['groups' => ['show_test']]
            );
    
         } catch (Exception $e){ // si une erreur est LANCE, je l'attrape
            // je gère l'erreur
            // par exemple si tu me file un genre ['3000'] qui n existe pas...
             return new JsonResponse("Hoouuu !! Ce qui vient d'arriver est de votre faute : JSON invalide", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/testmyself/{id}", name="test_myself", methods= {"GET","POST"}, requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param UserInterface $userInterface
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function ResultCurrentUser(User $user,Request $request, UserInterface $userInterface, EntityManagerInterface $manager, SessionInterface $session, $id)
    {
        $result = new Result();
        $form = $this->createForm(ResultCurrentUserType::class, $result);
       
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // si je ne suis pas coach et que je suis pas le user courant 
            // j'ai pas le droitd'envoyer un résultat pour un autre joueur
            if ( (!in_array("ROLE_COACH",$userInterface->getRoles())) && ($userInterface->getId() != $id )) {
                return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
            }


            $result->setDoneAt( new DateTime('now'));
            $post = $request->get('result_current_user');
            $test = $post['test'];
            $result->setUser($user);
            if(in_array("ROLE_COACH",$userInterface->getRoles()) && $userInterface->getId()!= $user->getId()){
                $result->setStatus(1);
            }else{
                $result->setStatus(0);
            }
            
            $manager->persist($result);
            $manager->flush();

            return $this->redirectToRoute('app_chart',['id'=> $test,'slug'=> $user->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('common/currentUserTest.html.twig', [
            'form' => $form,
        ]);
    }

    
}
