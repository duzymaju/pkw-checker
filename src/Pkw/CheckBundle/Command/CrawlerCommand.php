<?php
namespace Pkw\CheckBundle\Command;

use Doctrine\ORM\EntityManager;
use DOMElement;
use DOMNode;
use Pkw\CheckBundle\Entity\Community;
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
    /** @const string */
    const MAIN_URL = 'http://wybory2014.pkw.gov.pl/pl/';

    /** @var EntityManager */
    protected $em;

    /** @var OutputInterface $output */
    protected $output;

    /** @var array */
    protected $errors = [];

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
                    TRUNCATE pooling_station;
                    SET FOREIGN_KEY_CHECKS = 1;
                    COMMIT;
                ');
        }

        $this->updateProvincesData(self::MAIN_URL);
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
     * @return self
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
        $this->output->writeln(sprintf('Province "%s" (ID %d) added', $province->getName(), $id));

        $this->updateDistrictsData($province, $webpageManager, $url);

        return $this;
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
        $elements = $webpageManager->getElements('#substates tbody td.nazwa a');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = $this->getIdFromUrl($url);
            $electoratesNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);
            $pollingStationsNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);

            $this->updateDistrictData($webpageManager, $province, $url, $id, $this->parseString($element->nodeValue),
                $this->parseInteger($electoratesNumber->nodeValue),
                $this->parseInteger($pollingStationsNumber->nodeValue));
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
     * @param integer        $pollingStationsNumber pollingStations number
     *
     * @return self
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
        $this->output->writeln(sprintf('    District "%s" (ID %d) added', $district->getName(), $id));

        $this->updateCommunitiesData($district, $webpageManager, $url);

        return $this;
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
     * @return self
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
        $this->output->writeln(sprintf('        Community "%s" (ID %d%s) added', $community->getName(), $id,
            $community->getType() == Community::TYPE_CITY ? ', city' : ''));

        //$this->updateConstituenciesData($community, $webpageManager, $url);

        return $this;
    }

    /**r
     * Updates constituencies' data
     *
     * @param Community      $community community
     * @param WebpageManager $parent    parent
     * @param string         $url       URL
     *
     * @return self
     */
//    protected function updateConstituenciesData(Community $community, WebpageManager $parent, $url)
//    {
//
//    }

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
     * @param string $url URL
     *
     * @return integer
     */
    protected function getIdFromUrl($url)
    {
        $parts = explode('/', $url);
        $id = $this->parseInteger(array_pop($parts));

        return $id;
    }
}
