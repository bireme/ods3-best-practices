<?php

// This file is part of the ProEthos Software.
//
// Copyright 2013, PAHO. All rights reserved. You can redistribute it and/or modify
// ProEthos under the terms of the ProEthos License as published by PAHO, which
// restricts commercial use of the Software.
//
// ProEthos is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details.
//
// You should have received a copy of the ProEthos License along with the ProEthos
// Software. If not, see
// https://github.com/bireme/proethos2/blob/master/LICENSE.txt


namespace Proethos2\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Proethos2\CoreBundle\Util\Util;
use Proethos2\CoreBundle\Util\Solr;


class UpdateSolrIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('proethos2:update-solr-index')
            ->setDescription('Update Solr index')
            ->setHelp('Usage: php app/console proethos2:update-solr-index [protocol]')
            ->addArgument('protocol', InputArgument::OPTIONAL, 'Insert the protocol ID.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $translator = $container->get('translator');

        $em = $doctrine->getManager();
        $util = new Util($container, $doctrine);
        $protocol_repository = $em->getRepository('Proethos2ModelBundle:Protocol');

        $protocol_id = $input->getArgument('protocol');
        if ( $protocol_id ) {
            $protocols = $protocol_repository->findBy(array('id' => $protocol_id, 'status' => 'A'));
        } else {
            $protocols = $protocol_repository->findBy(array('status' => 'A'));
        }

        $solr = new Solr();
        list($response, $responseCode) = $solr->delete();

        if ($responseCode == 200) {
            if ( $protocols ) {
                $output->writeln("[INFO] Starting Solr index update:");
                $output->writeln("----------------------------------");

                foreach($protocols as $protocol) {
                    $solr = new Solr();
                    list($response, $responseCode) = $solr->update($protocol);

                    if ($responseCode == 200) {
                        $output->writeln(sprintf("Protocol %d - Solr query time: %dms", $protocol->getId(), $response->responseHeader->QTime));
                    } else {
                        $output->writeln(sprintf("Protocol %d - Solr error: %s", $protocol->getId(), $response->error->msg));
                    }
                }

                $output->writeln("----------------------------------");
                $output->writeln("[INFO] Solr index update finished!");
            } else {
                $output->writeln("[ERROR] Solr index update failed.");
            }
        } else {
            $output->writeln("[ERROR] Solr index update failed.");
        }
    }
}
