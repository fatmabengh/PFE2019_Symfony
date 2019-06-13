<?php

namespace App\Controller;

use App\Entity\Fields;
use App\Entity\Form;
use App\Entity\MailTrace;
use App\Entity\User;
use http\Env\Response;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DateTime;

class FormController extends AbstractController
{
    /**
     * @Route(path="api/form",name="form")
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $json = json_decode($request->getContent(false), true);
        $dateModif = new \DateTime();
        $form = new Form($json["title"], $json["description"], $dateModif);
        $form->setExpirationDate($json["expiration_date"]);

        $user = $em->find(User::class, $json["user_id"]);
        if (!$user) {
            return new JsonResponse('there\'s no such user in the database');
        }
        $form->setUser($user);
        $fields = $json['fields'];
        foreach ($fields as $field) {
            $quest = new Fields($field['label'], $field['subtitle'], $field['types']);
            $quest->setForm($form);
            $quest->setItems($field['items']);
            $quest -> setObligation($field['obligation']);

            $em->persist($quest);
            $em->flush();
        }
        $em->persist($form);
        $em->flush();
        return new JsonResponse($form->getId());
    }

    /**
     * @Route(path="api/userForm/{id}",name="")
     */
    public function getUserFormsAction( $id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        //var_dump($form);die();
        if (!$user) {
            return new JsonResponse('there\'s no such a form id in the database');
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $formsArray = array();

        foreach ($user->getForms() as $form) {
            $questionsArray = array();
            foreach ($form->getFields() as $field) {
                $questionsData = array(
                    'label' => $field->getLabel(),
                    'subtitle' => $field->getSubtitle(),
                    'types' => $field->getTypes(),
                    'items' => $field->getItems(),
                );
                array_push($questionsArray, $questionsData);
            }
            $data = array(
                'id' => $form->getId(),
                'title' => $form->getTitle(),
                'description' => $form->getDescription(),
                'date_modif' => $form->getDateModif(),
                'expiration_date'=> $form->getExpirationDate(),
                'field' => $questionsArray,
            );


            array_push($formsArray, $data);

        }
        return new JsonResponse($formsArray);
    }

    /**
     * @Route(path="api/getForm/{id}")
     *
     */
    public function showFormAction( $id)
    {
        $form = $this->getDoctrine()
            ->getRepository(Form::class)
            ->find($id);
        if (!$form) {
            return new JsonResponse('there\'s no such a form id in the database');
        }

        $questionsArray = array();

        foreach ($form->getFields() as $field) {
            $questionsData = array(
                'id' =>$field -> getId(),
                'label' => $field->getLabel(),
                'subtitle' => $field->getSubtitle(),
                'types' => $field->getTypes(),
                'items' => $field->getItems(),
            );
            array_push($questionsArray, $questionsData);
        }
        $data = array(
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'description' => $form->getDescription(),
            'date_modif' => $form->getDateModif(),
            'field' => $questionsArray,
        );

        return new JsonResponse($data);
    }

    /**
     * @Route(path="/api/deleteform/{id}",name="delete_form")
     *
     */
    public function deleteAction($id)
    {
        $form = $this->getDoctrine()
            ->getRepository(Form::class)
            ->find($id);
        if (!$form) {
            return new JsonResponse('there is no such a form id in the database');
        }
        foreach ($form->getFields() as $field) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($field);
            $em->flush();

        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($form);
        $em->flush();
        return new JsonResponse('form deleted');
    }

    /**
     * @Route(path="/api/UpdateForm/{id}",name="update_form")
     */
    public function UpdateAction($id, Request $request)
    {

        $form = $this->getDoctrine()
            ->getRepository(Form::class)
            ->find($id);
        if (!$form) {
            return new JsonResponse('there\'s no such a form id in the database');
        }
        $em = $this->getDoctrine()->getManager();
        $formToUpdate = $this->getDoctrine()->getRepository(Form::class)->find($id);

        $json = json_decode($request->getContent(false), true);

        $formToUpdate->setTitle($json["title"]);
        $formToUpdate->setDescription($json["description"]);
        $dateModif = new \DateTime();
        $formToUpdate->setDateModif($dateModif);

foreach ($json["fields"] as $fielde){
if (isset($fielde['id'])){
    $fieldToUpdate = $this->getDoctrine()->getRepository(Fields::class)->find($fielde['id']);
    $fieldToUpdate-> setLabel($fielde['label']);
    $fieldToUpdate-> setSubtitle($fielde['subtitle']);
    $fieldToUpdate-> setTypes( $fielde['types']);
    $fieldToUpdate-> setId($fielde['id']);
    $fieldToUpdate->setItems($fielde['items']);
    $fieldToUpdate->setForm($formToUpdate);
   $em->persist($fieldToUpdate);
    $em->flush();

       }
else {

    $newField= new Fields($fielde['label'], $fielde['subtitle'], $fielde['types']);
    $newField->setItems($fielde['items']);
    $newField->setForm($formToUpdate);
    $em->persist($newField);
    $em->flush();

    }
}
        $em->persist($formToUpdate);
        $em->flush();
        return new JsonResponse('form updated');
    }


/**
 * @Route(path="/api/SendMailForm/{id}",name="SendMail_form")
 */
public function SendMailFormAction(Request $request, \Swift_Mailer $mailer,$id)
{
    $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);



        $json = json_decode($request->getContent(false), true);
         $message = (new \Swift_Message())
            ->setSubject($json['subject'])
            ->setFrom(['fatoumaf16@gmail.com'=> 'stagePFE Sofia Holding'])
            ->setTo($json['recipient'])
            ->setBody($json['body']);

foreach ($message->getFrom() as $senders){
    $sender= $senders;
}

        $receiver= $json['recipient'];
        $Mailbody=$json['body'];
        $subject=$json['subject'];
    $traceMailer= new MailTrace( $sender, $receiver, $Mailbody, $subject);
    $traceMailer->setUser($user);

    $em->persist($traceMailer);
    $em->flush();
    $mailer->send($message);
    return new JsonResponse('mail sent successfully');

}
    /**
     * @Route(path="/api/getMailTrace/{id}",name="getMailTrace")
     *
     */
    public function GetMailTrace($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $TraceArray = array();
        foreach ($user->getMailTraces() as $tracemail) {
            $TraceData = array(
                'sender'  =>$tracemail -> getSender(),
                'receiver'=>$tracemail -> getReceiver(),
                'Mailbody'=>$tracemail -> getMailbody(),
                'subject'=>$tracemail -> getSubject(),
            );
            array_push($TraceArray, $TraceData);
        }



        return new JsonResponse($TraceArray);
        }



    /**
     * @Route(path="getForm/{id}")
     *
     */
    public function showAction($id)
    {
        $form = $this->getDoctrine()
            ->getRepository(Form::class)
            ->find($id);
        if (!$form) {
            return new JsonResponse('there\'s no such a form id in the database');
        }

        $questionsArray = array();

        foreach ($form->getFields() as $field) {
            $questionsData = array(
                'id' =>$field -> getId(),
                'label' => $field->getLabel(),
                'subtitle' => $field->getSubtitle(),
                'types' => $field->getTypes(),
                'items' => $field->getItems(),
                'obligation' => $field -> getObligation()
            );
            array_push($questionsArray, $questionsData);
        }
        $data = array(
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'description' => $form->getDescription(),
            'date_modif' => $form->getDateModif(),
            'field' => $questionsArray,
        );

        return new JsonResponse($data);
    }

}