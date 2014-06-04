<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;
use Design311\WebsiteBundle\Entity\DinnerParticipants;


class AjaxController extends BaseController
{
    public function likeRecipeAction($permalink)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);

        $response = $recipe->getLikedBy()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addLikedBy($this->getUser());
        }
        else{
            $recipe->removeLikedBy($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new JsonResponse(array(
            'status' => $response
        ));
    }

    public function saveRecipeAction($permalink)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);

        $response = $recipe->getSavedBy()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addSavedBy($this->getUser());
        }
        else{
            $recipe->removeSavedBy($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new JsonResponse(array(
            'status' => $response
        ));
    }

    public function shopRecipeAction($permalink)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);

        $response = $recipe->getShoppinglistFrom()->contains($this->getUser()) ? 0 : 1;

        if ($response === 1) {
            $recipe->addShoppinglistFrom($this->getUser());
        }
        else{
            $recipe->removeShoppinglistFrom($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return new JsonResponse(array(
            'status' => $response,
            'shoppinglistcount' => count($this->getUser()->getShoppinglist())
        ));
    }

    public function declineDinnerParticipantAction($participantId)
    {
        $participantRequest = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerParticipantRequest')->find($participantId);
                
        if ($participantRequest == null) {
            $this->get('session')->getFlashBag()->add('error','Deelnameverzoek niet gevonden');
            return $this->redirect($this->generateUrl('design311website_dinners'));
        }

        if ($this->getUser() == $participantRequest->getDinner()->getUser()) {

            $dinner = $participantRequest->getDinner(); //needed for redirect

            $em = $this->getDoctrine()->getManager();
            $em->remove($participantRequest);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Je hebt het verzoek geweigerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function acceptDinnerParticipantAction($participantId)
    {
        $participantRequest = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerParticipantRequest')->find($participantId);
        
        if ($participantRequest == null) {
            $this->get('session')->getFlashBag()->add('error','Deelnameverzoek niet gevonden');
            return $this->redirect($this->generateUrl('design311website_dinners'));
        }

        if ($this->getUser() == $participantRequest->getDinner()->getUser()) {

            $participant = new DinnerParticipants();
            $participant->setUser($participantRequest->getUser());
            $participant->setDinner($participantRequest->getDinner());

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->remove($participantRequest);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Je hebt het verzoek geaccepteerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $participantRequest->getDinner()->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function declineDinnerInviteAction($inviteId)
    {
        $invite = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerInvite')->find($inviteId);

        if ($invite == null) {
            $this->get('session')->getFlashBag()->add('error','Uitnodiging niet gevonden');
            return $this->redirect($this->generateUrl('design311website_dinners'));
        }

        var_dump($this->getUser()->getEmail());
        var_dump($invite->getEmail());
        
        if ($this->getUser()->getEmail() == $invite->getEmail()) {

            $dinner = $invite->getDinner(); //needed for redirect

            $em = $this->getDoctrine()->getManager();
            $em->remove($invite);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Je hebt de uitnodiging geweigerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function acceptDinnerInviteAction($inviteId)
    {
        $invite = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerInvite')->find($inviteId);

        if ($invite == null) {
            $this->get('session')->getFlashBag()->add('error','Uitnodiging niet gevonden');
            return $this->redirect($this->generateUrl('design311website_dinners'));
        }
        
        if ($this->getUser()->getEmail() == $invite->getEmail()) {

            $participant = new DinnerParticipants();
            $participant->setUser($this->getUser());
            $participant->setDinner($invite->getDinner());

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->remove($invite);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success','Je hebt de uitnodiging geaccepteerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $invite->getDinner()->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }
}
