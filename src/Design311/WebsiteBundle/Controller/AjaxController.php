<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Design311\WebsiteBundle\Form\Type\RecipeType;
use Design311\WebsiteBundle\Entity\Recipe;
use Design311\WebsiteBundle\Entity\DinnerParticipants;


class AjaxController extends Controller
{
    public function likeRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

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

    public function saveRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

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

    public function shopRecipeAction($recipeId)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->find($recipeId);

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

        //TODO DOUBLE DELETE ERROR
        $participantRequest = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerParticipantRequest')->find($participantId);
        
        if ($this->getUser() == $participantRequest->getDinner()->getUser()) {

            $dinner = $participantRequest->getDinner(); //needed for redirect

            $em = $this->getDoctrine()->getManager();
            $em->remove($participantRequest);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $dinner->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function acceptDinnerParticipantAction($participantId)
    {
        $participantRequest = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerParticipantRequest')->find($participantId);
        
        if ($this->getUser() == $participantRequest->getDinner()->getUser()) {

            $participant = new DinnerParticipants();
            $participant->setUser($participantRequest->getUser());
            $participant->setDinner($participantRequest->getDinner());

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->remove($participantRequest);
            $em->flush();

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $participantRequest->getDinner()->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function declineDinnerInviteAction($inviteId)
    {
        //TODO DOUBLE DELETE ERROR
        $invite = $this->getDoctrine()->getRepository('Design311WebsiteBundle:DinnerInvite')->find($inviteId);
        
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

    public function filterDinnersAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $qb
            ->from('Design311WebsiteBundle:Dinner', 'd')
            ->select('d')
            ->where($qb->expr()->lte('d.price', $request->get('maxprice')))
            ->leftJoin('Design311WebsiteBundle:Address', 'a', 'WITH', 'a.id = d.address');

        $query = $qb->getQuery();
        $dinners = $query->execute();

        //ladybug_dump($dinners);

        $serializer = $this->container->get('serializer');
        $dinners = $serializer->serialize($dinners, 'json');
        return new Response($dinners);

    }
}
