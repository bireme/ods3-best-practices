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


namespace Proethos2\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Intl\Intl;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Proethos2\CoreBundle\Util\Util;
use Proethos2\CoreBundle\Util\CountryLocale;

use Proethos2\ModelBundle\Entity\Submission;
use Proethos2\ModelBundle\Entity\SubmissionCountry;
use Proethos2\ModelBundle\Entity\SubmissionCost;
use Proethos2\ModelBundle\Entity\SubmissionTask;
use Proethos2\ModelBundle\Entity\SubmissionUpload;
use Proethos2\ModelBundle\Entity\Protocol;
use Proethos2\ModelBundle\Entity\ProtocolHistory;
use Proethos2\ModelBundle\Entity\SubmissionClinicalTrial;
use Proethos2\ModelBundle\Entity\SubmissionClinicalStudy;
use Proethos2\ModelBundle\Entity\SubmissionTechnicalAttribute;


class NewSubmissionController extends Controller
{
    /**
     * @Route("/submission/new/first", name="submission_new_first_step")
     * @Template()
     */
    public function FirstStepAction(Request $request)
    {
        $output = array();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getSession()->get('_locale');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $util = new Util($this->container, $this->getDoctrine());

        // getting good practice type list
        $best_practice_type_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeType');
        $best_practice_type = $best_practice_type_repository->findByStatus(true);
        // $best_practice_type = $best_practice_type_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_type'] = $best_practice_type;

        // getting good practice role list
        $best_practice_role_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeRole');
        $best_practice_role = $best_practice_role_repository->findByStatus(true);
        // $best_practice_role = $best_practice_role_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_role'] = $best_practice_role;

        // getting good practice entity list
        $best_practice_entity_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeEntity');
        $best_practice_entity = $best_practice_entity_repository->findByStatus(true);
        // $best_practice_entity = $best_practice_entity_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_entity'] = $best_practice_entity;

        // getting calls for good practices
        $best_practice_call_repository = $em->getRepository('Proethos2ModelBundle:Call');
        $best_practice_call = $best_practice_call_repository->findByStatus(true);
        // $best_practice_call = $best_practice_call_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_call'] = $best_practice_call;

        // getting institution list
        $institution_repository = $em->getRepository('Proethos2ModelBundle:Institution');
        $institution = $institution_repository->findByStatus(true);
        // $institution = $institution_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['institution'] = $institution;

        // getting stakeholder list
        $stakeholder_repository = $em->getRepository('Proethos2ModelBundle:Stakeholder');
        $stakeholder = $stakeholder_repository->findByStatus(true);
        // $stakeholder = $stakeholder_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['stakeholder'] = $stakeholder;

        // modality options
        $coop_modality = array(
            "A" => $translator->trans("North-North"),
            "B" => $translator->trans("North-South"),
            "C" => $translator->trans("South-South"),
            "D" => $translator->trans("Triangular Cooperation"),
        );
        $output['coop_modality'] = $coop_modality;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-first-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // checking required files
            foreach(array('title', 'proposal', 'best_practice_type', 'best_practice_role', 'institution', 'institution_name', 'stakeholder') as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $post_data;
                }
            }

            $protocol = new Protocol();
            $protocol->setOwner($user);
            $protocol->setStatus('D');
            $em->persist($protocol);
            $em->flush();

            $submission = new Submission();
            $submission->setTitle($post_data['title']);
            $submission->setProposal($post_data['proposal']);
            $submission->setOtherProposal($post_data['other_proposal']);
            $submission->setCoopModality($post_data['coop_modality']);
            $submission->setOtherRole($post_data['other_best_practice_role']);
            $submission->setOtherInstitution($post_data['other_institution']);
            $submission->setInstitutionName($post_data['institution_name']);
            $submission->setOtherStakeholder($post_data['other_stakeholder']);
            $submission->setReferenceNumber($post_data['reference_number']);
            $submission->setLanguage(($post_data['language']) ? $post_data['language'] : $locale);
            $submission->setProtocol($protocol);
            $submission->setNumber(1);

            $submission->setOwner($user);

            // good practice type
            $selected_best_practice_type = $best_practice_type_repository->find($post_data['best_practice_type']);
            $submission->setType($selected_best_practice_type);

            // good practice role
            $selected_best_practice_role = $best_practice_role_repository->find($post_data['best_practice_role']);
            $submission->setRole($selected_best_practice_role);

            // call for good practices
            $selected_call = $best_practice_call_repository->find($post_data['best_practice_call']);
            $submission->setCall($selected_call);

            // institution
            $selected_institution = $institution_repository->find($post_data['institution']);
            $submission->setInstitution($selected_institution);

            // removing all stakeholders to re-add
            if ($submission->getStakeholder()) {
                foreach($submission->getStakeholder() as $stakeholder) {
                    $submission->removeStakeholder($stakeholder);
                }
            }
            // re-add stakeholders
            if(isset($post_data['stakeholder'])) {
                foreach($post_data['stakeholder'] as $stakeholder) {
                    $stakeholder = $stakeholder_repository->find($stakeholder);
                    $submission->addStakeholder($stakeholder);
                }
            }

            // removing all entities to re-add
            if ($submission->getEntity()) {
                foreach($submission->getEntity() as $entity) {
                    $submission->removeEntity($entity);
                }
            }
            // re-add entities
            if(isset($post_data['best_practice_entity'])) {
                foreach($post_data['best_practice_entity'] as $entity) {
                    $entity = $best_practice_entity_repository->find($entity);
                    $submission->addEntity($entity);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            $protocol->setMainSubmission($submission);
            $em->persist($protocol);
            $em->flush();

            // generate the code
            $committee_prefix = $util->getConfiguration('committee.prefix');
            $total_submissions = count($protocol->getSubmission());
            $protocol_code = sprintf('%s.%04d.%02d', $committee_prefix, $protocol->getId(), $total_submissions);
            $protocol->setCode($protocol_code);

            $route = 'submission_new_third_step';
            if ( in_array($submission->getInstitution()->getSlug(), array("civil-society-organization", "academic-institution", "scientific-community", "private-sector", "philanthropic-organization", "other")) ) {
                $route = 'submission_new_second_step';
            }

            $session->getFlashBag()->add('success', $translator->trans("Submission started with success."));
            return $this->redirectToRoute($route, array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/first", name="submission_new_first_created_protocol_step")
     * @Template()
     */
    public function FirstStepCreatedProtocolAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getSession()->get('_locale');

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting good practice type list
        $best_practice_type_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeType');
        $best_practice_type = $best_practice_type_repository->findByStatus(true);
        // $best_practice_type = $best_practice_type_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_type'] = $best_practice_type;

        // getting good practice role list
        $best_practice_role_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeRole');
        $best_practice_role = $best_practice_role_repository->findByStatus(true);
        // $best_practice_role = $best_practice_role_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_role'] = $best_practice_role;

        // getting good practice entity list
        $best_practice_entity_repository = $em->getRepository('Proethos2ModelBundle:BestPracticeEntity');
        $best_practice_entity = $best_practice_entity_repository->findByStatus(true);
        // $best_practice_entity = $best_practice_entity_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_entity'] = $best_practice_entity;

        // getting calls for good practices
        $best_practice_call_repository = $em->getRepository('Proethos2ModelBundle:Call');
        $best_practice_call = $best_practice_call_repository->findByStatus(true);
        // $best_practice_call = $best_practice_call_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['best_practice_call'] = $best_practice_call;

        // getting institution list
        $institution_repository = $em->getRepository('Proethos2ModelBundle:Institution');
        $institution = $institution_repository->findByStatus(true);
        // $institution = $institution_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['institution'] = $institution;

        // getting stakeholder list
        $stakeholder_repository = $em->getRepository('Proethos2ModelBundle:Stakeholder');
        $stakeholder = $stakeholder_repository->findByStatus(true);
        // $stakeholder = $stakeholder_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['stakeholder'] = $stakeholder;

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission or !$submission->getCanBeEdited() or ($submission->getCanBeEdited() and !in_array('investigator', $user->getRolesSlug()))) {
            throw $this->createNotFoundException($translator->trans('No submission found'));
        }

        $users = $user_repository->findAll();
        $output['users'] = $users;

        // modality options
        $coop_modality = array(
            "A" => $translator->trans("North-North"),
            "B" => $translator->trans("North-South"),
            "C" => $translator->trans("South-South"),
            "D" => $translator->trans("Triangular Cooperation"),
        );
        $output['coop_modality'] = $coop_modality;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-first-step-created', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // echo "<pre>"; print_r($post_data); echo "</pre>"; die();

            // checking required files
            foreach(array('title', 'proposal', 'best_practice_type', 'best_practice_role', 'institution', 'institution_name', 'stakeholder') as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            $submission->setTitle($post_data['title']);
            $submission->setProposal($post_data['proposal']);
            $submission->setOtherProposal($post_data['other_proposal']);
            $submission->setCoopModality($post_data['coop_modality']);
            $submission->setOtherRole($post_data['other_best_practice_role']);
            $submission->setOtherInstitution($post_data['other_institution']);
            $submission->setInstitutionName($post_data['institution_name']);
            $submission->setOtherStakeholder($post_data['other_stakeholder']);
            $submission->setReferenceNumber($post_data['reference_number']);
            $submission->setLanguage(($post_data['language']) ? $post_data['language'] : $locale);

            // good practice type
            $selected_best_practice_type = $best_practice_type_repository->find($post_data['best_practice_type']);
            $submission->setType($selected_best_practice_type);

            // good practice role
            $selected_best_practice_role = $best_practice_role_repository->find($post_data['best_practice_role']);
            $submission->setRole($selected_best_practice_role);

            // call for good practices
            $selected_call = $best_practice_call_repository->find($post_data['best_practice_call']);
            $submission->setCall($selected_call);

            // institution
            $selected_institution = $institution_repository->find($post_data['institution']);
            $submission->setInstitution($selected_institution);

            // removing all stakeholders to re-add
            if ($submission->getStakeholder()) {
                foreach($submission->getStakeholder() as $stakeholder) {
                    $submission->removeStakeholder($stakeholder);
                }
            }
            // re-add stakeholders
            if(isset($post_data['stakeholder'])) {
                foreach($post_data['stakeholder'] as $stakeholder) {
                    $stakeholder = $stakeholder_repository->find($stakeholder);
                    $submission->addStakeholder($stakeholder);
                }
            }

            // removing all entities to re-add
            if ($submission->getEntity()) {
                foreach($submission->getEntity() as $entity) {
                    $submission->removeEntity($entity);
                }
            }
            // re-add entities
            if(isset($post_data['best_practice_entity'])) {
                foreach($post_data['best_practice_entity'] as $entity) {
                    $entity = $best_practice_entity_repository->find($entity);
                    $submission->addEntity($entity);
                }
            }

            $em->persist($submission);
            $em->flush();

            $route = 'submission_new_third_step';
            if ( in_array($submission->getInstitution()->getSlug(), array("civil-society-organization", "academic-institution", "scientific-community", "private-sector", "philanthropic-organization", "other")) ) {
                $route = 'submission_new_second_step';
            }

            $session->getFlashBag()->add('success', $translator->trans("Submission saved with success."));
            return $this->redirectToRoute($route, array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/translation", name="submission_new_first_translation_protocol_step")
     * @Template()
     */
    public function FirstStepTranslationProtocolAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission) {
            throw $this->createNotFoundException($translator->trans('No submission found'));
        }

        $users = $user_repository->findAll();
        $output['users'] = $users;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-first-step-translation', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // checking required files
            foreach(array('scientific_title', 'public_title', 'language') as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            $protocol = $submission->getProtocol();

            $new_submission = new Submission();
            $new_submission->setIsTranslation(true);
            $new_submission->setOriginalSubmission($submission);
            $new_submission->setIsClinicalTrial($submission->getIsClinicalTrial());
            $new_submission->setIsConsultation($submission->getIsConsultation());
            $new_submission->setPublicTitle($post_data['public_title']);
            $new_submission->setScientificTitle($post_data['scientific_title']);
            $new_submission->setTitleAcronym($post_data['title_acronym']);
            $new_submission->setLanguage($post_data['language']);
            $new_submission->setProtocol($protocol);
            $new_submission->setNumber(1);

            $new_submission->setGender($submission->getGender());
            $new_submission->setSampleSize($submission->getSampleSize());
            $new_submission->setMinimumAge($submission->getMinimumAge());
            $new_submission->setMaximumAge($submission->getMaximumAge());
            $new_submission->setRecruitmentInitDate($submission->getRecruitmentInitDate());
            $new_submission->setRecruitmentStatus($submission->getRecruitmentStatus());
            $new_submission->setPriorEthicalApproval($submission->getPriorEthicalApproval());

            $new_submission->setOwner($submission->getOwner());

            $em = $this->getDoctrine()->getManager();
            $em->persist($new_submission);
            $em->flush();

            $submission->addTranslation($new_submission);
            $em->persist($submission);
            $em->flush();

            $route = 'submission_new_third_step';
            if ( in_array($submission->getInstitution()->getSlug(), array("civil-society-organization", "academic-institution", "scientific-community", "private-sector", "philanthropic-organization", "other")) ) {
                $route = 'submission_new_second_step';
            }

            $session->getFlashBag()->add('success', $translator->trans("Submission saved with success."));
            return $this->redirectToRoute($route, array('submission_id' => $new_submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/second", name="submission_new_second_step")
     * @Template()
     */
    public function SecondStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $upload_type_repository = $em->getRepository('Proethos2ModelBundle:UploadType');
        $submission_upload_repository = $em->getRepository('Proethos2ModelBundle:SubmissionUpload');

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        $upload_types = $upload_type_repository->findByStatus(true);
        $output['upload_types'] = $upload_types;

        $upload_type = $upload_type_repository->findBy(array('slug' => 'fensa'));
        $upload_type_id = $upload_type[0]->getId();
        $fensa_upload = $submission_upload_repository->findBy(array('submission' => $submission->getId(), 'upload_type' => $upload_type_id));
        $output['fensa_upload'] = $fensa_upload;

        $upload_type = $upload_type_repository->findBy(array('slug' => 'fensa-tobacco-arms'));
        $upload_type_id = $upload_type[0]->getId();
        $tobacco_arms_upload = $submission_upload_repository->findBy(array('submission' => $submission->getId(), 'upload_type' => $upload_type_id));
        $output['tobacco_arms_upload'] = $tobacco_arms_upload;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-second-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // echo "<pre>"; print_r($post_data); echo "</pre>"; die();

            $file = $request->files->get('new-attachment-file');
            if(!empty($file)) {

                $upload_type = $upload_type_repository->findOneBy(array("slug" => $post_data['upload_type']));

                $file_ext = '.'.$file->getClientOriginalExtension();
                $ext_formats = $upload_type->getExtensionsFormat();
                if ( !in_array($file_ext, $ext_formats) ) {
                    $session->getFlashBag()->add('error', $translator->trans("File extension not allowed"));
                    return $output;
                }

                $submission_upload = new SubmissionUpload();
                $submission_upload->setSubmission($submission);
                $submission_upload->setUploadType($upload_type);
                $submission_upload->setUser($user);
                $submission_upload->setFile($file);
                $submission_upload->setSubmissionNumber($submission->getNumber());

                $em = $this->getDoctrine()->getManager();
                $em->persist($submission_upload);
                $em->flush();

                $submission->addAttachment($submission_upload);
                $em = $this->getDoctrine()->getManager();
                $em->persist($submission);
                $em->flush();

                $session->getFlashBag()->add('success', $translator->trans("File uploaded with success."));
                return $this->redirectToRoute('submission_new_second_step', array('submission_id' => $submission->getId()), 301);

            }

            if(isset($post_data['delete-attachment-id']) and !empty($post_data['delete-attachment-id'])) {

                $submission_upload = $submission_upload_repository->find($post_data['delete-attachment-id']);
                if($submission_upload) {

                    $em->remove($submission_upload);
                    $em->flush();
                    $session->getFlashBag()->add('success', $translator->trans("File removed with success."));
                    return $this->redirectToRoute('submission_new_second_step', array('submission_id' => $submission->getId()), 301);
                }
            }

            $session->getFlashBag()->add('success', $translator->trans("Submission saved with success."));
            return $this->redirectToRoute('submission_new_third_step', array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/third", name="submission_new_third_step")
     * @Template()
     */
    public function ThirdStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $user_repository = $em->getRepository('Proethos2ModelBundle:User');
        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        // $submission_country_repository = $em->getRepository('Proethos2ModelBundle:SubmissionCountry');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting subregion list
        $subregion_repository = $em->getRepository('Proethos2ModelBundle:SubRegion');
        $subregion = $subregion_repository->findByStatus(true);
        // $subregion = $subregion_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['subregion'] = $subregion;

        // getting population group list
        $population_group_repository = $em->getRepository('Proethos2ModelBundle:PopulationGroup');
        $population_group = $population_group_repository->findByStatus(true);
        // $population_group = $population_group_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['population_group'] = $population_group;

        // getting countries list
        $country_repository = $em->getRepository('Proethos2ModelBundle:Country');
        // $country = $country_repository->findByStatus(true);
        $countries = $country_repository->findBy(array('status' => true), array('name' => 'asc'));
        $output['countries'] = $countries;

        // getting technical matter list
        $technical_matter_repository = $em->getRepository('Proethos2ModelBundle:TechnicalMatter');
        $technical_matter = $technical_matter_repository->findByStatus(true);
        // $technical_matter = $technical_matter_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['technical_matter'] = $technical_matter;

        // getting intervention list
        $intervention_repository = $em->getRepository('Proethos2ModelBundle:Intervention');
        $intervention = $intervention_repository->findByStatus(true);
        // $intervention = $intervention_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['intervention'] = $intervention;

        // getting target list
        $target_repository = $em->getRepository('Proethos2ModelBundle:Target');
        $target = $target_repository->findByStatus(true);
        // $target = $target_repository->findBy(array('status' => true), array('name' => 'ASC'));
        $output['target'] = $target;

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        $users = $user_repository->findBy(array(), array('email' => 'ASC'));
        $output['users'] = $users;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-third-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // echo "<pre>"; print_r($post_data); echo "</pre>"; die();

            // checking required files
            $required_fields = array(
                'technical_matter',
                'intervention',
                'start-date',
                'country',
                'subregion',
                'target',
                'population_group',
                'introduction',
                'objectives',
                'main_results'
            );

            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            // removing all countries to re-add
            if ($submission->getCountry()) {
                foreach($submission->getCountry() as $country) {
                    $submission->removeCountry($country);
                }
            }
            // re-add countries
            if(isset($post_data['country'])) {
                foreach($post_data['country'] as $country) {
                    $country = $country_repository->find($country);
                    $submission->addCountry($country);
                }
            }

            // removing all subregions to re-add
            if ($submission->getSubregion()) {
                foreach($submission->getSubregion() as $subregion) {
                    $submission->removeSubregion($subregion);
                }
            }
            // re-add subregions
            if(isset($post_data['subregion'])) {
                foreach($post_data['subregion'] as $subregion) {
                    $subregion = $subregion_repository->find($subregion);
                    $submission->addSubregion($subregion);
                }
            }

            // removing all population groups to re-add
            if ($submission->getPopulationGroup()) {
                foreach($submission->getPopulationGroup() as $population_group) {
                    $submission->removePopulationGroup($population_group);
                }
            }
            // re-add population groups
            if(isset($post_data['population_group'])) {
                foreach($post_data['population_group'] as $population_group) {
                    $population_group = $population_group_repository->find($population_group);
                    $submission->addPopulationGroup($population_group);
                }
            }

            // removing all technical matters to re-add
            if ($submission->getTechnicalMatter()) {
                foreach($submission->getTechnicalMatter() as $technical_matter) {
                    $submission->removeTechnicalMatter($technical_matter);
                }
            }
            // re-add technical matters
            if(isset($post_data['technical_matter'])) {
                foreach($post_data['technical_matter'] as $technical_matter) {
                    $technical_matter = $technical_matter_repository->find($technical_matter);
                    $submission->addTechnicalMatter($technical_matter);
                }
            }

            // removing all interventions to re-add
            if ($submission->getIntervention()) {
                foreach($submission->getIntervention() as $intervention) {
                    $submission->removeIntervention($intervention);
                }
            }
            // re-add interventions
            if(isset($post_data['intervention'])) {
                foreach($post_data['intervention'] as $intervention) {
                    $intervention = $intervention_repository->find($intervention);
                    $submission->addIntervention($intervention);
                }
            }

            // removing all targets to re-add
            if ($submission->getTarget()) {
                foreach($submission->getTarget() as $target) {
                    $submission->removeTarget($target);
                }
            }
            // re-add targets
            if(isset($post_data['target'])) {
                foreach($post_data['target'] as $target) {
                    $target = $target_repository->find($target);
                    $submission->addTarget($target);
                }
            }

            // adding fields to model
            $submission->setOtherPopulationGroup($post_data['other_population_group']);
            $submission->setOtherTechnicalMatter($post_data['other_technical_matter']);
            $submission->setOtherIntervention($post_data['other_intervention']);
            $submission->setIntroduction($post_data['introduction']);
            $submission->setObjectives($post_data['objectives']);
            $submission->setActivities($post_data['activities']);
            $submission->setMainResults($post_data['main_results']);
            $submission->setFactors($post_data['factors']);
            $submission->setStartDate(new \DateTime($post_data['start-date']));
            if ( $post_data['end-date'] ) $submission->setEnddate(new \DateTime($post_data['end-date']));

            $em->persist($submission);
            $em->flush();

            $session->getFlashBag()->add('success', "Submission saved with success.");
            return $this->redirectToRoute('submission_new_fourth_step', array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/fourth", name="submission_new_fourth_step")
     * @Template()
     */
    public function FourthStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // likert options
        $likert = array(
            "A" => $translator->trans("I fully agree"),
            "B" => $translator->trans("I agree"),
            "C" => $translator->trans("I can't say"),
            "D" => $translator->trans("I disagree"),
            "E" => $translator->trans("I totally disagree")
        );

        $output['likert'] = $likert;

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-fourth-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // checking required files
            $required_fields = array('resources_assigned', 'scalability', 'adaptability_replicability', 'other_contexts_demo');

            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            // adding fields to model
            $submission->setResourcesAssigned($post_data['resources_assigned']);
            $submission->setOutcomeInformation($post_data['outcome_information']);
            $submission->setScalability($post_data['scalability']);
            $submission->setAdaptabilityReplicability($post_data['adaptability_replicability']);
            $submission->setOtherContextsDemo($post_data['other_contexts_demo']);
            $submission->setDescribeHow($post_data['describe_how']);
            $submission->setSustainability($post_data['sustainability']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            if ( 'paho-who-technical-cooperation' == $submission->getType()->getSlug() ) {
                $route = 'submission_new_fifth_step';
            } elseif ( $attributes ) {
                $route = 'submission_new_technical_attributes';
            } else {
                $route = 'submission_new_sixth_step';
            }

            $session->getFlashBag()->add('success', "Submission saved with success.");
            return $this->redirectToRoute($route, array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/fifth", name="submission_new_fifth_step")
     * @Template()
     */
    public function FifthStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting outcomes list
        $outcomes_repository = $em->getRepository('Proethos2ModelBundle:Outcomes');
        $outcomes = $outcomes_repository->findByStatus(true);
        $output['outcomes'] = $outcomes;

        // getting goals list
        $goals_repository = $em->getRepository('Proethos2ModelBundle:Goals');
        $goals = $goals_repository->findByStatus(true);
        $output['goals'] = $goals;

        // likert options
        $likert = array(
            "A" => $translator->trans("I fully agree"),
            "B" => $translator->trans("I agree"),
            "C" => $translator->trans("I can't say"),
            "D" => $translator->trans("I disagree"),
            "E" => $translator->trans("I totally disagree")
        );

        $output['likert'] = $likert;

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-fifth-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // checking required files
            $required_fields = array(
                'health_system_contribution',
                'value_chain_organization',
                'outcomes',
                'goals',
                // 'relevance_information',
                'counterpart_recognized',
                'catalytic_role',
                'neutral_role',
                'recognition_information',
                'cross_cutting_approach',
                'engagement_information'
            );

            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            // adding fields to model
            $submission->setHealthSystemContribution($post_data['health_system_contribution']);
            $submission->setValueChainOrganization($post_data['value_chain_organization']);
            $submission->setPublicHealthIssue($post_data['public_health_issue']);
            $submission->setPlanningInformation($post_data['planning_information']);
            // $submission->setRelevanceInformation($post_data['relevance_information']);
            $submission->setCounterpartRecognized($post_data['counterpart_recognized']);
            $submission->setCatalyticRole($post_data['catalytic_role']);
            $submission->setNeutralRole($post_data['neutral_role']);
            $submission->setRecognitionInformation($post_data['recognition_information']);
            $submission->setCrossCuttingApproach($post_data['cross_cutting_approach']);
            $submission->setEngagementInformation($post_data['engagement_information']);

            // outcomes
            $selected_outcomes = $outcomes_repository->find($post_data['outcomes']);
            $submission->setOutcomes($selected_outcomes);

            // goals
            $selected_goals = $goals_repository->find($post_data['goals']);
            $submission->setGoals($selected_goals);

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            $route = 'submission_new_sixth_step';
            if ( $attributes ) {
                $route = 'submission_new_technical_attributes';
            }

            $session->getFlashBag()->add('success', $translator->trans("Submission saved with success."));
            return $this->redirectToRoute($route, array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/sixth", name="submission_new_sixth_step")
     * @Template()
     */
    public function SixthStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $upload_type_repository = $em->getRepository('Proethos2ModelBundle:UploadType');
        $submission_upload_repository = $em->getRepository('Proethos2ModelBundle:SubmissionUpload');

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        $upload_types = $upload_type_repository->findByStatus(true);
        $output['upload_types'] = $upload_types;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-sixth-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            $file = $request->files->get('new-atachment-file');
            if(!empty($file)) {

                $upload_type = $upload_type_repository->findOneBy(array("slug" => "image"));

                $file_ext = '.'.$file->getClientOriginalExtension();
                $ext_formats = $upload_type->getExtensionsFormat();
                if ( !in_array($file_ext, $ext_formats) ) {
                    $session->getFlashBag()->add('error', $translator->trans("File extension not allowed"));
                    return $output;
                }

                $submission_upload = new SubmissionUpload();
                $submission_upload->setSubmission($submission);
                $submission_upload->setUploadType($upload_type);
                $submission_upload->setUser($user);
                $submission_upload->setFile($file);
                $submission_upload->setSubmissionNumber($submission->getNumber());

                $em = $this->getDoctrine()->getManager();
                $em->persist($submission_upload);
                $em->flush();

                $submission->addAttachment($submission_upload);
                $em = $this->getDoctrine()->getManager();
                $em->persist($submission);
                $em->flush();

                $session->getFlashBag()->add('success', $translator->trans("File uploaded with success."));
                return $this->redirectToRoute('submission_new_sixth_step', array('submission_id' => $submission->getId()), 301);

            }

            if(isset($post_data['delete-attachment-id']) and !empty($post_data['delete-attachment-id'])) {

                $submission_upload = $submission_upload_repository->find($post_data['delete-attachment-id']);
                if($submission_upload) {

                    $em->remove($submission_upload);
                    $em->flush();
                    $session->getFlashBag()->add('success', $translator->trans("File removed with success."));
                    return $this->redirectToRoute('submission_new_sixth_step', array('submission_id' => $submission->getId()), 301);
                }
            }

            // checking required fields
            $required_fields = array('challenges_information', 'lessons_information');
            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

             // adding fields to model
            $submission->setDescriptors($post_data['descriptors']);
            $submission->setChallengesInformation($post_data['challenges_information']);
            $submission->setLessonsInformation($post_data['lessons_information']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            $session->getFlashBag()->add('success', "Submission saved with success.");
            return $this->redirectToRoute('submission_new_seventh_step', array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/seventh", name="submission_new_seventh_step")
     * @Template()
     */
    public function SeventhStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');
        // $submission_country_repository = $em->getRepository('Proethos2ModelBundle:SubmissionCountry');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-seventh-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();
/*
            // checking required files
            $required_fields = array('products_information', 'other_sources_information');
            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }
*/
            $submission->setProductsInformation($post_data['products_information']);
            $submission->setOtherSourcesInformation($post_data['other_sources_information']);
            $submission->setPahoComments($post_data['paho_comments']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            $session->getFlashBag()->add('success', "Submission saved with success.");
            return $this->redirectToRoute('submission_new_eighth_step', array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/eighth", name="submission_new_eighth_step")
     * @Template()
     */
    public function EighthStepAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getSession()->get('_locale');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $util = new Util($this->container, $this->getDoctrine());

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $upload_type_repository = $em->getRepository('Proethos2ModelBundle:UploadType');
        $submission_upload_repository = $em->getRepository('Proethos2ModelBundle:SubmissionUpload');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        // getting submission_attribute
        $submission_attribute_repository = $em->getRepository('Proethos2ModelBundle:SubmissionTechnicalAttribute');
        $submission_attributes = $submission_attribute_repository->findBy(array("submission" => $submission));
        $output['submission_attributes'] = $submission_attributes;

        $mail_translator = $this->get('translator');
        $mail_translator->setLocale($submission->getLanguage());

        $trans_repository = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');
        $help_repository = $em->getRepository('Proethos2ModelBundle:Help');
        // $help = $help_repository->findBy(array("id" => {id}, "type" => "mail"));
        // $translations = $trans_repository->findTranslations($help[0]);

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // Revisions
        $revisions = array();
        $final_status = true;

        // $text = $translator->trans("%total% member(s)", array("%total%" => $submission->getTotalTeam()));
        // $item = array('text' => $text, 'status' => true);
        // $revisions[] = $item;

        $text = $translator->trans('Title');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getTitle())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Proposal');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getProposal())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Type');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getType())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Role');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getRole())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        if ( 'other' == $submission->getRole()->getSlug() ) {

            $text = $translator->trans('Other Role');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getOtherRole())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

        }

        $text = $translator->trans('Type of institution reporting the Good Practice');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getInstitution())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        if ( 'other' == $submission->getInstitution()->getSlug() ) {

            $text = $translator->trans('Other Institution');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getOtherInstitution())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

        }

        $text = $translator->trans("Institution's Name");
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getInstitutionName())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Counterparts and Partnerships');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getStakeholderList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        if ( 'paho-who-technical-cooperation' == $submission->getType()->getSlug() ) {

            $text = $translator->trans('Entity');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getEntityList())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Reference Number');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getReferenceNumber())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

        }

        if ( in_array($submission->getInstitution()->getSlug(), array("civil-society-organization", "academic-institution", "scientific-community", "private-sector", "philanthropic-organization", "other")) ) {

            // checking required fensa attachment
            $text = $translator->trans('FENSA');
            $item = array('text' => $text, 'status' => true);
            $upload_type = $upload_type_repository->findBy(array('slug' => 'fensa'));
            $upload_type_id = $upload_type[0]->getId();
            $fensa_upload = $submission_upload_repository->findBy(array('submission' => $submission->getId(), 'upload_type' => $upload_type_id));
            if( !$fensa_upload or count($fensa_upload) != 1 ) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            // checking required tobacco/arms attachment
            $text = $translator->trans('FENSA Tobacco/Arms');
            $item = array('text' => $text, 'status' => true);
            $upload_type = $upload_type_repository->findBy(array('slug' => 'fensa-tobacco-arms'));
            $upload_type_id = $upload_type[0]->getId();
            $tobacco_arms_upload = $submission_upload_repository->findBy(array('submission' => $submission->getId(), 'upload_type' => $upload_type_id));
            if( !$tobacco_arms_upload or count($tobacco_arms_upload) != 1 ) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

        }

        $text = $translator->trans('Technical Matters');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getTechnicalMatterList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Interventions');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getInterventionList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Start Date');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getStartDate())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Country');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getCountryList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Subregion');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getSubregionList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Target');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getTargetList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Population Group');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getPopulationGroupList())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Introduction');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getIntroduction())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Objectives');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getObjectives())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Main Results');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getMainResults())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Resources Assigned');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getResourcesAssigned())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Scalability');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getScalability())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Adaptability and Replicability');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getAdaptabilityReplicability())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Other Contexts');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getOtherContextsDemo())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        if ( 'paho-who-technical-cooperation' == $submission->getType()->getSlug() ) {

            $text = $translator->trans('Health System Contribution');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getHealthSystemContribution())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Outcomes');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getOutcomes())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Goals');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getGoals())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans("Organization's Value Chain");
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getValueChainOrganization())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Counterpart');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getCounterpartRecognized())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Catalytic Role');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getCatalyticRole())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Neutral Role');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getNeutralRole())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Recognition Information');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getRecognitionInformation())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Cross Cutting Approach');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getCrossCuttingApproach())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

            $text = $translator->trans('Engagement Information');
            $item = array('text' => $text, 'status' => true);
            if(empty($submission->getEngagementInformation())) {
                $item = array('text' => $text, 'status' => false);
                $final_status = false;
            }
            $revisions[] = $item;

        }
/*
        $text = $translator->trans('Attachments');
        $item = array('text' => $text, 'status' => true);
        $upload_type = $upload_type_repository->findOneBy(array('slug' => 'image'));
        $upload_type_id = $upload_type->getId();
        $submission_data = $submission_upload_repository->findBy(array('submission' => $submission->getId(), 'upload_type' => $upload_type_id));
        if( !$submission_data ) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;
*/
        $text = $translator->trans('Challenges Information');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getChallengesInformation())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $text = $translator->trans('Lessons Information');
        $item = array('text' => $text, 'status' => true);
        if(empty($submission->getLessonsInformation())) {
            $item = array('text' => $text, 'status' => false);
            $final_status = false;
        }
        $revisions[] = $item;

        $output['revisions'] = $revisions;
        $output['final_status'] = $final_status;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-eighth-step', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // echo "<pre>"; print_r($post_data); echo "</pre>"; die();

            // checking required files
            $required_fields = array('interest_conflicts');

            foreach($required_fields as $field) {
                if(!isset($post_data[$field]) or empty($post_data[$field])) {
                    $session->getFlashBag()->add('error', $translator->trans("Field '%field%' is required.", array("%field%" => $field)));
                    return $output;
                }
            }

            if($final_status) {

                if($post_data['accept-terms'] == 'on') {

                    // gerando um novo pdf da submission original
                    try {
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl . $this->generateUrl('home');
                        $output['home_url'] = rtrim($url, '/');

                        $submission->setInterestConflicts($post_data['interest_conflicts']);
                        $submission->setOtherInterestConflicts($post_data['other_interest_conflicts']);
                        $output['submission'] = $submission;

                        // likert options
                        $likert = array(
                            "A" => $translator->trans("I fully agree"),
                            "B" => $translator->trans("I agree"),
                            "C" => $translator->trans("I can't say"),
                            "D" => $translator->trans("I disagree"),
                            "E" => $translator->trans("I totally disagree")
                        );
                        $output['likert'] = $likert;

                        // modality options
                        $coop_modality = array(
                            "A" => $translator->trans("North-North"),
                            "B" => $translator->trans("North-South"),
                            "C" => $translator->trans("South-South"),
                            "D" => $translator->trans("Triangular Cooperation"),
                        );
                        $output['coop_modality'] = $coop_modality;

                        $html = $this->renderView(
                            'Proethos2CoreBundle:NewSubmission:showPdf.html.twig',
                            $output
                        );

                        $pdf = $this->get('knp_snappy.pdf');

                        if ( version_compare(PHP_VERSION, '7.3.0') < 0 ) {
                            // setting margins
                            $pdf->getInternalGenerator()->setOption('margin-top', '50px');
                            $pdf->getInternalGenerator()->setOption('margin-bottom', '50px');
                            $pdf->getInternalGenerator()->setOption('margin-left', '20px');
                            $pdf->getInternalGenerator()->setOption('margin-right', '20px');
                        }

                        // adding pdf to tmp file
                        $filepath = "/tmp/" . uniqid() . '-' . date("Y-m-d-H\hi\ms\s") . "-submission.pdf";
                        file_put_contents($filepath, $pdf->getOutputFromHtml($html));

                        $submission_number = count($submission->getProtocol()->getSubmission());

                        $upload_type = $upload_type_repository->findOneBy(array("slug" => "protocol"));

                        // send tmp file to upload class and save
                        $pdfFile = new SubmissionUpload();
                        $pdfFile->setSubmission($submission);
                        $pdfFile->setSimpleFile($filepath);
                        $pdfFile->setUploadType($upload_type);
                        $pdfFile->setUser($user);
                        $pdfFile->setSubmissionNumber($submission->getNumber());
                        $em->persist($pdfFile);
                        $em->flush();

                    } catch(\RuntimeException $e) {

                        if($post_data['extra'] != 'no-pdf') {
                            $session->getFlashBag()->add('error', $translator->trans('Problems generating PDF. Please contact the administrator.'));
                            return $output;
                        }
                    }

                    // genrating pdf from translations
                    foreach($submission->getTranslations() as $translation) {

                        $new_output = $output;
                        $new_output['submission'] = $translation;

                        // gerando um novo pdf
                        try {
                            $html = $this->renderView(
                                'Proethos2CoreBundle:NewSubmission:showPdf.html.twig',
                                $new_output
                            );

                            $pdf = $this->get('knp_snappy.pdf');

                            if ( version_compare(PHP_VERSION, '7.3.0') < 0 ) {
                                // setting margins
                                $pdf->getInternalGenerator()->setOption('margin-top', '50px');
                                $pdf->getInternalGenerator()->setOption('margin-bottom', '50px');
                                $pdf->getInternalGenerator()->setOption('margin-left', '20px');
                                $pdf->getInternalGenerator()->setOption('margin-right', '20px');
                            }

                            // adding pdf to tmp file
                            $filepath = "/tmp/" . uniqid() . '-' . date("Y-m-d-H\hi\ms\s") . "-submission-". $translation->getLanguage() .".pdf";
                            file_put_contents($filepath, $pdf->getOutputFromHtml($html));

                            $upload_type = $upload_type_repository->findOneBy(array("slug" => "protocol"));

                            // send tmp file to upload class and save
                            $pdfFile = new SubmissionUpload();
                            $pdfFile->setSubmission($submission);
                            $pdfFile->setSimpleFile($filepath);
                            $pdfFile->setUploadType($upload_type);
                            $pdfFile->setUser($user);
                            $pdfFile->setSubmissionNumber($submission->getNumber());
                            $em->persist($pdfFile);
                            $em->flush();

                        } catch(\RuntimeException $e) {

                            if($post_data['extra'] != 'no-pdf') {
                                $session->getFlashBag()->add('error', $translator->trans('Problems generating PDF. Please contact the administrator.'));
                                return $output;
                            }
                        }
                    }

                    // in case of editing migrated posts
                    if ($submission->getProtocol()->getIsMigrated() and !$submission->getCanBeEdited()) {
                        $em->persist($submission);
                        $em->flush();

                        $session->getFlashBag()->add('success', $translator->trans("Good practice submitted with success!"));
                        return $this->redirectToRoute('protocol_show_protocol', array('protocol_id' => $submission->getProtocol()->getId()), 301);
                    }

                    // updating protocol and setting status
                    $protocol = $submission->getProtocol();
                    $protocol->setStatus("S");
                    $protocol->setDateInformed(new \DateTime());
                    $protocol->setUpdatedIn(new \DateTime());
                    $em->persist($protocol);
                    $em->flush();

                    // generate the code
                    if ( !$protocol->getCode() ) {
                        $committee_prefix = $util->getConfiguration('committee.prefix');
                        $total_submissions = count($protocol->getSubmission());
                        $protocol_code = sprintf('%s.%04d.%02d', $committee_prefix, $protocol->getId(), $total_submissions);
                        $protocol->setCode($protocol_code);
                    }

                    // adding fields to model
                    $submission->setInterestConflicts($post_data['interest_conflicts']);
                    $submission->setOtherInterestConflicts($post_data['other_interest_conflicts']);
                    $submission->setIsSended(true);
                    $em->persist($submission);
                    $em->flush();

                    $protocol_history = new ProtocolHistory();
                    $protocol_history->setProtocol($protocol);
                    $protocol_history->setUser($user);
                    $protocol_history->setMessage($translator->trans("Submission of good practice."));
                    $em->persist($protocol_history);
                    $em->flush();

                    if($protocol->getMonitoringAction()) {
                        // sending email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl . $this->generateUrl('protocol_show_protocol', array("protocol_id" => $protocol->getId()));
                        $edit_submission_url = $baseurl . $this->generateUrl('submission_new_first_created_protocol_step', array("submission_id" => $submission->getId()));

                        $help = $help_repository->find(201);
                        $translations = $trans_repository->findTranslations($help);
                        $text = $translations[$submission->getLanguage()];
                        $body = $text['message'];
                        $body = str_replace("%protocol_url%", $url, $body);
                        $body = str_replace("%edit_submission_url%", $edit_submission_url, $body);
                        $body = str_replace("%protocol_code%", $protocol->getCode(), $body);
                        $body = str_replace("\r\n", "<br />", $body);
                        $body .= "<br /><br />";

                        $secretaries_emails = array();
                        foreach($user_repository->findAll() as $secretary) {
                            if(in_array("secretary", $secretary->getRolesSlug())) {
                                $secretaries_emails[] = $secretary->getEmail();
                            }
                        }

                        $message = \Swift_Message::newInstance()
                        ->setSubject($translator->trans("[GP]") . " " . $mail_translator->trans("A new monitoring action has been submitted."))
                        ->setFrom($util->getConfiguration('committee.email'))
                        ->setTo($secretaries_emails)
                        ->setBody(
                            $body
                            ,
                            'text/html'
                        );

                        $send = $this->get('mailer')->send($message);

                        $session->getFlashBag()->add('success', $translator->trans("Amendment submitted with success!"));
                    } else {
                        // sending email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $home_url = $baseurl . $this->generateUrl('home');
                        $url = $baseurl . $this->generateUrl('protocol_show_protocol', array("protocol_id" => $protocol->getId()));
                        $edit_submission_url = $baseurl . $this->generateUrl('submission_new_first_created_protocol_step', array("submission_id" => $submission->getId()));

                        // sending message to investigator
                        $help = $help_repository->find(202);
                        $translations = $trans_repository->findTranslations($help);
                        $text = $translations[$submission->getLanguage()];
                        $body = $text['message'];
                        $body = str_replace("%protocol_url%", $url, $body);
                        $body = str_replace("%edit_submission_url%", $edit_submission_url, $body);
                        $body = str_replace("%protocol_code%", $protocol->getCode(), $body);
                        $body = str_replace("\r\n", "<br />", $body);
                        $body .= "<br /><br />";

                        $recipient = $protocol->getMainSubmission()->getOwner();

                        $message = \Swift_Message::newInstance()
                        ->setSubject($translator->trans("[GP]") . " " . $mail_translator->trans("Your good practice was sent to review."))
                        ->setFrom($util->getConfiguration('committee.email'))
                        ->setTo($recipient->getEmail())
                        ->setBody(
                            $body
                            ,
                            'text/html'
                        );

                        $send = $this->get('mailer')->send($message);

                        $help = $help_repository->find(217);
                        $translations = $trans_repository->findTranslations($help);
                        $text = $translations[$submission->getLanguage()];
                        $body = $text['message'];
                        $body = str_replace("%home_url%", $home_url, $body);
                        $body = str_replace("%protocol_url%", $url, $body);
                        $body = str_replace("%edit_submission_url%", $edit_submission_url, $body);
                        $body = str_replace("%protocol_code%", $protocol->getCode(), $body);
                        $body = str_replace("\r\n", "<br />", $body);
                        $body .= "<br /><br />";

                        $secretaries_emails = array();
                        foreach($user_repository->findAll() as $secretary) {
                            if(in_array("secretary", $secretary->getRolesSlug())) {
                                $secretaries_emails[] = $secretary->getEmail();
                            }
                        }

                        $message = \Swift_Message::newInstance()
                        ->setSubject($translator->trans("[GP]") . " " . $mail_translator->trans("A new good practice has been submitted."))
                        ->setFrom($util->getConfiguration('committee.email'))
                        ->setTo($secretaries_emails)
                        ->setBody(
                            $body
                            ,
                            'text/html'
                        );

                        $send = $this->get('mailer')->send($message);

                        $session->getFlashBag()->add('success', $translator->trans("Good practice submitted with success!"));
                    }

                    return $this->redirectToRoute('protocol_show_protocol', array('protocol_id' => $protocol->getId()), 301);

                } else {
                    $session->getFlashBag()->add('error', $translator->trans("You must accept the terms and conditions."));
                }
            } else {
                $session->getFlashBag()->add('error', $translator->trans('You have pending reviews.'));
            }
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/technical-attributes", name="submission_new_technical_attributes")
     * @Template()
     */
    public function TechnicalAttributesAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $user_repository = $em->getRepository('Proethos2ModelBundle:User');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        // getting the current submission
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        if (!$submission or $submission->getCanBeEdited() == false) {
            if(!$submission or ($submission->getProtocol()->getIsMigrated() and !in_array('administrator', $user->getRolesSlug()))) {
                throw $this->createNotFoundException($translator->trans('No submission found'));
            }
        }

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        // getting submission_attribute
        $submission_attribute_repository = $em->getRepository('Proethos2ModelBundle:SubmissionTechnicalAttribute');
        $submission_attributes = $submission_attribute_repository->findBy(array("submission" => $submission));
        $output['submission_attributes'] = $submission_attributes;

        // checking if was a post request
        if($this->getRequest()->isMethod('POST')) {

            $submittedToken = $request->request->get('token');

            if (!$this->isCsrfTokenValid('submission-technical-attributes', $submittedToken)) {
                throw $this->createNotFoundException($translator->trans('CSRF token not valid'));
            }

            // getting post data
            $post_data = $request->request->all();

            // echo "<pre>"; print_r($post_data); echo "</pre>"; die();

            $technical_attributes = array_filter( $post_data, function ($key) { 
                return(strpos($key,'input-attribute') !== false);
            }, ARRAY_FILTER_USE_KEY );

            if ( $technical_attributes ) {
                foreach ($technical_attributes as $attr) {
                    // getting the technical_attribute
                    $technical_attribute = $attribute_repository->find($attr);

                    // getting the submission_technical_attribute
                    $submission_technical_attribute = $submission_attribute_repository->findOneBy(array(
                        "submission" => $submission,
                        "attribute" => $technical_attribute
                    ));

                    if ( $submission_technical_attribute ) {
                        $submission_technical_attribute->setValue($post_data['attribute-'.$attr.'-value']);
                        
                        $em->persist($submission_technical_attribute);
                        $em->flush();
                    } else {
                        $submission_technical_attribute = new SubmissionTechnicalAttribute();
                        $submission_technical_attribute->setSubmission($submission);
                        $submission_technical_attribute->setAttribute($technical_attribute);
                        $submission_technical_attribute->setValue($post_data['attribute-'.$attr.'-value']);
                        
                        $em->persist($submission_technical_attribute);
                        $em->flush();
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($submission);
            $em->flush();

            $session->getFlashBag()->add('success', "Submission saved with success.");
            return $this->redirectToRoute('submission_new_sixth_step', array('submission_id' => $submission->getId()), 301);
        }

        return $output;
    }

    /**
     * @Route("/submission/new/{submission_id}/pdf", name="submission_generate_pdf")
     * @Template()
     */
    public function showPdfAction($submission_id)
    {
        $output = array();
        $request = $this->getRequest();
        $session = $request->getSession();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $util = new Util($this->container, $this->getDoctrine());

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $url = $baseurl . $this->generateUrl('home');
        $output['home_url'] = rtrim($url, '/');

        $configuration_repository = $em->getRepository('Proethos2ModelBundle:Configuration');

        // getting the current submission
        $submission_repository = $em->getRepository('Proethos2ModelBundle:Submission');
        $submission = $submission_repository->find($submission_id);
        $output['submission'] = $submission;

        $protocol = $submission->getProtocol();
        $committee_prefix = $util->getConfiguration('committee.prefix');
        $total_submissions = count($protocol->getSubmission());
        $protocol_code = sprintf('%s.%04d.%02d', $committee_prefix, $protocol->getId(), $total_submissions);
        $output['protocol_code'] = $protocol_code;

        // likert options
        $likert = array(
            "A" => $translator->trans("I fully agree"),
            "B" => $translator->trans("I agree"),
            "C" => $translator->trans("I can't say"),
            "D" => $translator->trans("I disagree"),
            "E" => $translator->trans("I totally disagree")
        );
        $output['likert'] = $likert;

        // modality options
        $coop_modality = array(
            "A" => $translator->trans("North-North"),
            "B" => $translator->trans("North-South"),
            "C" => $translator->trans("South-South"),
            "D" => $translator->trans("Triangular Cooperation"),
        );
        $output['coop_modality'] = $coop_modality;

        // getting technical attribute list
        $attribute_repository = $em->getRepository('Proethos2ModelBundle:TechnicalAttribute');
        $attributes = $attribute_repository->findByCall($submission->getCall());
        $output['attributes'] = $attributes;

        // getting submission_attribute
        $submission_attribute_repository = $em->getRepository('Proethos2ModelBundle:SubmissionTechnicalAttribute');
        $submission_attributes = $submission_attribute_repository->findBy(array("submission" => $submission));
        $output['submission_attributes'] = $submission_attributes;

        if (!$submission or ($submission->getCanBeEdited() and !in_array('investigator', $user->getRolesSlug()))) {
            throw $this->createNotFoundException($translator->trans('No submission found'));
        }

        $html = $this->renderView(
            'Proethos2CoreBundle:NewSubmission:showPdf.html.twig',
            $output
        );

        $pdf = $this->get('knp_snappy.pdf');

        if ( version_compare(PHP_VERSION, '7.3.0') < 0 ) {
            // setting margins
            $pdf->getInternalGenerator()->setOption('margin-top', '50px');
            $pdf->getInternalGenerator()->setOption('margin-bottom', '50px');
            $pdf->getInternalGenerator()->setOption('margin-left', '20px');
            $pdf->getInternalGenerator()->setOption('margin-right', '20px');
        }

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf'
            )
        );
    }
}
