<?php

namespace AppBundle\Controller\APP;

use AppBundle\Entity\User;
use Symfony\Component\Intl\Intl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function indexAction()
    {
        return $this->render('APP/Dashboard/index.html.twig');
    }

    /**
     * Return flag by locale
     * @return array
     * */
    protected function getCountryFlags()
    {
        return [
            'es' => 'Spain',
            'es_ES' => 'Spain',
            'es_GT' => 'Guatemala',
            'en' => 'United-States',
            'en_US' => 'United-States',
            'en_GB' => 'United-Kingdom',
            'fr' => 'France',
            'de' => 'Denmark',
            'pt' => 'Portugal',
            'pt_PT' => 'Portugal',
            'pt_BR' => 'Brazil',
            'es_CR' => 'Costa-Rica',
            'pl_PL' => 'Poland',
            'no_NO' => 'Norway',
            'da_DK' => 'Denmark',
            'de_DE' => 'Germany',
            'cs_CZ' => 'Czech-Republic'
        ];
    }

    /**
     * Renders Inspinia/Sidebar/settings.html.twig
     * @param Request $request
     * @return Response
     * */
    public function localesAction($request)
    {
        $opts = [];

        $languages = [];
        $currentLocale = $request->getLocale();
        $locales = $this->getParameter('lexik_translation.managed_locales');

        $flags = $this->getCountryFlags();
        $defaultFlag = 'Unknown';

        foreach ($locales as $key => $locale) {
            $languages[$locale]['name'] = ucfirst(Intl::getLocaleBundle()->getLocaleName($locale, $currentLocale));
            $languages[$locale]['code'] = $locale;
            $languages[$locale]['country'] = isset($flags[$locale]) ? $flags[$locale]: $defaultFlag;
        }

        $opts['locales'] = $languages;
        $opts['route'] = $request->get('_route', 'grand_central');
        $opts['routeParams'] = $request->get('_route_params', []);

        return $this->render('Components/Inspinia/locales.html.twig', $opts);
    }

    /**
     * @Route("/session/dispatcher", name="grand_central")
     */
    public function grandCentralAction()
    {
        $stringRoute = 'app_homepage';
        $session = new Session();
        
        /** @var User $user */
        $user = $this->get('app.tools')->getCurrentUser();

        if ($user) {
            $session->set('_locale', $user->getLocale());
        }

        if ($this->get('security.authorization_checker')->isGranted(User::ROLE_USER)) {
            $stringRoute = 'fos_user_security_logout';
        }

        if ($this->get('security.authorization_checker')->isGranted(User::ROLE_APP)) {
            $stringRoute = 'app_homepage';
        }

        if ($this->get('security.authorization_checker')->isGranted(User::ROLE_ADMIN)) {
            $stringRoute = 'administrator_dashboard';
        }

        if ($this->get('security.authorization_checker')->isGranted(User::ROLE_SUPER_ADMIN)) {
            $stringRoute = 'super_administrator_dashboard';
        }

        $locale = $session->get('_locale');
        $route = $locale ? $this->generateUrl($stringRoute, ['_locale' => $locale]) : $this->generateUrl($stringRoute);
        return $this->redirect($route);
    }

    /**
     * Change language.
     * @param Request $request
     * @return Response
     * @Route("/session/language", name="change_language")
     */
    public function languageAction(Request $request)
    {
        $locale = $request->get('locale');
        $request->setLocale($locale);

        /** @var User $user */
        $user = $this->get('app.tools')->getCurrentUser();
        $user->setLocale($locale);

        $this->getDoctrine()->getManager()->flush();

        $route = $request->get('redirectTo', $this->generateUrl('grand_central'));
        return $this->redirect($route);
    }

    /**
     * App manifest
     * */
    public function manifestAction()
    {
        $response =  $this->render(':APP/Notifications:_manifest.json.twig');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Firebase messaging service worker
     * */
    public function firebaseMessagingSwAction()
    {
        $response = $this->render(':APP/Notifications:_firebase-messaging-sw.js.twig');
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }
}
