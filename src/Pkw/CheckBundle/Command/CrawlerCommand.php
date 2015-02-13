<?php
namespace Pkw\CheckBundle\Command;

use Doctrine\ORM\EntityManager;
use DOMElement;
use DOMNode;
use Pkw\CheckBundle\Entity\Committee;
use Pkw\CheckBundle\Entity\Community;
use Pkw\CheckBundle\Entity\Constituency;
use Pkw\CheckBundle\Entity\District;
use Pkw\CheckBundle\Entity\Province;
use Pkw\CheckBundle\Manager\WebpageManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Command
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CrawlerCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    /** @var OutputInterface $output */
    protected $output;

    /** @var array */
    protected $errors = [];

    /** @var array */
    protected $committeeIds = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('crawler:update')
            ->setDescription('Update database')
            ->addOption('truncate', 'tr', InputOption::VALUE_NONE, 'If set, all tables will be truncated before update');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->em = $this->getContainer()
            ->get('doctrine.orm.entity_manager');

        if ($input->getOption('truncate')) {
            $this->em->getConnection()
                ->query('
                    START TRANSACTION;
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE province;
                    TRUNCATE district;
                    TRUNCATE community;
                    TRUNCATE constituency;
                    TRUNCATE committee;
                    TRUNCATE pooling_station;
                    SET FOREIGN_KEY_CHECKS = 1;
                    COMMIT;
                ');
        }

        $mainUrl = $this->getContainer()->getParameter('main_url');
        $this->updateProvincesData($mainUrl);
        $this->displayErrors();
        if (!$input->getOption('truncate')) {
            $this->output->writeln('You didn\'t truncate database so primary key duplications are possible!');
        }
        $this->em->flush();
    }

    /**
     * Updates provinces' data
     *
     * @param string $url URL
     *
     * @return self
     */
    protected function updateProvincesData($url)
    {
        $webpageManager = new WebpageManager($url);

        $elements = $webpageManager->getElements('#content div.states tbody td.nazwa a');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = $this->getIdFromUrl($url);
            $electoratesNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);
            $pollingStationsNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);

            $this->updateProvinceData($webpageManager, $url, $id, $this->parseString($element->nodeValue),
                $this->parseInteger($electoratesNumber->nodeValue),
                $this->parseInteger($pollingStationsNumber->nodeValue));
        }

        return $this;
    }

    /**
     * Updates province data
     *
     * @param WebpageManager $webpageManager        webpage manager
     * @param string         $url                   URL
     * @param integer        $id                    ID
     * @param string         $name                  name
     * @param integer        $electoratesNumber     electorates number
     * @param integer        $pollingStationsNumber pollingStations number
     *
     * @return Province
     */
    protected function updateProvinceData(WebpageManager $webpageManager, $url, $id, $name, $electoratesNumber,
        $pollingStationsNumber)
    {
        $province = new Province();
        $province->setId($id)
            ->setName($name)
            ->setElectoratesNumber($electoratesNumber)
            ->setPollingStationsNumber($pollingStationsNumber);
        $this->em->persist($province);
        $this->output->writeln(sprintf('Province "%s" (ID %d) added', $province->getName(), $province->getId()));

        $this->updateDistrictsData($province, $webpageManager, $url);

        return $province;
    }

    /**
     * Updates districts' data
     *
     * @param Province       $province province
     * @param WebpageManager $parent   parent
     * @param string         $url      URL
     *
     * @return self
     */
    protected function updateDistrictsData(Province $province, WebpageManager $parent, $url)
    {
        $webpageManager = new WebpageManager($url, $parent);

        $districts = [];
        $elements = $webpageManager->getElements('#substates tr td.nazwa a');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = $this->getIdFromUrl($url);
            $electoratesNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);
            $pollingStationsNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);

            $district = $this->updateDistrictData($webpageManager, $province, $url, $id,
                $this->parseString($element->nodeValue), $this->parseInteger($electoratesNumber->nodeValue),
                $this->parseInteger($pollingStationsNumber->nodeValue));
            $districts[preg_replace('#^m\. #', '', $district->getName())] = $district;
        }

        $elements = $webpageManager->getElements('#committes tr td:nth-child(3) a');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = $this->getIdFromUrl($url, -2);
            $name = $this->getPreviousNode($element->parentNode, 1, XML_ELEMENT_NODE);

            $this->updateCommitteeData($webpageManager, $url, $id, $this->parseString($name->nodeValue));
        }

        $elements = $webpageManager->getElements('#wards tr td:first-child a');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = $this->getIdFromUrl($url);
            $borders = explode(', ', $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE)->nodeValue);
            foreach ($borders as $i => $border) {
                $borders[$i] = preg_replace('#^(powiaty?|miast[ao]): #i', '', $border);
            }
            $candidatesNumber = $this->getNextNode($element->parentNode, 3, XML_ELEMENT_NODE);
            $mandatesNumber = $this->getNextNode($element->parentNode, 4, XML_ELEMENT_NODE);

            $this->updateConstituencyData($webpageManager, $districts, $borders, $url, $id,
                $this->parseInteger($element->nodeValue), $this->parseInteger($candidatesNumber->nodeValue),
                $this->parseInteger($mandatesNumber->nodeValue));
        }

        return $this;
    }

    /**
     * Updates district data
     *
     * @param WebpageManager $webpageManager        webpage manager
     * @param Province       $province              province
     * @param string         $url                   URL
     * @param integer        $id                    ID
     * @param string         $name                  name
     * @param integer        $electoratesNumber     electorates number
     * @param integer        $pollingStationsNumber polling stations number
     *
     * @return District
     */
    protected function updateDistrictData(WebpageManager $webpageManager, Province $province, $url, $id, $name,
        $electoratesNumber, $pollingStationsNumber)
    {
        $district = new District();
        $district->setId($id)
            ->setName($name)
            ->setProvince($province)
            ->setElectoratesNumber($electoratesNumber)
            ->setPollingStationsNumber($pollingStationsNumber);
        $this->em->persist($district);
        $this->output->writeln(sprintf('    District "%s" (ID %d) added', $district->getName(), $district->getId()));

        $this->updateCommunitiesData($district, $webpageManager, $url);

        return $district;
    }

    /**
     * Updates committee data
     *
     * @param WebpageManager $webpageManager webpage manager
     * @param string         $url            URL
     * @param integer        $id             ID
     * @param string         $name           name
     *
     * @return Committee|null
     */
    protected function updateCommitteeData(WebpageManager $webpageManager, $url, $id, $name)
    {
        if (!in_array($id, $this->committeeIds)) {
            $committee = new Committee();
            $committee->setId($id)
                ->setName($name);
            $this->em->persist($committee);
            $this->output->writeln(sprintf('    Committee "%s" (ID %d) added', $committee->getName(),
                $committee->getId()));
            $this->committeeIds[] = $id;

            return $committee;
        }

        return null;
    }

    /**
     * Updates constituency data
     *
     * @param WebpageManager $webpageManager   webpage manager
     * @param array          $districts        districts
     * @param array          $borders          borders
     * @param string         $url              URL
     * @param integer        $id               ID
     * @param integer        $number           number
     * @param integer        $candidatesNumber candidates number
     * @param integer        $mandatesNumber   mandates number
     *
     * @return Constituency
     */
    protected function updateConstituencyData(WebpageManager $webpageManager, array $districts, array $borders, $url,
        $id, $number, $candidatesNumber, $mandatesNumber)
    {
        $constituency = new Constituency();
        $constituency->setId($id)
            ->setNumber($number)
            ->setCandidatesNumber($candidatesNumber)
            ->setMandatesNumber($mandatesNumber);
        $this->em->persist($constituency);
        $this->output->writeln(sprintf('    Constituency no %d (ID %d) added', $constituency->getNumber(),
            $constituency->getId()));

        foreach ($districts as $district) {
            /** @var District $district */
            if (in_array($district->getName(), $borders)) {
                $district->setConstituency($constituency);
                $this->em->persist($district);
            }
        }

        return $constituency;
    }

    /**
     * Updates communities' data
     *
     * @param District       $district district
     * @param WebpageManager $parent   parent
     * @param string         $url      URL
     *
     * @return self
     */
    protected function updateCommunitiesData(District $district, WebpageManager $parent, $url)
    {
        try {
            $webpageManager = new WebpageManager($url, $parent);
        } catch (ResourceNotFoundException $e) {
            $this->output->writeln($e->getMessage(), OutputInterface::OUTPUT_RAW);
            $this->addError('districts', sprintf('%s (%s)', $district->getName(), $url));
            return $this;
        }

        if ($webpageManager->getRedirectCount() == 1 || $this->getIdFromUrl($webpageManager->getHost()) > 9999) {
            $url = $webpageManager->getFileName();
            $id = $this->getIdFromUrl($url);
            $elements = $webpageManager->getElements('#wrap div.navi span:last-child');
            $element = $this->getNextNode($elements->getNode(0), 1, XML_TEXT_NODE);

            $this->updateCommunityData($webpageManager, $district, $url, $id, $this->parseString($element->nodeValue),
                Community::TYPE_CITY);
        } else {
            $elements = $webpageManager->getElements('#area area');
            $ids = array();

            /** @var DOMElement $element */
            foreach ($elements as $element) {
                $url = $element->getAttribute('href');
                $id = $this->getIdFromUrl($url);
                $title = $element->getAttribute('title');

                if (!in_array($id, $ids)) {
                    $this->updateCommunityData($webpageManager, $district, $url, $id, $title);
                    $ids[] = $id;
                }
            }
        }

        return $this;
    }

    /**
     * Updates community data
     *
     * @param WebpageManager $webpageManager webpage manager
     * @param District       $district       district
     * @param string         $url            URL
     * @param integer        $id             ID
     * @param string         $name           name
     * @param integer|null   $type           type
     *
     * @return Community
     */
    protected function updateCommunityData(WebpageManager $webpageManager, District $district, $url, $id, $name,
        $type = null)
    {
        if (empty($type)) {
            $type = preg_match('#, m.$#', $name) ? Community::TYPE_CITY : Community::TYPE_COMMUNITY;
        }

        $community = new Community();
        $community->setId($id)
            ->setName(preg_replace('#, [a-z]+\.?$#', '', $name))
            ->setType($type)
            ->setDistrict($district);
        $this->em->persist($community);
        $this->output->writeln(sprintf('        Community "%s" (ID %d%s) added', $community->getName(),
            $community->getId(), $community->getType() == Community::TYPE_CITY ? ', city' : ''));

        return $community;
    }

    /**
     * Add error
     *
     * @param string $type  type
     * @param string $value value
     */
    protected function addError($type, $value)
    {
        if (!array_key_exists($type, $this->errors)) {
            $this->errors[$type] = [];
        }
        $this->errors[$type][] = $value;
    }

    /**
     * Display errors
     */
    protected function displayErrors()
    {
        $this->output->writeln("\n" . 'Errors which appeared during data crawling:');
        foreach ($this->errors as $type => $values) {
            $this->output->writeln('- ' . $type . ': ' . implode(', ', $values) . '.');
        }
    }

    /**
     * Get previous node
     *
     * @param DOMNode      $node node
     * @param integer      $no   no
     * @param integer|null $type type
     *
     * @return DOMNode|null
     */
    protected function getPreviousNode(DOMNode $node, $no = 1, $type = null)
    {
        do {
            $node = $node->previousSibling;
            if (!isset($type) || $type == $node->nodeType) {
                $no--;
            }
        } while (isset($node) && $no > 0);

        return $node;
    }

    /**
     * Get next node
     *
     * @param DOMNode      $node node
     * @param integer      $no   no
     * @param integer|null $type type
     *
     * @return DOMNode|null
     */
    protected function getNextNode(DOMNode $node, $no = 1, $type = null)
    {
        do {
            $node = $node->nextSibling;
            if (!isset($type) || $type == $node->nodeType) {
                $no--;
            }
        } while (isset($node) && $no > 0);

        return $node;
    }

    /**
     * Parse integer
     *
     * @param mixed $value value
     *
     * @return integer
     */
    protected function parseInteger($value)
    {
        $value = (integer) preg_replace('#[^0-9]#', '', $value);

        return $value;
    }

    /**
     * Parse string
     *
     * @param mixed $value value
     *
     * @return string
     */
    protected function parseString($value)
    {
        $value = trim(strip_tags($value));

        return $value;
    }

    /**
     * Get ID from URL
     *
     * @param string  $url  URL
     * @param integer $idNo ID no (positive - from the beginning, negative - from the end)
     *
     * @return integer
     */
    protected function getIdFromUrl($url, $idNo = -1)
    {
        $parts = explode('/', $url);
        $id = 0;
        for ($i = abs($idNo); $i > 0; $i--) {
            $id = $idNo > 0 ? array_shift($parts) : array_pop($parts);
        }
        $id = $this->parseInteger($id);

        return $id;
    }
}
