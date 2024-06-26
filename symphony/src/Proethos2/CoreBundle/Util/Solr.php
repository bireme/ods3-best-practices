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

    protected $solr_service = 'http://plugins-idx.bvsalud.org:8983/solr/best-practices/update';

    public function __construct() {}

    public function update($protocol='')
    {

        global $kernel;
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $trans_repository = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');

        $submission = $protocol->getMainSubmission();

        $data = array();
        $data['id'] = $protocol->getId();
        $data['title'] = $submission->getTitle();
        $data['coop_modality'] = $submission->getCoopModality();
        $data['introduction'] = $submission->getIntroduction();
        $data['objectives'] = $submission->getObjectives();
        $data['activities'] = $submission->getActivities();
        $data['main_results'] = $submission->getMainResults();
        $data['factors'] = $submission->getFactors();
        $data['outcome_information'] = $submission->getOutcomeInformation();
        $data['describe_how'] = $submission->getDescribeHow();
        $data['challenges_information'] = $submission->getChallengesInformation();
        $data['lessons_information'] = $submission->getLessonsInformation();
        $data['sustainability'] = $submission->getSustainability();
        $data['keywords'] = $submission->getDescriptorsList();
        $data['is_private'] = $protocol->getIsPrivate();
        $data['start_date'] = $submission->getStartDate()->format('Y-m-d H:i:s');
        if ( $submission->getEndDate() ) $data['end_date'] = $submission->getEndDate()->format('Y-m-d H:i:s');

        // type field
        $type = $submission->getType();
        if ( $type ) {
            $type->setTranslatableLocale('en');
            $em->refresh($type);
            // type translations
            $translations = $trans_repository->findTranslations($type);
            $texts = array();
            $texts['en'] = 'en^'.$type->getName();
            foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                if ( array_key_exists($_locale, $translations) ) {
                    $text = $translations[$_locale];
                    $_locale = substr($_locale, 0, 2);
                    $texts[$_locale] = $_locale.'^'.$text['name'];
                }
            }
            $data['type'] = implode('|', $texts);
        }

        // outcomes field
        $outcomes = $submission->getOutcomes();
        if ( $outcomes ) {
            $outcomes->setTranslatableLocale('en');
            $em->refresh($outcomes);
            // outcomes translations
            $translations = $trans_repository->findTranslations($outcomes);
            $texts = array();
            $texts['en'] = 'en^'.$outcomes->getName();
            foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                if ( array_key_exists($_locale, $translations) ) {
                    $text = $translations[$_locale];
                    $_locale = substr($_locale, 0, 2);
                    $texts[$_locale] = $_locale.'^'.$text['name'];
                }
            }
            $data['outcomes'] = implode('|', $texts);
        }

        // institution field
        if ( $submission->getOtherInstitution() ) {
            $data['institution'] = $submission->getOtherInstitution();
        } else {
            $institution = $submission->getInstitution();
            if ( $institution ) {
                $institution->setTranslatableLocale('en');
                $em->refresh($institution);

                // institution translations
                $translations = $trans_repository->findTranslations($institution);
                $texts = array();
                $texts['en'] = 'en^'.$institution->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['institution'] = implode('|', $texts);
            }
        }

        // entity field
        $entity = $submission->getEntity();
        if ( $entity ) {
            $data['entity'] = array();
            foreach ($entity as $e) {
                $e->setTranslatableLocale('en');
                $em->refresh($e);

                // entity translations
                $translations = $trans_repository->findTranslations($e);
                $texts = array();
                $texts['en'] = 'en^'.$e->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['entity'][] = implode('|', $texts);
            }
        }

        // country field
        $country = $submission->getCountry();
        if ( $country ) {
            $data['country'] = array();
            foreach ($country as $c) {
                $c->setTranslatableLocale('en');
                $em->refresh($c);

                // country translations
                $translations = $trans_repository->findTranslations($c);
                $texts = array();
                $texts['en'] = 'en^'.$c->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['country'][] = implode('|', $texts);
            }
        }

        // subregion field
        $subregion = $submission->getSubregion();
        if ( $subregion ) {
            $data['subregion'] = array();
            foreach ($subregion as $sub) {
                $sub->setTranslatableLocale('en');
                $em->refresh($sub);

                // subregion translations
                $translations = $trans_repository->findTranslations($sub);
                $texts = array();
                $texts['en'] = 'en^'.$sub->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['subregion'][] = implode('|', $texts);
            }
        }

        // stakeholder field
        $stakeholder = $submission->getStakeholder();
        if ( $stakeholder ) {
            $data['stakeholder'] = array();
            foreach ($stakeholder as $sh) {
                $sh->setTranslatableLocale('en');
                $em->refresh($sh);

                // stakeholder translations
                $translations = $trans_repository->findTranslations($sh);
                $texts = array();
                $texts['en'] = 'en^'.$sh->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['stakeholder'][] = implode('|', $texts);
            }
        }

        // population group field
        $population_group = $submission->getPopulationGroup();
        if ( $population_group ) {
            $data['population_group'] = array();
            foreach ($population_group as $pg) {
                $pg->setTranslatableLocale('en');
                $em->refresh($pg);

                // population group translations
                $translations = $trans_repository->findTranslations($pg);
                $texts = array();
                $texts['en'] = 'en^'.$pg->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['population_group'][] = implode('|', $texts);
            }
        }

        // intervention field
        $intervention = $submission->getIntervention();
        if ( $intervention ) {
            $data['intervention'] = array();
            foreach ($intervention as $i) {
                $i->setTranslatableLocale('en');
                $em->refresh($i);

                // intervention translations
                $translations = $trans_repository->findTranslations($i);
                $texts = array();
                $texts['en'] = 'en^'.$i->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['intervention'][] = implode('|', $texts);
            }
        }

        // technical matter field
        $technical_matter = $submission->getTechnicalMatter();
        if ( $technical_matter ) {
            $data['technical_matter'] = array();
            foreach ($technical_matter as $tm) {
                $tm->setTranslatableLocale('en');
                $em->refresh($tm);

                // technical matter translations
                $translations = $trans_repository->findTranslations($tm);
                $texts = array();
                $texts['en'] = 'en^'.$tm->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['technical_matter'][] = implode('|', $texts);
            }
        }

        // target field
        $target = $submission->getTarget();
        if ( $target ) {
            $data['target'] = array();
            foreach ($target as $t) {
                $t->setTranslatableLocale('en');
                $em->refresh($t);

                // target translations
                $translations = $trans_repository->findTranslations($t);
                $texts = array();
                $texts['en'] = 'en^'.$t->getName();
                foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                    if ( array_key_exists($_locale, $translations) ) {
                        $text = $translations[$_locale];
                        $_locale = substr($_locale, 0, 2);
                        $texts[$_locale] = $_locale.'^'.$text['name'];
                    }
                }
                $data['target'][] = implode('|', $texts);
            }
        }

        // call field
        $call = $submission->getCall();
        if ( $call ) {
            $call->setTranslatableLocale('en');
            $em->refresh($call);
            // call translations
            $translations = $trans_repository->findTranslations($call);
            $texts = array();
            $texts['en'] = 'en^'.$call->getName();
            foreach(array('pt_BR', 'es_ES', 'fr_FR') as $_locale) {
                if ( array_key_exists($_locale, $translations) ) {
                    $text = $translations[$_locale];
                    $_locale = substr($_locale, 0, 2);
                    $texts[$_locale] = $_locale.'^'.$text['name'];
                }
            }
            $data['call'] = implode('|', $texts);
        }

        $json = json_encode($data);

        $ch = curl_init($this->solr_service.'/json/docs?commit=true');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = json_decode(curl_exec($ch));
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return array($response, $responseCode);

    }

    public function delete($protocol='')
    {
        $query = ( $protocol ) ? 'id:'.$protocol->getId() : '*:*';
        $json = "{'delete': {'query': '".$query."'}}";
        $ch = curl_init($this->solr_service.'?commit=true');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = json_decode(curl_exec($ch));
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return array($response, $responseCode);

    }

}
