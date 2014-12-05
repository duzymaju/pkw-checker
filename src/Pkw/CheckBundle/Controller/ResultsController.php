<?php

namespace Pkw\CheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Results controller
 */
class ResultsController extends Controller
{
    /**
     * Main page
     *
     * @return Response
     */
    public function homepageAction()
    {
        $provincesRepository = $this->get('pkw_check.repository.province');

        $communitiesNumber = $this->get('pkw_check.repository.community')
            ->count();
        $districtsNumber = $this->get('pkw_check.repository.district')
            ->count();
        $provincesNumber = $provincesRepository->count();

        $provinces = $provincesRepository->findAll();

        return $this->render('PkwCheckBundle:Results:homepage.html.twig', array(
            'communitiesNumber' => $communitiesNumber,
            'districtsNumber' => $districtsNumber,
            'provinces' => $provinces,
            'provincesNumber' => $provincesNumber,
        ));
    }
}
