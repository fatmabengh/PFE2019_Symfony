<?php

namespace App\Controller;

use App\Entity\Fields;
use App\Entity\Form;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FieldsController extends AbstractController
{
    /**
     * @Route(path="api/fields", name="fields")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $json = json_decode($request->getContent(false), true);
        $label = $json["label"];
        $subtitle = $json["subtitle"];
        $types = $json["types"];
        $items= $json["items"];
        $form_id = $json["form_id"];
        $fields=new Fields($label, $subtitle,$types);
        $fields->setItems($items);
        $form = $em->find(Form::class, $form_id);
        $fields->setForm($form);
        $em->persist($fields);
        $em->flush();
        return new JsonResponse('new fields added');
    }

    /**
     * @Route(path="api/getFieldForm",name="get_form_field")
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fields = $em->getRepository(Fields::class)->findAll();
        $stack = array();

        foreach (  $fields as $field) {
            $form = $field->getForm();
            $datafield=array(
                'label' => $field->getLabel(),
                'subtitle' => $field->getSubtitle(),
                'types' => $field->getTypes(),
                'items' => $field->getItems(),
                'form' => array(
                    'id' => $form-> getId(),
                    'title' => $form->getTitle(),
                    'description' => $form->getDescription(),
                    'date_modif' => $form->getDateModif(),
                )
            );


            array_push($stack, $datafield);
        }

        return new JsonResponse($stack);

    }

    /**
     * @Route(path="api/deleteField/{id}",name="Delete_field")
     */
    public function DeleteAction($id){
        $question = $this->getDoctrine()
            ->getRepository(Fields::class)
            ->find($id);
        if (!$question) {
            return new JsonResponse('there\'s no such a field in this form');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($question);
        $em->flush();
        return new JsonResponse('field deleted');
    }

    /**
     * @Route(path="api/getField/{id}",name="get_fieldByID")
     *
     */
    public function listFieldAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $field = $em->getRepository(Fields::class)-> find($id);

            $datafield=array(
                'id' => $field -> getId(),
                'label' => $field->getLabel(),
                'items' => $field->getItems(),
            );

        return new JsonResponse($datafield);

    }

}
