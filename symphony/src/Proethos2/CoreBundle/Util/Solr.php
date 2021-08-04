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
 

namespace Proethos2\CoreBundle\Util;


class Solr {

    public function update($protocol)
    {

        global $kernel;
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $trans_repository = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');
        
        $submission = $protocol->getMainSubmission();

        $data = array();
        $data['id'] = $protocol->getId();
        $data['title'] = $submission->getTitle();
        $data['introduction'] = $submission->getIntroduction();
        $data['objectives'] = $submission->getObjectives();
        $data['activities'] = $submission->getActivities();
        $data['main_results'] = $submission->getMainResults();
        $data['factors'] = $submission->getFactors();
        $data['outcome_information'] = $submission->getOutcomeInformation();
        $data['describe_how'] = $submission->getDescribeHow();
        $data['challenges_information'] = $submission->getChallengesInformation();
        $data['lessons_information'] = $submission->getLessonsInformation();
        $data['start_date'] = $submission->getStartDate()->format('Y-m-d H:i:s');
        $data['end_date'] = $submission->getEndDate()->format('Y-m-d H:i:s');

        // country field
        $country = $submission->getCountry();
        $country->setTranslatableLocale('en');
        $em->refresh($country);

        // country translations
        $translations = $trans_repository->findTranslations($country);
        $texts = array();
        $texts['en'] = 'en^'.$country->getName();
        foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
            $text = $translations[$_locale];
            if ( $text ) {
                $_locale = substr($_locale, 0, 2);
                $texts[$_locale] = $_locale.'^'.$text['name'];
            }
        }
        $data['country'] = implode('|', $texts);

        // subregion field
        $subregion = $submission->getSubRegion();
        $subregion->setTranslatableLocale('en');
        $em->refresh($subregion);

        // subregion translations
        $translations = $trans_repository->findTranslations($subregion);
        $texts = array();
        $texts['en'] = 'en^'.$subregion->getName();
        foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
            $text = $translations[$_locale];
            if ( $text ) {
                $_locale = substr($_locale, 0, 2);
                $texts[$_locale] = $_locale.'^'.$text['name'];
            }
        }
        $data['subregion'] = implode('|', $texts);

        // target field
        $target = $submission->getTarget();
        $data['target'] = array();

        foreach ($target as $t) {
            $t->setTranslatableLocale('en');
            $em->refresh($t);

            // target translations
            $translations = $trans_repository->findTranslations($t);
            $texts = array();
            $texts['en'] = 'en^'.$t->getName();
            foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                $text = $translations[$_locale];
                if ( $text ) {
                    $_locale = substr($_locale, 0, 2);
                    $texts[$_locale] = $_locale.'^'.$text['name'];
                }
            }
            $data['target'][] = implode('|', $texts);
        }

        $json = json_encode($data);

        $ch = curl_init('http://plugins-idx.bvsalud.org:8983/solr/best-practices/update/json/docs?commit=true');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $output = json_decode(curl_exec($ch));
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $responseCode;

    }

}