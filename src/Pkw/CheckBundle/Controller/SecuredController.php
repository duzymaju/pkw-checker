<?php

namespace Pkw\CheckBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 */
class SecuredController extends Controller
{
    /**
     * @Route("/login", name="admin_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="admin_login_check")
     */
    public function loginCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/hello", name="admin_hello")
     * @Security("is_granted('ROLE_USER')")
     * @Template()
     */
    public function helloAction()
    {
        return array(
            'user' => $this->getUser(),
        );
    }

    /**
     * @Route("/admin/hello", name="admin_hello_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function helloadminAction()
    {
        return array(
            'user' => $this->getUser(),
        );
    }

    /**
     * @Route("/", name="admin")
     */
    public function homepageAction()
    {
        $router = $this->get('router');
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted('ROLE_ADMIN')) {
            $url = $router->generate('admin_hello_admin');
        } elseif ($securityContext->isGranted('ROLE_USER')) {
            $url = $router->generate('admin_login');
        } else {
            $url = $router->generate('homepage');
        }

        return $this->redirect($url);
    }
}
