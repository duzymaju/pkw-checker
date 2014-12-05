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

/**
 * Command
 */
class CrawlerCommand extends ContainerAwareCommand
{
    /** @const string */
    const MAIN_URL = 'http://wybory2010.pkw.gov.pl/geo/pl/000000.html';

    /** @var EntityManager */
    protected $em;

    /** @var OutputInterface $output */
    protected $output;

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
        $elements = $webpageManager->getElements('#tabs-3 table.geo td.details a.tablist');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = (integer) substr($url, 0, 6);
            $residentsNumber = $this->getNextNode($element->parentNode, 1, XML_ELEMENT_NODE);
            $electoratesNumber = $this->getNextNode($residentsNumber, 1, XML_ELEMENT_NODE);
            $districtsNumber = $this->getNextNode($electoratesNumber, 3, XML_ELEMENT_NODE);
            $communitiesNumber = $this->getNextNode($districtsNumber, 1, XML_ELEMENT_NODE);

            $province = new Province();
            $province->setId($id)
                ->setName(trim(strip_tags($element->nodeValue), ' *'))
                ->setResidentsNumber($this->parseInteger($residentsNumber->nodeValue))
                ->setElectoratesNumber($this->parseInteger($electoratesNumber->nodeValue))
                ->setDistrictsNumber($this->parseInteger($districtsNumber->nodeValue))
                ->setCommunitiesNumber($this->parseInteger($communitiesNumber->nodeValue));
            $this->em->persist($province);
            $this->output->writeln(sprintf('Province "%s" added', $province->getName()));

            $this->updateDistrictsData($province, $webpageManager, $url);
        }

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
        $elements = $webpageManager->getElements('#tabs-3 table.geo td.details a.tablist');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = (integer) substr($url, 0, 6);

            $district = new District();
            $district->setId($id)
                ->setName(trim(strip_tags($element->nodeValue), ' *'))
                ->setProvince($province);
            $this->em->persist($district);
            $this->output->writeln(sprintf('    District "%s" added', $district->getName()));

            $this->updateCommunitiesData($district, $webpageManager, $url);
        }

        return $this;
    }

    /**
     * Updates districts' data
     *
     * @param District       $district district
     * @param WebpageManager $parent   parent
     * @param string         $url      URL
     *
     * @return self
     */
    protected function updateCommunitiesData(District $district, WebpageManager $parent, $url)
    {
        $webpageManager = new WebpageManager($url, $parent);
        $elements = $webpageManager->getElements('#tabs-1 table.geo td.details a.tablist');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $url = $element->getAttribute('href');
            $id = (integer) substr($url, 0, 6);

            $community = new Community();
            $community->setId($id)
                ->setName(trim(strip_tags($element->nodeValue), ' *'))
                ->setDistrict($district);
            $this->em->persist($community);
            $this->output->writeln(sprintf('        Community "%s" added', $community->getName()));

            //$this->updateCommunitiesData($district, $webpageManager, $url);
        }

        return $this;
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
}
