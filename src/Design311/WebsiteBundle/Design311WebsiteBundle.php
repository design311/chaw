<?php

namespace Design311\WebsiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class Design311WebsiteBundle extends Bundle
{
    public function boot() {
        $router = $this->container->get('router');
        $event = $this->container->get('event_dispatcher');
        $em = $this->container->get('doctrine')->getManager();
        //listen presta_sitemap.populate event
        $event->addListener(
                SitemapPopulateEvent::ON_SITEMAP_POPULATE, function(SitemapPopulateEvent $event) use ($router, $em) {

            if (!$router) {
                $router = new \Symfony\Component\Routing\Router();
                new \Symfony\Component\Routing\RouteCollection();
            }

            $routes = $router->getRouteCollection()->all();
            foreach ($routes as $k => $route) {
                if (stristr($k, "design311website") && !stristr($k, "login_check") && !stristr($k, "edit") && !stristr($k, "add") && !stristr($k, "filter") && !stristr($k, "invite") && !stristr($k, "participant") && !stristr($k, "like") && !stristr($k, "save") && !stristr($k, "shop") && !stristr($k, "_ajax") && !stristr($k, "aantal_personen") && !stristr($k, "required") && !stristr($k, "profile_password") && !stristr($k, "logout") && $k != 'design311website_recepten_zoeken') {

                    if (count($route->compile()->getPathVariables()) == 0) {
                    	$url = $router->generate($k, array(), true);
                        $event->getGenerator()->addUrl(
                                new UrlConcrete(
                                $url, new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1
                                ), 'default'
                        );
                    }
                    elseif (stristr($k, "profile_view") || stristr($k, "recepten_user")) {
                        $users = $em->getRepository('Design311WebsiteBundle:User')->findAll();
                        foreach ($users as $user) {
                            $url = $router->generate($k, array('username' => $user->getUsername()), true);
                            $event->getGenerator()->addUrl(
                                    new UrlConcrete(
                                    $url, new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1
                                    ), 'default'
                            );
                        }
                    }
                    elseif (stristr($k, "dinners")) {
                        $dinners = $em->getRepository('Design311WebsiteBundle:Dinner')->findAll();
                        foreach ($dinners as $dinner) {
                            $url = $router->generate($k, array('permalink' => $dinner->getPermalink()), true);
                            $event->getGenerator()->addUrl(
                                    new UrlConcrete(
                                    $url, new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1
                                    ), 'default'
                            );
                        }
                    }
                    elseif (stristr($k, "recepten_category")) {
                        $categories = $em->getRepository('Design311WebsiteBundle:RecipeCategory')->findAll();
                        foreach ($categories as $category) {
                            $url = $router->generate($k, array('category' => strtolower($category->getPlural())), true);
                            $event->getGenerator()->addUrl(
                                    new UrlConcrete(
                                    $url, new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1
                                    ), 'default'
                            );
                        }
                    }
                    elseif (stristr($k, "recepten_detail")) {
                        $recipes = $em->getRepository('Design311WebsiteBundle:Recipe')->findAll();
                        foreach ($recipes as $recipe) {
                            $url = $router->generate($k, array('category' => strtolower($recipe->getCategory()->getPlural()), 'permalink' => $recipe->getPermalink()), true);
                            $event->getGenerator()->addUrl(
                                    new UrlConcrete(
                                    $url, new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1
                                    ), 'default'
                            );
                        }
                    }
                }
            }
        });
    }
}
