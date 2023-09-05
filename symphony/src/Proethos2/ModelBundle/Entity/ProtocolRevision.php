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

/**
 * @ORM\Table(name="protocol_revision")
 * @ORM\Entity
 */
class ProtocolRevision extends Base
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="members") 
     */ 
    private $member;

    /**
     * @ORM\Column(type="boolean")
     */
    private $answered = false;

    /**
     * @ORM\ManyToOne(targetEntity="Protocol", inversedBy="revision")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $protocol;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_final_revision = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $social_value;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sscientific_validity;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fair_participant_selection;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $favorable_balance;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informed_consent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $respect_for_participants;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $other_comments;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $suggestions;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     * @Assert\NotBlank
     */
    private $final_decision;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $effectiveness_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $effectiveness_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $effectiveness_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $cost_effectiveness_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $cost_effectiveness_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cost_effectiveness_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $efficiency_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $efficiency_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $efficiency_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $sustainability_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $sustainability_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sustainability_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $replicability_adaptability_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $replicability_adaptability_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $replicability_adaptability_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $innovation_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $innovation_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $innovation_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $participation_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $participation_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $participation_revisions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $cross_cutting_themes_scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $cross_cutting_themes_feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cross_cutting_themes_revisions;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank
     */
    private $average_score;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank
     */
    private $core_average_score;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank
     */
    private $technical_average_score;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $zero_scores;

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
     * Set answered
     *
     * @param boolean $answered
     *
     * @return ProtocolRevision
     */
    public function setAnswered($answered)
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * Get answered
     *
     * @return boolean
     */
    public function getAnswered()
    {
        return $this->answered;
    }

    /**
     * Set member
     *
     * @param \Proethos2\ModelBundle\Entity\User $member
     *
     * @return ProtocolRevision
     */
    public function setMember(\Proethos2\ModelBundle\Entity\User $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \Proethos2\ModelBundle\Entity\User
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set protocol
     *
     * @param \Proethos2\ModelBundle\Entity\Protocol $protocol
     *
     * @return ProtocolRevision
     */
    public function setProtocol(\Proethos2\ModelBundle\Entity\Protocol $protocol = null)
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
     * Set isFinalRevision
     *
     * @param boolean $isFinalRevision
     *
     * @return ProtocolRevision
     */
    public function setIsFinalRevision($isFinalRevision)
    {
        $this->is_final_revision = $isFinalRevision;

        return $this;
    }

    /**
     * Get isFinalRevision
     *
     * @return boolean
     */
    public function getIsFinalRevision()
    {
        return $this->is_final_revision;
    }

    /**
     * Set socialValue
     *
     * @param string $socialValue
     *
     * @return ProtocolRevision
     */
    public function setSocialValue($socialValue)
    {
        $this->social_value = $socialValue;

        return $this;
    }

    /**
     * Get socialValue
     *
     * @return string
     */
    public function getSocialValue()
    {
        return $this->social_value;
    }

    /**
     * Set sscientificValidity
     *
     * @param string $sscientificValidity
     *
     * @return ProtocolRevision
     */
    public function setSscientificValidity($sscientificValidity)
    {
        $this->sscientific_validity = $sscientificValidity;

        return $this;
    }

    /**
     * Get sscientificValidity
     *
     * @return string
     */
    public function getSscientificValidity()
    {
        return $this->sscientific_validity;
    }

    /**
     * Set fairParticipantSelection
     *
     * @param string $fairParticipantSelection
     *
     * @return ProtocolRevision
     */
    public function setFairParticipantSelection($fairParticipantSelection)
    {
        $this->fair_participant_selection = $fairParticipantSelection;

        return $this;
    }

    /**
     * Get fairParticipantSelection
     *
     * @return string
     */
    public function getFairParticipantSelection()
    {
        return $this->fair_participant_selection;
    }

    /**
     * Set favorableBalance
     *
     * @param string $favorableBalance
     *
     * @return ProtocolRevision
     */
    public function setFavorableBalance($favorableBalance)
    {
        $this->favorable_balance = $favorableBalance;

        return $this;
    }

    /**
     * Get favorableBalance
     *
     * @return string
     */
    public function getFavorableBalance()
    {
        return $this->favorable_balance;
    }

    /**
     * Set informedConsent
     *
     * @param string $informedConsent
     *
     * @return ProtocolRevision
     */
    public function setInformedConsent($informedConsent)
    {
        $this->informed_consent = $informedConsent;

        return $this;
    }

    /**
     * Get informedConsent
     *
     * @return string
     */
    public function getInformedConsent()
    {
        return $this->informed_consent;
    }

    /**
     * Set respectForParticipants
     *
     * @param string $respectForParticipants
     *
     * @return ProtocolRevision
     */
    public function setRespectForParticipants($respectForParticipants)
    {
        $this->respect_for_participants = $respectForParticipants;

        return $this;
    }

    /**
     * Get respectForParticipants
     *
     * @return string
     */
    public function getRespectForParticipants()
    {
        return $this->respect_for_participants;
    }

    /**
     * Set otherComments
     *
     * @param string $otherComments
     *
     * @return ProtocolRevision
     */
    public function setOtherComments($otherComments)
    {
        $this->other_comments = $otherComments;

        return $this;
    }

    /**
     * Get otherComments
     *
     * @return string
     */
    public function getOtherComments()
    {
        return $this->other_comments;
    }

    /**
     * Set suggestions
     *
     * @param string $suggestions
     *
     * @return ProtocolRevision
     */
    public function setSuggestions($suggestions)
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    /**
     * Get suggestions
     *
     * @return string
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * Set finalDecision
     *
     * @param string $finalDecision
     *
     * @return ProtocolRevision
     */
    public function setFinalDecision($finalDecision)
    {
        $this->final_decision = $finalDecision;

        return $this;
    }

    /**
     * Get finalDecision
     *
     * @return string
     */
    public function getFinalDecision()
    {
        return $this->final_decision;
    }

    public function getDecisionLabel()
    {
        switch ($this->final_decision) {
            case 'D': return "Draft"; break;
            case 'S': return "Submitted"; break;
            case 'I': return "Waiting for initial evaluation"; break;
            case 'E': return "Waiting for Technical Assessment"; break;
            case 'H': return "Scheduled"; break;
            case 'F': return "Exempted"; break;
            case 'A': return "Approved"; break;
            case 'N': return "Rejected"; break;
            case 'C': return "Revisions required"; break;
            case 'X': return "Expedite Approval"; break;
            case 'T': return "Withdrawn"; break;
        }
        return $this->final_decision;
    }

    /**
     * Set effectivenessScoring
     *
     * @param integer $effectivenessScoring
     *
     * @return ProtocolRevision
     */
    public function setEffectivenessScoring($effectivenessScoring)
    {
        $this->effectiveness_scoring = $effectivenessScoring;

        return $this;
    }

    /**
     * Get effectivenessScoring
     *
     * @return integer
     */
    public function getEffectivenessScoring()
    {
        return $this->effectiveness_scoring;
    }

    /**
     * Set effectivenessFeedback
     *
     * @param string $effectivenessFeedback
     *
     * @return ProtocolRevision
     */
    public function setEffectivenessFeedback($effectivenessFeedback)
    {
        $this->effectiveness_feedback = $effectivenessFeedback;

        return $this;
    }

    /**
     * Get effectivenessFeedback
     *
     * @return string
     */
    public function getEffectivenessFeedback()
    {
        return $this->effectiveness_feedback;
    }

    /**
     * Set effectivenessRevisions
     *
     * @param string $effectivenessRevisions
     *
     * @return ProtocolRevision
     */
    public function setEffectivenessRevisions($effectivenessRevisions)
    {
        $this->effectiveness_revisions = $effectivenessRevisions;

        return $this;
    }

    /**
     * Get effectivenessRevisions
     *
     * @return string
     */
    public function getEffectivenessRevisions()
    {
        return $this->effectiveness_revisions;
    }

    /**
     * Set costEffectivenessScoring
     *
     * @param integer $costEffectivenessScoring
     *
     * @return ProtocolRevision
     */
    public function setCostEffectivenessScoring($costEffectivenessScoring)
    {
        $this->cost_effectiveness_scoring = $costEffectivenessScoring;

        return $this;
    }

    /**
     * Get costEffectivenessScoring
     *
     * @return integer
     */
    public function getCostEffectivenessScoring()
    {
        return $this->cost_effectiveness_scoring;
    }

    /**
     * Set costEffectivenessFeedback
     *
     * @param string $costEffectivenessFeedback
     *
     * @return ProtocolRevision
     */
    public function setCostEffectivenessFeedback($costEffectivenessFeedback)
    {
        $this->cost_effectiveness_feedback = $costEffectivenessFeedback;

        return $this;
    }

    /**
     * Get costEffectivenessFeedback
     *
     * @return string
     */
    public function getCostEffectivenessFeedback()
    {
        return $this->cost_effectiveness_feedback;
    }

    /**
     * Set costEffectivenessRevisions
     *
     * @param string $costEffectivenessRevisions
     *
     * @return ProtocolRevision
     */
    public function setCostEffectivenessRevisions($costEffectivenessRevisions)
    {
        $this->cost_effectiveness_revisions = $costEffectivenessRevisions;

        return $this;
    }

    /**
     * Get costEffectivenessRevisions
     *
     * @return string
     */
    public function getCostEffectivenessRevisions()
    {
        return $this->cost_effectiveness_revisions;
    }

    /**
     * Set efficiencyScoring
     *
     * @param integer $efficiencyScoring
     *
     * @return ProtocolRevision
     */
    public function setEfficiencyScoring($efficiencyScoring)
    {
        $this->efficiency_scoring = $efficiencyScoring;

        return $this;
    }

    /**
     * Get efficiencyScoring
     *
     * @return integer
     */
    public function getEfficiencyScoring()
    {
        return $this->efficiency_scoring;
    }

    /**
     * Set efficiencyFeedback
     *
     * @param string $efficiencyFeedback
     *
     * @return ProtocolRevision
     */
    public function setEfficiencyFeedback($efficiencyFeedback)
    {
        $this->efficiency_feedback = $efficiencyFeedback;

        return $this;
    }

    /**
     * Get efficiencyFeedback
     *
     * @return string
     */
    public function getEfficiencyFeedback()
    {
        return $this->efficiency_feedback;
    }

    /**
     * Set efficiencyRevisions
     *
     * @param string $efficiencyRevisions
     *
     * @return ProtocolRevision
     */
    public function setEfficiencyRevisions($efficiencyRevisions)
    {
        $this->efficiency_revisions = $efficiencyRevisions;

        return $this;
    }

    /**
     * Get efficiencyRevisions
     *
     * @return string
     */
    public function getEfficiencyRevisions()
    {
        return $this->efficiency_revisions;
    }

    /**
     * Set sustainabilityScoring
     *
     * @param integer $sustainabilityScoring
     *
     * @return ProtocolRevision
     */
    public function setSustainabilityScoring($sustainabilityScoring)
    {
        $this->sustainability_scoring = $sustainabilityScoring;

        return $this;
    }

    /**
     * Get sustainabilityScoring
     *
     * @return integer
     */
    public function getSustainabilityScoring()
    {
        return $this->sustainability_scoring;
    }

    /**
     * Set sustainabilityFeedback
     *
     * @param string $sustainabilityFeedback
     *
     * @return ProtocolRevision
     */
    public function setSustainabilityFeedback($sustainabilityFeedback)
    {
        $this->sustainability_feedback = $sustainabilityFeedback;

        return $this;
    }

    /**
     * Get sustainabilityFeedback
     *
     * @return string
     */
    public function getSustainabilityFeedback()
    {
        return $this->sustainability_feedback;
    }

    /**
     * Set sustainabilityRevisions
     *
     * @param string $sustainabilityRevisions
     *
     * @return ProtocolRevision
     */
    public function setSustainabilityRevisions($sustainabilityRevisions)
    {
        $this->sustainability_revisions = $sustainabilityRevisions;

        return $this;
    }

    /**
     * Get sustainabilityRevisions
     *
     * @return string
     */
    public function getSustainabilityRevisions()
    {
        return $this->sustainability_revisions;
    }

    /**
     * Set replicabilityAdaptabilityScoring
     *
     * @param integer $replicabilityAdaptabilityScoring
     *
     * @return ProtocolRevision
     */
    public function setReplicabilityAdaptabilityScoring($replicabilityAdaptabilityScoring)
    {
        $this->replicability_adaptability_scoring = $replicabilityAdaptabilityScoring;

        return $this;
    }

    /**
     * Get replicabilityAdaptabilityScoring
     *
     * @return integer
     */
    public function getReplicabilityAdaptabilityScoring()
    {
        return $this->replicability_adaptability_scoring;
    }

    /**
     * Set replicabilityAdaptabilityFeedback
     *
     * @param string $replicabilityAdaptabilityFeedback
     *
     * @return ProtocolRevision
     */
    public function setReplicabilityAdaptabilityFeedback($replicabilityAdaptabilityFeedback)
    {
        $this->replicability_adaptability_feedback = $replicabilityAdaptabilityFeedback;

        return $this;
    }

    /**
     * Get replicabilityAdaptabilityFeedback
     *
     * @return string
     */
    public function getReplicabilityAdaptabilityFeedback()
    {
        return $this->replicability_adaptability_feedback;
    }

    /**
     * Set replicabilityAdaptabilityRevisions
     *
     * @param string $replicabilityAdaptabilityRevisions
     *
     * @return ProtocolRevision
     */
    public function setReplicabilityAdaptabilityRevisions($replicabilityAdaptabilityRevisions)
    {
        $this->replicability_adaptability_revisions = $replicabilityAdaptabilityRevisions;

        return $this;
    }

    /**
     * Get replicabilityAdaptabilityRevisions
     *
     * @return string
     */
    public function getReplicabilityAdaptabilityRevisions()
    {
        return $this->replicability_adaptability_revisions;
    }

    /**
     * Set innovationScoring
     *
     * @param integer $innovationScoring
     *
     * @return ProtocolRevision
     */
    public function setInnovationScoring($innovationScoring)
    {
        $this->innovation_scoring = $innovationScoring;

        return $this;
    }

    /**
     * Get innovationScoring
     *
     * @return integer
     */
    public function getInnovationScoring()
    {
        return $this->innovation_scoring;
    }

    /**
     * Set innovationFeedback
     *
     * @param string $innovationFeedback
     *
     * @return ProtocolRevision
     */
    public function setInnovationFeedback($innovationFeedback)
    {
        $this->innovation_feedback = $innovationFeedback;

        return $this;
    }

    /**
     * Get innovationFeedback
     *
     * @return string
     */
    public function getInnovationFeedback()
    {
        return $this->innovation_feedback;
    }

    /**
     * Set innovationRevisions
     *
     * @param string $innovationRevisions
     *
     * @return ProtocolRevision
     */
    public function setInnovationRevisions($innovationRevisions)
    {
        $this->innovation_revisions = $innovationRevisions;

        return $this;
    }

    /**
     * Get innovationRevisions
     *
     * @return string
     */
    public function getInnovationRevisions()
    {
        return $this->innovation_revisions;
    }

    /**
     * Set participationScoring
     *
     * @param integer $participationScoring
     *
     * @return ProtocolRevision
     */
    public function setParticipationScoring($participationScoring)
    {
        $this->participation_scoring = $participationScoring;

        return $this;
    }

    /**
     * Get participationScoring
     *
     * @return integer
     */
    public function getParticipationScoring()
    {
        return $this->participation_scoring;
    }

    /**
     * Set participationFeedback
     *
     * @param string $participationFeedback
     *
     * @return ProtocolRevision
     */
    public function setParticipationFeedback($participationFeedback)
    {
        $this->participation_feedback = $participationFeedback;

        return $this;
    }

    /**
     * Get participationFeedback
     *
     * @return string
     */
    public function getParticipationFeedback()
    {
        return $this->participation_feedback;
    }

    /**
     * Set participationRevisions
     *
     * @param string $participationRevisions
     *
     * @return ProtocolRevision
     */
    public function setParticipationRevisions($participationRevisions)
    {
        $this->participation_revisions = $participationRevisions;

        return $this;
    }

    /**
     * Get participationRevisions
     *
     * @return string
     */
    public function getParticipationRevisions()
    {
        return $this->participation_revisions;
    }

    /**
     * Set crossCuttingThemesScoring
     *
     * @param integer $crossCuttingThemesScoring
     *
     * @return ProtocolRevision
     */
    public function setCrossCuttingThemesScoring($crossCuttingThemesScoring)
    {
        $this->cross_cutting_themes_scoring = $crossCuttingThemesScoring;

        return $this;
    }

    /**
     * Get crossCuttingThemesScoring
     *
     * @return integer
     */
    public function getCrossCuttingThemesScoring()
    {
        return $this->cross_cutting_themes_scoring;
    }

    /**
     * Set crossCuttingThemesFeedback
     *
     * @param string $crossCuttingThemesFeedback
     *
     * @return ProtocolRevision
     */
    public function setCrossCuttingThemesFeedback($crossCuttingThemesFeedback)
    {
        $this->cross_cutting_themes_feedback = $crossCuttingThemesFeedback;

        return $this;
    }

    /**
     * Get crossCuttingThemesFeedback
     *
     * @return string
     */
    public function getCrossCuttingThemesFeedback()
    {
        return $this->cross_cutting_themes_feedback;
    }

    /**
     * Set crossCuttingThemesRevisions
     *
     * @param string $crossCuttingThemesRevisions
     *
     * @return ProtocolRevision
     */
    public function setCrossCuttingThemesRevisions($crossCuttingThemesRevisions)
    {
        $this->cross_cutting_themes_revisions = $crossCuttingThemesRevisions;

        return $this;
    }

    /**
     * Get crossCuttingThemesRevisions
     *
     * @return string
     */
    public function getCrossCuttingThemesRevisions()
    {
        return $this->cross_cutting_themes_revisions;
    }

    /**
     * Set averageScore
     *
     * @param float $averageScore
     *
     * @return ProtocolRevision
     */
    public function setAverageScore($averageScore)
    {
        $this->average_score = $averageScore;

        return $this;
    }

    /**
     * Get averageScore
     *
     * @return float
     */
    public function getAverageScore()
    {
        return $this->average_score;
    }

    /**
     * Set zeroScores
     *
     * @param string $zeroScores
     *
     * @return ProtocolRevision
     */
    public function setZeroScores($zeroScores)
    {
        $this->zero_scores = $zeroScores;

        return $this;
    }

    /**
     * Get zeroScores
     *
     * @return string
     */
    public function getZeroScores()
    {
        return $this->zero_scores;
    }

    /**
     * Set coreAverageScore
     *
     * @param float $coreAverageScore
     *
     * @return ProtocolRevision
     */
    public function setCoreAverageScore($coreAverageScore)
    {
        $this->core_average_score = $coreAverageScore;

        return $this;
    }

    /**
     * Get coreAverageScore
     *
     * @return float
     */
    public function getCoreAverageScore()
    {
        return $this->core_average_score;
    }

    /**
     * Set technicalAverageScore
     *
     * @param float $technicalAverageScore
     *
     * @return ProtocolRevision
     */
    public function setTechnicalAverageScore($technicalAverageScore)
    {
        $this->technical_average_score = $technicalAverageScore;

        return $this;
    }

    /**
     * Get technicalAverageScore
     *
     * @return float
     */
    public function getTechnicalAverageScore()
    {
        return $this->technical_average_score;
    }
}
