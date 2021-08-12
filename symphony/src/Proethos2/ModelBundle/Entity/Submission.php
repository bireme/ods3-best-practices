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


namespace Proethos2\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

use Proethos2\CoreBundle\Util\Security;


/**
 * Submission
 *
 * @ORM\Table(name="submission")
 * @ORM\Entity(repositoryClass="Proethos2\ModelBundle\Repository\SubmissionRepository")
 */
class Submission extends Base
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Protocol
     *
     * @ORM\ManyToOne(targetEntity="Protocol", inversedBy="submissions")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Assert\NotBlank
     */
    private $protocol;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_translation", type="boolean")
     */
    private $is_translation = false;

    /**
     * @var Submission
     *
     * @ORM\ManyToOne(targetEntity="Submission")
     * @ORM\JoinColumn(name="original_submission_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $original_submission;

    /**
     * @var User
     *
     * @Exclude
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Assert\NotBlank
     */
    private $owner;

    /**
     * @var Team
     * @ORM\ManyToMany(targetEntity="User", inversedBy="users")
     * @ORM\JoinTable(name="submission_user")
     */
    private $team;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="original_submission", cascade={"remove"})
     */
    private $translations;

    /**
     * @ORM\Column(name="number", type="integer")
     * @Assert\NotBlank
     */
    private $number;

    /******************** BASIC INFORMATION ********************/

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=510)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="BestPracticeType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="BestPracticeRole")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $other_role;

    /**
     * @var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $institution;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $other_institution;

    /**
     * @var Stakeholder
     *
     * @ORM\ManyToOne(targetEntity="Stakeholder")
     * @ORM\JoinColumn(name="stakeholder_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $stakeholder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $other_stakeholder;

    /**
     * @var Entity
     *
     * @ORM\ManyToOne(targetEntity="BestPracticeEntity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $entity;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank
     */
    private $reference_number;

    /******************** TYPE OF BEST PRACTICE ********************/

    /**
     * @var TechnicalMatter
     *
     * @ORM\ManyToMany(targetEntity="TechnicalMatter", inversedBy="submissions")
     * @ORM\JoinTable(name="submission_technical_matter")
     * @Assert\NotBlank
     */
    private $technical_matter;

    /**
     * @var Intervention
     *
     * @ORM\ManyToMany(targetEntity="Intervention", inversedBy="submissions")
     * @ORM\JoinTable(name="submission_intervention")
     * @Assert\NotBlank
     */
    private $intervention;

    /******************** IDENTIFYING THE BEST PRACTICE ********************/

    /**
     * @var date
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $start_date;

    /**
     * @var date
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $end_date;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $country;

    /**
     * @var SubRegion
     *
     * @ORM\ManyToOne(targetEntity="SubRegion")
     * @ORM\JoinColumn(name="subregion_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $subregion;

    /**
     * @var Target
     *
     * @ORM\ManyToMany(targetEntity="Target", inversedBy="submissions")
     * @ORM\JoinTable(name="submission_target")
     * @Assert\NotBlank
     */
    private $target;

    /**
     * @var PopulationGroup
     *
     * @ORM\ManyToOne(targetEntity="PopulationGroup")
     * @ORM\JoinColumn(name="population_group_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $population_group;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $other_population_group;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $introduction;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $objectives;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $activities;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $main_results;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $factors;

    /******************** EFFECTIVENESS AND EFFICIENCY ********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $resources_assigned;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $outcome_information;

    /******************** ADAPTABILITY AND REPLICABILITY ********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $scalability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $adaptability_replicability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $other_contexts_demo;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $describe_how;

    /******************** RELEVANCE FOR PAHO'S TECHNICAL COOPERATION ********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $health_system_contribution;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $value_chain_organization;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $public_health_issue;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $planning_information;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $relevance_information;

    /******************** RECOGNITION OF PAHO'S TC IMPORTANCE BY THE COUNTERPART ********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $counterpart_recognized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $catalytic_role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $neutral_role;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $recognition_information;

    /******************** ENGAGEMENT WITH THE PRIORITIES ORGANIZATION'S CROSS-CUTTING THEMES ********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $cross_cutting_approach;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $engagement_information;

    /******************** ILLUSTRATING THE BEST PRACTICE ********************/

    /**
     * @var SubmissionUpload
     * @ORM\OneToMany(targetEntity="SubmissionUpload", mappedBy="submission", cascade={"persist"})
     * @ORM\JoinTable(name="submission_upload")
     */
    private $attachments;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $products_information;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $other_sources_information;

    /******************** CONCLUSION - FINAL COMMENTS ********************/

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $challenges_information;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $lessons_information;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $paho_comments;


    public function __construct() {

        $this->attachments = new ArrayCollection();

        // call Grandpa's constructor
        parent::__construct();
    }

    public function __clone() {

        $this->setCreated(new \Datetime());
        $this->setUpdated(new \Datetime());
        $this->setIsSended(false);

        foreach(array('attachments') as $attribute) {

            $mClone = new ArrayCollection();
            foreach ($this->$attribute as $item) {
                $itemClone = clone $item;
                $itemClone->setSubmission($this);
                $mClone->add($itemClone);
            }

            $this->$attribute = $mClone;
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set protocol
     *
     * @param \Proethos2\ModelBundle\Entity\Protocol $protocol
     *
     * @return Submission
     */
    public function setProtocol(\Proethos2\ModelBundle\Entity\Protocol $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return \Proethos2\ModelBundle\Entity\Protocol
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set owner
     *
     * @param \Proethos2\ModelBundle\Entity\User $owner
     *
     * @return Submission
     */
    public function setOwner(\Proethos2\ModelBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Proethos2\ModelBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add team
     *
     * @param \Proethos2\ModelBundle\Entity\User $team
     *
     * @return Submission
     */
    public function addTeam(\Proethos2\ModelBundle\Entity\User $team)
    {
        $this->team[] = $team;

        return $this;
    }

    /**
     * Remove team
     *
     * @param \Proethos2\ModelBundle\Entity\User $team
     */
    public function removeTeam(\Proethos2\ModelBundle\Entity\User $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get team
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Get total team
     *
     * @return int
     */
    public function getTotalTeam()
    {
        return count($this->team) + 1;
    }

    public function isOwner(User $user)
    {
        if($this->getTeam()->contains($user)) {
            return true;
        }

        if($user == $this->getOwner()) {
            return true;
        }

        return false;
    }

    /**
     * Add attachment
     *
     * @param \Proethos2\ModelBundle\Entity\SubmissionUpload $attachment
     *
     * @return Submission
     */
    public function addAttachment(\Proethos2\ModelBundle\Entity\SubmissionUpload $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \Proethos2\ModelBundle\Entity\SubmissionUpload $attachment
     */
    public function removeAttachment(\Proethos2\ModelBundle\Entity\SubmissionUpload $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set isSended
     *
     * @param boolean $isSended
     *
     * @return Submission
     */
    public function setIsSended($isSended)
    {
        $this->is_sent = $isSended;

        return $this;
    }

    /**
     * Get isSended
     *
     * @return boolean
     */
    public function getIsSended()
    {
        return $this->is_sent;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Submission
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set isSent
     *
     * @param boolean $isSent
     *
     * @return Submission
     */
    public function setIsSent($isSent)
    {
        $this->is_sent = $isSent;

        return $this;
    }

    /**
     * Get isSent
     *
     * @return boolean
     */
    public function getIsSent()
    {
        return $this->is_sent;
    }

    /**
     * Set isTranslation
     *
     * @param boolean $isTranslation
     *
     * @return Submission
     */
    public function setIsTranslation($isTranslation)
    {
        $this->is_translation = $isTranslation;

        return $this;
    }

    /**
     * Get isTranslation
     *
     * @return boolean
     */
    public function getIsTranslation()
    {
        return $this->is_translation;
    }

    /**
     * Set originalSubmission
     *
     * @param \Proethos2\ModelBundle\Entity\Submission $originalSubmission
     *
     * @return Submission
     */
    public function setOriginalSubmission(\Proethos2\ModelBundle\Entity\Submission $originalSubmission = null)
    {
        $this->original_submission = $originalSubmission;

        return $this;
    }

    /**
     * Get originalSubmission
     *
     * @return \Proethos2\ModelBundle\Entity\Submission
     */
    public function getOriginalSubmission()
    {
        return $this->original_submission;
    }

    /**
     * can be edited?
     *
     * @return boolean
     */
    public function getCanBeEdited()
    {
        if(in_array($this->getProtocol()->getStatus(), array('D', 'R', 'C'))) {
            return true;
        } else {
            if($this->getProtocol()->getIsMigrated()) {
                return true;
            }
            return false;
        }
    }

    /**
     * Add translation
     *
     * @param \Proethos2\ModelBundle\Entity\Submission $translation
     *
     * @return Submission
     */
    public function addTranslation(\Proethos2\ModelBundle\Entity\Submission $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Proethos2\ModelBundle\Entity\Submission $translation
     */
    public function removeTranslation(\Proethos2\ModelBundle\Entity\Submission $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Get total translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTotalTranslations()
    {
        return count($this->getTranslations());
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Submission
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set otherRole
     *
     * @param string $otherRole
     *
     * @return Submission
     */
    public function setOtherRole($otherRole)
    {
        $this->other_role = $otherRole;

        return $this;
    }

    /**
     * Get otherRole
     *
     * @return string
     */
    public function getOtherRole()
    {
        return $this->other_role;
    }

    /**
     * Set otherInstitution
     *
     * @param string $otherInstitution
     *
     * @return Submission
     */
    public function setOtherInstitution($otherInstitution)
    {
        $this->other_institution = $otherInstitution;

        return $this;
    }

    /**
     * Get otherInstitution
     *
     * @return string
     */
    public function getOtherInstitution()
    {
        return $this->other_institution;
    }

    /**
     * Set referenceNumber
     *
     * @param string $referenceNumber
     *
     * @return Submission
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->reference_number = $referenceNumber;

        return $this;
    }

    /**
     * Get referenceNumber
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->reference_number;
    }

    /**
     * Set otherStakeholders
     *
     * @param string $otherStakeholders
     *
     * @return Submission
     */
    public function setOtherStakeholders($otherStakeholders)
    {
        $this->other_stakeholders = $otherStakeholders;

        return $this;
    }

    /**
     * Get otherStakeholders
     *
     * @return string
     */
    public function getOtherStakeholders()
    {
        return $this->other_stakeholders;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Submission
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Submission
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set objectives
     *
     * @param string $objectives
     *
     * @return Submission
     */
    public function setObjectives($objectives)
    {
        $this->objectives = $objectives;

        return $this;
    }

    /**
     * Get objectives
     *
     * @return string
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * Set activities
     *
     * @param string $activities
     *
     * @return Submission
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;

        return $this;
    }

    /**
     * Get activities
     *
     * @return string
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set mainResults
     *
     * @param string $mainResults
     *
     * @return Submission
     */
    public function setMainResults($mainResults)
    {
        $this->main_results = $mainResults;

        return $this;
    }

    /**
     * Get mainResults
     *
     * @return string
     */
    public function getMainResults()
    {
        return $this->main_results;
    }

    /**
     * Set factors
     *
     * @param string $factors
     *
     * @return Submission
     */
    public function setFactors($factors)
    {
        $this->factors = $factors;

        return $this;
    }

    /**
     * Get factors
     *
     * @return string
     */
    public function getFactors()
    {
        return $this->factors;
    }

    /**
     * Set resourcesAssigned
     *
     * @param string $resourcesAssigned
     *
     * @return Submission
     */
    public function setResourcesAssigned($resourcesAssigned)
    {
        $this->resources_assigned = $resourcesAssigned;

        return $this;
    }

    /**
     * Get resourcesAssigned
     *
     * @return string
     */
    public function getResourcesAssigned()
    {
        return $this->resources_assigned;
    }

    /**
     * Set outcomeInformation
     *
     * @param string $outcomeInformation
     *
     * @return Submission
     */
    public function setOutcomeInformation($outcomeInformation)
    {
        $this->outcome_information = $outcomeInformation;

        return $this;
    }

    /**
     * Get outcomeInformation
     *
     * @return string
     */
    public function getOutcomeInformation()
    {
        return $this->outcome_information;
    }

    /**
     * Set scalability
     *
     * @param string $scalability
     *
     * @return Submission
     */
    public function setScalability($scalability)
    {
        $this->scalability = $scalability;

        return $this;
    }

    /**
     * Get scalability
     *
     * @return string
     */
    public function getScalability()
    {
        return $this->scalability;
    }

    /**
     * Set adaptabilityReplicability
     *
     * @param string $adaptabilityReplicability
     *
     * @return Submission
     */
    public function setAdaptabilityReplicability($adaptabilityReplicability)
    {
        $this->adaptability_replicability = $adaptabilityReplicability;

        return $this;
    }

    /**
     * Get adaptabilityReplicability
     *
     * @return string
     */
    public function getAdaptabilityReplicability()
    {
        return $this->adaptability_replicability;
    }

    /**
     * Set otherContextsDemo
     *
     * @param string $otherContextsDemo
     *
     * @return Submission
     */
    public function setOtherContextsDemo($otherContextsDemo)
    {
        $this->other_contexts_demo = $otherContextsDemo;

        return $this;
    }

    /**
     * Get otherContextsDemo
     *
     * @return string
     */
    public function getOtherContextsDemo()
    {
        return $this->other_contexts_demo;
    }

    /**
     * Set describeHow
     *
     * @param string $describeHow
     *
     * @return Submission
     */
    public function setDescribeHow($describeHow)
    {
        $this->describe_how = $describeHow;

        return $this;
    }

    /**
     * Get describeHow
     *
     * @return string
     */
    public function getDescribeHow()
    {
        return $this->describe_how;
    }

    /**
     * Set healthSystemContribution
     *
     * @param string $healthSystemContribution
     *
     * @return Submission
     */
    public function setHealthSystemContribution($healthSystemContribution)
    {
        $this->health_system_contribution = $healthSystemContribution;

        return $this;
    }

    /**
     * Get healthSystemContribution
     *
     * @return string
     */
    public function getHealthSystemContribution()
    {
        return $this->health_system_contribution;
    }

    /**
     * Set valueChainOrganization
     *
     * @param string $valueChainOrganization
     *
     * @return Submission
     */
    public function setValueChainOrganization($valueChainOrganization)
    {
        $this->value_chain_organization = $valueChainOrganization;

        return $this;
    }

    /**
     * Get valueChainOrganization
     *
     * @return string
     */
    public function getValueChainOrganization()
    {
        return $this->value_chain_organization;
    }

    /**
     * Set publicHealthIssue
     *
     * @param string $publicHealthIssue
     *
     * @return Submission
     */
    public function setPublicHealthIssue($publicHealthIssue)
    {
        $this->public_health_issue = $publicHealthIssue;

        return $this;
    }

    /**
     * Get publicHealthIssue
     *
     * @return string
     */
    public function getPublicHealthIssue()
    {
        return $this->public_health_issue;
    }

    /**
     * Set planningInformation
     *
     * @param string $planningInformation
     *
     * @return Submission
     */
    public function setPlanningInformation($planningInformation)
    {
        $this->planning_information = $planningInformation;

        return $this;
    }

    /**
     * Get planningInformation
     *
     * @return string
     */
    public function getPlanningInformation()
    {
        return $this->planning_information;
    }

    /**
     * Set relevanceInformation
     *
     * @param string $relevanceInformation
     *
     * @return Submission
     */
    public function setRelevanceInformation($relevanceInformation)
    {
        $this->relevance_information = $relevanceInformation;

        return $this;
    }

    /**
     * Get relevanceInformation
     *
     * @return string
     */
    public function getRelevanceInformation()
    {
        return $this->relevance_information;
    }

    /**
     * Set counterpartRecognized
     *
     * @param string $counterpartRecognized
     *
     * @return Submission
     */
    public function setCounterpartRecognized($counterpartRecognized)
    {
        $this->counterpart_recognized = $counterpartRecognized;

        return $this;
    }

    /**
     * Get counterpartRecognized
     *
     * @return string
     */
    public function getCounterpartRecognized()
    {
        return $this->counterpart_recognized;
    }

    /**
     * Set catalyticRole
     *
     * @param string $catalyticRole
     *
     * @return Submission
     */
    public function setCatalyticRole($catalyticRole)
    {
        $this->catalytic_role = $catalyticRole;

        return $this;
    }

    /**
     * Get catalyticRole
     *
     * @return string
     */
    public function getCatalyticRole()
    {
        return $this->catalytic_role;
    }

    /**
     * Set neutralRole
     *
     * @param string $neutralRole
     *
     * @return Submission
     */
    public function setNeutralRole($neutralRole)
    {
        $this->neutral_role = $neutralRole;

        return $this;
    }

    /**
     * Get neutralRole
     *
     * @return string
     */
    public function getNeutralRole()
    {
        return $this->neutral_role;
    }

    /**
     * Set recognitionInformation
     *
     * @param string $recognitionInformation
     *
     * @return Submission
     */
    public function setRecognitionInformation($recognitionInformation)
    {
        $this->recognition_information = $recognitionInformation;

        return $this;
    }

    /**
     * Get recognitionInformation
     *
     * @return string
     */
    public function getRecognitionInformation()
    {
        return $this->recognition_information;
    }

    /**
     * Set crossCuttingApproach
     *
     * @param string $crossCuttingApproach
     *
     * @return Submission
     */
    public function setCrossCuttingApproach($crossCuttingApproach)
    {
        $this->cross_cutting_approach = $crossCuttingApproach;

        return $this;
    }

    /**
     * Get crossCuttingApproach
     *
     * @return string
     */
    public function getCrossCuttingApproach()
    {
        return $this->cross_cutting_approach;
    }

    /**
     * Set engagementInformation
     *
     * @param string $engagementInformation
     *
     * @return Submission
     */
    public function setEngagementInformation($engagementInformation)
    {
        $this->engagement_information = $engagementInformation;

        return $this;
    }

    /**
     * Get engagementInformation
     *
     * @return string
     */
    public function getEngagementInformation()
    {
        return $this->engagement_information;
    }

    /**
     * Set productsInformation
     *
     * @param string $productsInformation
     *
     * @return Submission
     */
    public function setProductsInformation($productsInformation)
    {
        $this->products_information = $productsInformation;

        return $this;
    }

    /**
     * Get productsInformation
     *
     * @return string
     */
    public function getProductsInformation()
    {
        return $this->products_information;
    }

    /**
     * Get productsInformationLinks
     *
     * @return array
     */
    public function getProductsInformationLinks()
    {
        $links = array();
        $_links = explode("\r\n", $this->products_information);

        foreach ($_links as $link) {
            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * Set otherSourcesInformation
     *
     * @param string $otherSourcesInformation
     *
     * @return Submission
     */
    public function setOtherSourcesInformation($otherSourcesInformation)
    {
        $this->other_sources_information = $otherSourcesInformation;

        return $this;
    }

    /**
     * Get otherSourcesInformation
     *
     * @return string
     */
    public function getOtherSourcesInformation()
    {
        return $this->other_sources_information;
    }

    /**
     * Get getOtherSourcesInformationLinks
     *
     * @return array
     */
    public function getOtherSourcesInformationLinks()
    {
        $links = array();
        $_links = explode("\r\n", $this->other_sources_information);

        foreach ($_links as $link) {
            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * Set challengesInformation
     *
     * @param string $challengesInformation
     *
     * @return Submission
     */
    public function setChallengesInformation($challengesInformation)
    {
        $this->challenges_information = $challengesInformation;

        return $this;
    }

    /**
     * Get challengesInformation
     *
     * @return string
     */
    public function getChallengesInformation()
    {
        return $this->challenges_information;
    }

    /**
     * Set lessonsInformation
     *
     * @param string $lessonsInformation
     *
     * @return Submission
     */
    public function setLessonsInformation($lessonsInformation)
    {
        $this->lessons_information = $lessonsInformation;

        return $this;
    }

    /**
     * Get lessonsInformation
     *
     * @return string
     */
    public function getLessonsInformation()
    {
        return $this->lessons_information;
    }

    /**
     * Set pahoComments
     *
     * @param string $pahoComments
     *
     * @return Submission
     */
    public function setPahoComments($pahoComments)
    {
        $this->paho_comments = $pahoComments;

        return $this;
    }

    /**
     * Get pahoComments
     *
     * @return string
     */
    public function getPahoComments()
    {
        return $this->paho_comments;
    }

    /**
     * Set type
     *
     * @param \Proethos2\ModelBundle\Entity\BestPracticeType $type
     *
     * @return Submission
     */
    public function setType(\Proethos2\ModelBundle\Entity\BestPracticeType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Proethos2\ModelBundle\Entity\BestPracticeType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set role
     *
     * @param \Proethos2\ModelBundle\Entity\BestPracticeRole $role
     *
     * @return Submission
     */
    public function setRole(\Proethos2\ModelBundle\Entity\BestPracticeRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Proethos2\ModelBundle\Entity\BestPracticeRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set institution
     *
     * @param \Proethos2\ModelBundle\Entity\Institution $institution
     *
     * @return Submission
     */
    public function setInstitution(\Proethos2\ModelBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return \Proethos2\ModelBundle\Entity\Institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set stakeholder
     *
     * @param \Proethos2\ModelBundle\Entity\Stakeholder $stakeholder
     *
     * @return Submission
     */
    public function setStakeholder(\Proethos2\ModelBundle\Entity\Stakeholder $stakeholder = null)
    {
        $this->stakeholder = $stakeholder;

        return $this;
    }

    /**
     * Get stakeholder
     *
     * @return \Proethos2\ModelBundle\Entity\Stakeholder
     */
    public function getStakeholder()
    {
        return $this->stakeholder;
    }


    /**
     * Set subregion
     *
     * @param \Proethos2\ModelBundle\Entity\SubRegion $subregion
     *
     * @return Submission
     */
    public function setSubregion(\Proethos2\ModelBundle\Entity\SubRegion $subregion = null)
    {
        $this->subregion = $subregion;

        return $this;
    }

    /**
     * Get subregion
     *
     * @return \Proethos2\ModelBundle\Entity\SubRegion
     */
    public function getSubregion()
    {
        return $this->subregion;
    }

    /**
     * Set populationGroup
     *
     * @param \Proethos2\ModelBundle\Entity\PopulationGroup $populationGroup
     *
     * @return Submission
     */
    public function setPopulationGroup(\Proethos2\ModelBundle\Entity\PopulationGroup $populationGroup = null)
    {
        $this->population_group = $populationGroup;

        return $this;
    }

    /**
     * Get populationGroup
     *
     * @return \Proethos2\ModelBundle\Entity\PopulationGroup
     */
    public function getPopulationGroup()
    {
        return $this->population_group;
    }

    /**
     * Set otherStakeholder
     *
     * @param string $otherStakeholder
     *
     * @return Submission
     */
    public function setOtherStakeholder($otherStakeholder)
    {
        $this->other_stakeholder = $otherStakeholder;

        return $this;
    }

    /**
     * Get otherStakeholder
     *
     * @return string
     */
    public function getOtherStakeholder()
    {
        return $this->other_stakeholder;
    }

    /**
     * Add intervention
     *
     * @param \Proethos2\ModelBundle\Entity\Intervention $intervention
     *
     * @return Submission
     */
    public function addIntervention(\Proethos2\ModelBundle\Entity\Intervention $intervention)
    {
        $this->intervention[] = $intervention;

        return $this;
    }

    /**
     * Remove intervention
     *
     * @param \Proethos2\ModelBundle\Entity\Intervention $intervention
     */
    public function removeIntervention(\Proethos2\ModelBundle\Entity\Intervention $intervention)
    {
        $this->intervention->removeElement($intervention);
    }

    /**
     * Get intervention
     *
     * @return \Proethos2\ModelBundle\Entity\Intervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    public function getInterventionList() {
        $interventions = array();
        
        if ( $this->intervention ) {
            foreach($this->intervention as $intervention) {
                $interventions[] = $intervention->getName();
            }
        }

        return $interventions;
    }

    /**
     * Set entity
     *
     * @param \Proethos2\ModelBundle\Entity\BestPracticeEntity $entity
     *
     * @return Submission
     */
    public function setEntity(\Proethos2\ModelBundle\Entity\BestPracticeEntity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Proethos2\ModelBundle\Entity\BestPracticeEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set country
     *
     * @param \Proethos2\ModelBundle\Entity\Country $country
     *
     * @return Submission
     */
    public function setCountry(\Proethos2\ModelBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Proethos2\ModelBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set introduction
     *
     * @param string $introduction
     *
     * @return Submission
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Add technicalMatter
     *
     * @param \Proethos2\ModelBundle\Entity\TechnicalMatter $technicalMatter
     *
     * @return Submission
     */
    public function addTechnicalMatter(\Proethos2\ModelBundle\Entity\TechnicalMatter $technicalMatter)
    {
        $this->technical_matter[] = $technicalMatter;

        return $this;
    }

    /**
     * Remove technicalMatter
     *
     * @param \Proethos2\ModelBundle\Entity\TechnicalMatter $technicalMatter
     */
    public function removeTechnicalMatter(\Proethos2\ModelBundle\Entity\TechnicalMatter $technicalMatter)
    {
        $this->technical_matter->removeElement($technicalMatter);
    }

    /**
     * Get technicalMatter
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTechnicalMatter()
    {
        return $this->technical_matter;
    }

    public function getTechnicalMatterList() {
        $technical_matters = array();
        
        if ( $this->technical_matter ) {
            foreach($this->technical_matter as $technical_matter) {
                $technical_matters[] = $technical_matter->getName();
            }
        }

        return $technical_matters;
    }

    /**
     * Set otherPopulationGroup
     *
     * @param string $otherPopulationGroup
     *
     * @return Submission
     */
    public function setOtherPopulationGroup($otherPopulationGroup)
    {
        $this->other_population_group = $otherPopulationGroup;

        return $this;
    }

    /**
     * Get otherPopulationGroup
     *
     * @return string
     */
    public function getOtherPopulationGroup()
    {
        return $this->other_population_group;
    }

    /**
     * Add target
     *
     * @param \Proethos2\ModelBundle\Entity\Target $target
     *
     * @return Submission
     */
    public function addTarget(\Proethos2\ModelBundle\Entity\Target $target)
    {
        $this->target[] = $target;

        return $this;
    }

    /**
     * Remove target
     *
     * @param \Proethos2\ModelBundle\Entity\Target $target
     */
    public function removeTarget(\Proethos2\ModelBundle\Entity\Target $target)
    {
        $this->target->removeElement($target);
    }

    /**
     * Get target
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarget()
    {
        return $this->target;
    }

    public function getTargetList() {
        $targets = array();
        
        if ( $this->target ) {
            foreach($this->target as $target) {
                $targets[] = $target->getName();
            }
        }

        return $targets;
    }
}
