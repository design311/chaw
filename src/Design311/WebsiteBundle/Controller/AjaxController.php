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

    public function removeFromShopAction($permalink)
    {

        $recipe = $this->getDoctrine()->getRepository('Design311WebsiteBundle:Recipe')->findOneByPermalink($permalink);
        
        $recipe->removeShoppinglistFrom($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return $this->redirect($this->generateUrl('design311website_recepten_shoppinglist'));
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
            $user = $participantRequest->getUser();

            $em = $this->getDoctrine()->getManager();
            $em->remove($participantRequest);
            $em->flush();

            $mail = \Swift_Message::newInstance()
                ->setSubject($dinner->getUser()->getDisplayName() . ' heeft je verzoek geweigerd')
                ->setFrom('info@chaw.be')
                ->setReplyTo('info@chaw.be')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Design311WebsiteBundle:Mail:layout.html.twig',
                        array(
                            'message' => $dinner->getUser()->getDisplayName() . ' heeft je deelnameverzoek geweigerd, dit kan voor heel veel verschillende redenen zijn, je hoeft je zeker niet aangevallen voelen. Kijk snel naar de andere etentjes.',
                            )
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($mail);

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

            $mail = \Swift_Message::newInstance()
                ->setSubject($dinner->getUser()->getDisplayName() . ' heeft je verzoek geaccepteerd')
                ->setFrom($dinner->getUser()->getEmail())
                ->setReplyTo($dinner->getUser()->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Design311WebsiteBundle:Mail:layout.html.twig',
                        array(
                            'message' => $dinner->getUser()->getDisplayName() . ' heeft je deelnameverzoek geaccepteerd, als je vragen hebt kan je contact opnemen met hem/haar door deze mail te beantwoorden.',
                            )
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($mail);

            $this->get('session')->getFlashBag()->add('success','Je hebt het verzoek geaccepteerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $participant->getDinner()->getPermalink()) ));
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
        
        if ($this->getUser()->getEmail() == $invite->getEmail()) {

            $dinner = $invite->getDinner(); //needed for redirect

            $dinnerUser = $invite->getDinner()->getUser();

            $em = $this->getDoctrine()->getManager();
            $em->remove($invite);
            $em->flush();

            $mail = \Swift_Message::newInstance()
                ->setSubject($this->getUser()->getDisplayName() . ' heeft je uitnodiging geweigerd')
                ->setFrom($this->getUser()->getEmail())
                ->setReplyTo($this->getUser()->getEmail())
                ->setTo($dinnerUser->getEmail())
                ->setBody(
                    $this->renderView(
                        'Design311WebsiteBundle:Mail:layout.html.twig',
                        array(
                            'message' => $dinner->getUser()->getDisplayName() . ' zal niet aanwezig kunnen zijn op je dinner, nodig snel iemand anders uit.',
                            )
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($mail);

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

            $mail = \Swift_Message::newInstance()
                ->setSubject($this->getUser()->getDisplayName() . ' heeft je uitnodiging geaccepteerd')
                ->setFrom($this->getUser()->getEmail())
                ->setReplyTo($this->getUser()->getEmail())
                ->setTo($participant->getDinner()->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'Design311WebsiteBundle:Mail:layout.html.twig',
                        array(
                            'message' => $dinner->getUser()->getDisplayName() . ' zal aanwezig zijn op je dinner.',
                            )
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($mail);

            $this->get('session')->getFlashBag()->add('success','Je hebt de uitnodiging geaccepteerd');

            return $this->redirect($this->generateUrl('design311website_dinners_detail', array('permalink' => $participant->getDinner()->getPermalink()) ));
        }
        else{
            throw new AccessDeniedException('Je hebt geen toegang tot deze pagina');
        }
    }

    public function usernameExistsAction(Request $request){
        $user = $this->getDoctrine()->getRepository('Design311WebsiteBundle:User')->findOneByUsername($request->get('username'));

        if ($user == null) {
            return new JsonResponse(false);
        }
        else{
            return new JsonResponse(true);
        }
    }
}
