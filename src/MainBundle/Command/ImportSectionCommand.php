<?php

/*
 * This file is part of the ESN package.
 *
 * (c) ESNFranceG33kTeam <https://github.com/ESNFranceG33kTeam/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Esn\EsnBundle\Entity\Section;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Storage section object
 *
 * @author Jérémie Samson <jeremie.samson@ix.esnlille.fr>
 */
class ImportSectionCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('esn:section:import')
            ->setDescription('Import all sections from XML')
            ->setHelp(<<<EOT
The <info>'esn:section:import</info> command import all sections from the last sections.xml file from the satellite website
EOT
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $sectionsFile = __DIR__ . "/Data/sections.xml";

        if (!file_exists($sectionsFile)){
            $output->writeln('<error>The file sections.xml does not exist, find it in the latest release (http://satellite.esn.org)</error>');
            exit;
        }

        $output->writeln("Begin import");

        $doc = new \DOMDocument();

        $output->writeln('<comment>Loading sections from file ...</comment>');

        if ($doc->load($sectionsFile)){
            /** @var \DOMNodeList $sections_xml */
            $sections_xml = $doc->getElementsByTagName('section');
            $size = $sections_xml->length;

            $output->writeln('<comment>' .$size. '</comment>');

            /** @var \DOMNodeList $section_xml */
            foreach($sections_xml as $section_xml){

                //Section
                $code = $section_xml->getElementsByTagName('sc')->item(0)->nodeValue;

                /** @var Section $section_db */
                $section_db = $em->getRepository('EsnEsnBundle:Section')->findOneBy(array("code" => $code));

                $section = ($section_db) ? $section_db : new Section();

                $section->setCode($code);

                //Name
                $name = $section_xml->getElementsByTagName('sectionname')->item(0)->nodeValue;
                $section->setName($name);

                //Street
                $street = $section_xml->getElementsByTagName('street')->item(0)->nodeValue;
                $section->setStreet($street);

                //City
                $city = $section_xml->getElementsByTagName('l')->item(0)->nodeValue;
                $section->setCity($city);

                //Country
                $country = $section_xml->getElementsByTagName('c')->item(0)->nodeValue;
                $section->setCountry($country);

                //Website
                $website = $section_xml->getElementsByTagName('website')->item(0)->nodeValue;
                $section->setWebsite($website);

                //Facebook
                $facebook = $section_xml->getElementsByTagName('facebook')->item(0)->nodeValue;
                $section->setFacebook($facebook);

                //Longitude
                $longitude = $section_xml->getElementsByTagName('longitude')->item(0)->nodeValue;
                $section->setLongitude($longitude);

                //Latitude
                $latitude = $section_xml->getElementsByTagName('latitude')->item(0)->nodeValue;
                $section->setLatitude($latitude);

                //Joindate
                $section->setJoindate(new \DateTime());

                if (!$section_db){
                    $em->persist($section);
                }

                $em->flush();
            }
        }

        $output->writeln("Import finished");
    }

}