<?php

namespace ExampleBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

use ExampleBundle\Entity\StackOverFlower;

class StackOverFlowerController extends FOSRestController
{
    /**
     * findStackOverFlowerByRequest
     * 
     * @param Request $request
     * @return StackOverFlower
     * @throws NotFoundException
     */
    private function findStackOverFlowerByRequest(Request $request) {
        
        $id = $request->get('id');
        $user = $this->getDoctrine()->getManager()->getRepository("ExampleBundle:StackOverFlower")->findOneBy(array('id' => $id));
        
        return $user;
    }
    
    /**
     * validateAndPersistEntity
     * 
     * @param StackOverFlower $user
     * @param Boolean $delete
     * @return View the view
     */
    private function validateAndPersistEntity(StackOverFlower $user, $delete = false) {
        
        $template = "ExampleBundle:StackOverFlower:example.html.twig";
        
        $validator = $this->get('validator');
        $errors_list = $validator->validate($user); 
        
        if (count($errors_list) == 0) {
            
            $em = $this->getDoctrine()->getManager();
            
            if ($delete === true) {
                $em->remove($user);
            } else {
                $em->persist($user);
            }
            
            $em->flush();
            
            $view = $this->view($user)
                         ->setTemplateVar('user')
                         ->setTemplate($template);
        } else {
            
            $errors = "";
            foreach ($errors_list as $error) {
                $errors .= (string) $error->getMessage();  
            }
            
            $view = $this->view($errors)
                         ->setTemplateVar('errors')
                         ->setTemplate($template);
            
        } 
        
        return $view;
    }
    
    /**
     * newStackOverFlowerAction
     * 
     * "new_stackoverflower" [POST] /stackoverflower/new/[example-name] 
     * 
     * @Post("/stackoverflower/new/{name}")
     * 
     * @param Request $request
     * @return String
     */
    public function newStackOverFlowerAction(Request $request)
    {   
        $user = new StackOverFlower();
        $user->setName($request->get('name'));
        
        $view = $this->validateAndPersistEntity($user);
            
        return $this->handleView($view);
    }
      
    /**
     * editStackOverFlowerAction
     * 
     * "edit_stackoverflower" [POST] /user/edit/[example-id]/[example-name]
     * 
     * @Post("/stackoverflower/edit/{id}/{name}")
     * 
     * @param Request $request
     * @return type
     */
    public function editStackOverFlowerAction(Request $request) {
        
        $user = $this->findStackOverFlowerByRequest($request);
        
        if (! $user) {
            $view = $this->view("No StackOverFlower found for this id:". $request->get('id'), 404);
            return $this->handleView($view);
        }
        
        $user->setName($request->get('name'));
        
        $view = $this->validateAndPersistEntity($user);
                
        return $this->handleView($view);
    }
    
    /**
     * deleteStackOverFlowerAction
     * 
     * "get_user" [DELETE] /stackoverflower/delete/[example-id]
     * 
     * @Delete("/stackoverflower/delete/{id}")
     * 
     * @param Request $request
     * @return type
     */
    public function deleteStackOverFlowerAction(Request $request) {
        
        $user = $this->findStackOverFlowerByRequest($request);
        
        if (! $user) {
            $view = $this->view("No StackOverFlower found for this id:". $request->get('id'), 404);
            return $this->handleView();
        }
        
        $view = $this->validateAndPersistEntity($user, true);
                
        return $this->handleView($view);
    }
    
    /**
     * getStackOverFlowerAction
     * 
     * @Get("/stackoverflowers")
     * 
     * @param Request $request
     * @return type
     */
    public function getStackOverFlowerAction(Request $request) {
        
        $template = "ExampleBundle:StackOverFlower:example.html.twig";
        
        $users = $this->getDoctrine()->getManager()->getRepository("ExampleBundle:StackOverFlower")->findAll();
        
        if (count($users) === 0) {
            $view = $this->view("No StackOverFlower found.", 404);
            return $this->handleView();
        }
        
        $view = $this->view($users)
                     ->setTemplateVar('users')
                     ->setTemplate($template);
        
        return $this->handleView($view);
    }
}