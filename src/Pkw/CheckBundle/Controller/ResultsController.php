<?php

namespace Pkw\CheckBundle\Controller;

use Pkw\CheckBundle\Entity\Province;
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

        $committeesNumber = $this->get('pkw_check.repository.committee')
            ->count();
        $constituenciesNumber = $this->get('pkw_check.repository.constituency')
            ->count();
        $pollingStationsNumber = $this->get('pkw_check.repository.polling_station')
            ->count();

        $provinces = $provincesRepository->findAll();

        // temporary solution:
        foreach ($provinces as $province) {
            /** @var Province $province */
            $pollingStationsNumber += $province->getPollingStationsNumber();
        }

        return $this->render('PkwCheckBundle:Results:homepage.html.twig', array(
            'committeesNumber' => $committeesNumber,
            'communitiesNumber' => $communitiesNumber,
            'constituenciesNumber' => $constituenciesNumber,
            'districtsNumber' => $districtsNumber,
            'pollingStationsNumber' => $pollingStationsNumber,
            'provinces' => $provinces,
            'provincesNumber' => $provincesNumber,
        ));
    }
}
