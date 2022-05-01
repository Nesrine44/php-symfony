<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Innovation
 *
 * @ORM\Table(name="innovation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InnovationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Innovation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $old_id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="own_innovations")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    /**
     * @var datetime
     *
     * @ORM\Column(name="in_market_date", type="datetime", nullable=true)
     */
    private $in_market_date;

    /**
     * @var datetime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $start_date;

    /**
     * @ORM\ManyToOne(targetEntity="Stage")
     * @ORM\JoinColumn(name="stage_id", referencedColumnName="id")
     */
    private $stage;

    /**
     * @ORM\ManyToOne(targetEntity="Stage")
     * @ORM\JoinColumn(name="old_stage_id", referencedColumnName="id")
     */
    private $old_stage;

    /**
     * @var datetime
     *
     * @ORM\Column(name="old_stage_date", type="datetime", nullable=true)
     */
    private $old_stage_date;

    /**
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Classification")
     * @ORM\JoinColumn(name="classification_id", referencedColumnName="id")
     */
    private $classification;

    /**
     * @ORM\ManyToOne(targetEntity="ConsumerOpportunity")
     * @ORM\JoinColumn(name="consumer_opportunity_id", referencedColumnName="id")
     */
    private $consumer_opportunity;

    /**
     * @ORM\ManyToOne(targetEntity="MomentOfConsumption")
     * @ORM\JoinColumn(name="moment_of_consumption_id", referencedColumnName="id")
     */
    private $moment_of_consumption;

    /**
     * @ORM\ManyToOne(targetEntity="BusinessDriver")
     * @ORM\JoinColumn(name="business_driver_id", referencedColumnName="id")
     */
    private $business_driver;

    /**
     * @ORM\ManyToOne(targetEntity="PortfolioProfile")
     * @ORM\JoinColumn(name="portfolio_profile_id", referencedColumnName="id")
     */
    private $portfolio_profile;

    /**
     * @ORM\ManyToOne(targetEntity="Entity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('', 'Aniseed', 'Bitters', 'Brandy non Cognac', 'Cognac', 'Gin', 'Intl Champagne', 'Liqueurs', 'Non-Scotch Whisky', 'RTD/RTS', 'Rum', 'Scotch Whisky', 'Tequila', 'Vodka', 'Wine', 'Other')")
     */
    private $category;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_multi_brand", type="boolean")
     */
    private $is_multi_brand = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_replacing_existing_product", type="boolean")
     */
    private $is_replacing_existing_product = false;

    /**
     * @var string
     *
     * @ORM\Column(name="replacing_product", type="string", length=255, nullable=true)
     */
    private $replacing_product;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_in_prisma", type="boolean")
     */
    private $is_in_prisma = false;

    /**
     * @var string
     *
     * @ORM\Column(name="why_invest_in_this_innovation", type="text", length=65535, nullable=true)
     */
    private $why_invest_in_this_innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="unique_experience", type="text", length=65535, nullable=true)
     */
    private $unique_experience;

    /**
     * @var string
     *
     * @ORM\Column(name="story", type="text", length=65535, nullable=true)
     */
    private $story;

    /**
     * @var string
     *
     * @ORM\Column(name="uniqueness", type="text", length=65535, nullable=true)
     */
    private $uniqueness;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_insight", type="text", length=65535, nullable=true)
     */
    private $consumer_insight;

    /**
     * @var string
     *
     * @ORM\Column(name="early_adopter_persona", type="text", length=65535, nullable=true)
     */
    private $early_adopter_persona;

    /**
     * @var string
     *
     * @ORM\Column(name="source_of_business", type="text", length=65535, nullable=true)
     */
    private $source_of_business;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_1", type="string", length=255, nullable=true)
     */
    private $universal_key_information_1;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_2", type="string", length=255, nullable=true)
     */
    private $universal_key_information_2;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_3", type="string", length=255, nullable=true)
     */
    private $universal_key_information_3;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_3_vs", type="string", length=255, nullable=true)
     */
    private $universal_key_information_3_vs;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_4", type="string", length=255, nullable=true)
     */
    private $universal_key_information_4;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_4_vs", type="string", length=255, nullable=true)
     */
    private $universal_key_information_4_vs;

    /**
     * @var string
     *
     * @ORM\Column(name="universal_key_information_5", type="string", length=255, nullable=true)
     */
    private $universal_key_information_5;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="pot_picture_1_id", referencedColumnName="id")
     */
    private $pot_picture_1;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="pot_picture_2_id", referencedColumnName="id")
     */
    private $pot_picture_2;

    /**
     * @var string
     *
     * @ORM\Column(name="pot_legend_1", type="string", length=255, nullable=true)
     */
    private $pot_legend_1;

    /**
     * @var string
     *
     * @ORM\Column(name="pot_legend_2", type="string", length=255, nullable=true)
     */
    private $pot_legend_2;

    /**
     * @var string
     *
     * @ORM\Column(name="key_learning_so_far", type="text", length=65535, nullable=true)
     */
    private $key_learning_so_far;

    /**
     * @var string
     *
     * @ORM\Column(name="next_steps", type="text", length=65535, nullable=true)
     */
    private $next_steps;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('', 'fast_growth', 'slow_build')")
     */
    private $growth_model;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_earning_any_money_yet", type="boolean")
     */
    private $is_earning_any_money_yet = false;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_to_make_money", type="text", length=65535, nullable=true)
     */
    private $plan_to_make_money;

    /**
     * @var string
     *
     * @ORM\Column(name="markets", type="text", length=65535, nullable=true)
     */
    private $markets;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="beautyshot_picture_id", referencedColumnName="id")
     */
    private $beautyshot_picture;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="packshot_picture_id", referencedColumnName="id")
     */
    private $packshot_picture;

    /**
     * @ORM\ManyToOne(targetEntity="Picture")
     * @ORM\JoinColumn(name="financial_graph_picture_id", referencedColumnName="id")
     */
    private $financial_graph_picture;


    /**
     * @ORM\OneToMany(targetEntity="AdditionalPicture", mappedBy="innovation", cascade={"remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     *
     */
    protected $additional_pictures;

    /**
     * @var string
     *
     * @ORM\Column(name="video_url", type="string", length=255, nullable=true)
     */
    private $video_url;

    /**
     * @var string
     *
     * @ORM\Column(name="video_password", type="string", length=255, nullable=true)
     */
    private $video_password;

    /**
     * @var string
     *
     * @ORM\Column(name="ibp_url", type="string", length=255, nullable=true)
     */
    private $ibp_url;

    /**
     * @var string
     *
     * @ORM\Column(name="mybrands_url", type="string", length=255, nullable=true)
     */
    private $mybrands_url;

    /**
     * @var string
     *
     * @ORM\Column(name="press_url", type="string", length=255, nullable=true)
     */
    private $press_url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_needing_financial_update", type="boolean")
     */
    private $is_needing_financial_update = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $is_active = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_new_to_the_world", type="boolean")
     */
    private $is_new_to_the_world = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_frozen", type="boolean")
     */
    private $is_frozen = false;

    /**
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="FinancialData", mappedBy="innovation")
     */
    protected $financial_datas;

    /**
     * @ORM\OneToMany(targetEntity="PerformanceReview", mappedBy="innovation")
     */
    protected $performance_reviews;

    /**
     * @ORM\OneToMany(targetEntity="UserInnovationRight", cascade={"ALL"}, mappedBy="innovation")
     */
    protected $user_innovation_rights;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="innovation")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $activities;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="innovation_tag")
     */
    private $tags;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_score", type="integer", nullable=true)
     */
    private $sort_score;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="City", cascade={"persist"})
     * @ORM\JoinTable(name="innovation_city")
     */
    private $key_cities;



    /**
     * @var string
     * @ORM\Column(name="new_business_opportunity", type="string", length=255, nullable=true)
     */
    private $new_business_opportunity;

    /**
     * @var string
     * @ORM\Column(name="investment_model", type="string", length=255, nullable=true)
     */
    private $investment_model;


    /**
     * @var boolean
     *
     * @ORM\Column(name="as_seperate_pl", type="boolean")
     */
    private $as_seperate_pl = false;


    /**
     * @var string
     *
     * @ORM\Column(name="idea_description", type="text", length=65535, nullable=true)
     */
    private $idea_description;


   /**
    * @var string
    *
    * @ORM\Column(name="strategic_intent_mission", type="text", length=65535, nullable=true)
    */
    private $strategic_intent_mission;


    /**
     * @ORM\OneToMany(targetEntity="Canvas", mappedBy="innovation")
     */
    protected $canvas_collection;


    /**
     * @ORM\OneToOne(targetEntity="OpenQuestion", mappedBy="innovation")
     * @ORM\JoinColumn(name="open_question_id", referencedColumnName="id", nullable=true)
     */
    protected $open_question;


    /**
     * @var string
     *
     * @ORM\Column(name="project_owner_disponibility", type="text", length=65535, nullable=true)
     */
    private $project_owner_disponibility;

    /**
     * @var string
     *
     * @ORM\Column(name="full_time_employees", type="text", length=65535, nullable=true)
     */
    private $full_time_employees;

    /**
     * @var string
     *
     * @ORM\Column(name="external_text", type="text", length=65535, nullable=true)
     */
    private $external_text;


    /**
     * @var string
     *
     * @ORM\Column(name="alcohol_by_volume", type="string", length=255, nullable=true)
     */
    private $alcohol_by_volume;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Innovation
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set inMarketDate.
     *
     * @param \DateTime|null $inMarketDate
     *
     * @return Innovation
     */
    public function setInMarketDate($inMarketDate = null)
    {
        $this->in_market_date = $inMarketDate;

        return $this;
    }

    /**
     * Get inMarketDate.
     *
     * @return \DateTime|null
     */
    public function getInMarketDate()
    {
        return $this->in_market_date;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime|null $startDate
     *
     * @return Innovation
     */
    public function setStartDate($startDate = null)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime|null
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set contact.
     *
     * @param \AppBundle\Entity\User|null $contact
     *
     * @return Innovation
     */
    public function setContact(\AppBundle\Entity\User $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set stage.
     *
     * @param \AppBundle\Entity\Stage|null $stage
     *
     * @return Innovation
     */
    public function setStage(\AppBundle\Entity\Stage $stage = null)
    {
        // Automatically update old stage and old stage date
        if ($this->stage && $this->stage != $stage) {
            $this->setOldStage($this->stage);
            $this->setOldStageDate(new \DateTime());
        }
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage.
     *
     * @return \AppBundle\Entity\Stage|null
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set oldStage.
     *
     * @param \AppBundle\Entity\Stage|null $oldStage
     *
     * @return Innovation
     */
    public function setOldStage(\AppBundle\Entity\Stage $oldStage = null)
    {
        $this->old_stage = $oldStage;

        return $this;
    }

    /**
     * Get oldStage.
     *
     * @return \AppBundle\Entity\Stage|null
     */
    public function getOldStage()
    {
        return $this->old_stage;
    }


    /**
     * Set oldStageDate.
     *
     * @param \DateTime|null $oldStageDate
     *
     * @return Innovation
     */
    public function setOldStageDate($oldStageDate = null)
    {
        $this->old_stage_date = $oldStageDate;

        return $this;
    }

    /**
     * Get oldStageDate.
     *
     * @return \DateTime|null
     */
    public function getOldStageDate()
    {
        return $this->old_stage_date;
    }

    /**
     * Set type.
     *
     * @param \AppBundle\Entity\Type|null $type
     *
     * @return Innovation
     */
    public function setType(\AppBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return \AppBundle\Entity\Type|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set classification.
     *
     * @param \AppBundle\Entity\Classification|null $classification
     *
     * @return Innovation
     */
    public function setClassification(\AppBundle\Entity\Classification $classification = null)
    {
        $this->classification = $classification;

        return $this;
    }

    /**
     * Get classification.
     *
     * @return \AppBundle\Entity\Classification|null
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set isNewToTheWorld.
     *
     * @param bool $isNewToTheWorld
     *
     * @return Innovation
     */
    public function setIsNewToTheWorld($isNewToTheWorld)
    {
        $this->is_new_to_the_world = $isNewToTheWorld;

        return $this;
    }

    /**
     * Get isNewToTheWorld.
     *
     * @return bool
     */
    public function getIsNewToTheWorld()
    {
        return $this->is_new_to_the_world;
    }

    /**
     * Set isFrozen.
     *
     * @param bool $isFrozen
     *
     * @return Innovation
     */
    public function setIsFrozen($isFrozen)
    {
        $this->is_frozen = $isFrozen;

        return $this;
    }

    /**
     * Get isFrozen.
     *
     * @return bool
     */
    public function getIsFrozen()
    {
        return $this->is_frozen;
    }


    /**
     * Set consumerOpportunity.
     *
     * @param \AppBundle\Entity\ConsumerOpportunity|null $consumerOpportunity
     *
     * @return Innovation
     */
    public function setConsumerOpportunity(\AppBundle\Entity\ConsumerOpportunity $consumerOpportunity = null)
    {
        $this->consumer_opportunity = $consumerOpportunity;

        return $this;
    }

    /**
     * Get consumerOpportunity.
     *
     * @return \AppBundle\Entity\ConsumerOpportunity|null
     */
    public function getConsumerOpportunity()
    {
        return $this->consumer_opportunity;
    }

    /**
     * Set momentOfConsumption.
     *
     * @param \AppBundle\Entity\MomentOfConsumption|null $momentOfConsumption
     *
     * @return Innovation
     */
    public function setMomentOfConsumption(\AppBundle\Entity\MomentOfConsumption $momentOfConsumption = null)
    {
        $this->moment_of_consumption = $momentOfConsumption;

        return $this;
    }

    /**
     * Get momentOfConsumption.
     *
     * @return \AppBundle\Entity\MomentOfConsumption|null
     */
    public function getMomentOfConsumption()
    {
        return $this->moment_of_consumption;
    }

    /**
     * Set businessDriver.
     *
     * @param \AppBundle\Entity\BusinessDriver|null $businessDriver
     *
     * @return Innovation
     */
    public function setBusinessDriver(\AppBundle\Entity\BusinessDriver $businessDriver = null)
    {
        $this->business_driver = $businessDriver;

        return $this;
    }

    /**
     * Get businessDriver.
     *
     * @return \AppBundle\Entity\BusinessDriver|null
     */
    public function getBusinessDriver()
    {
        return $this->business_driver;
    }

    /**
     * Set portfolioProfile.
     *
     * @param \AppBundle\Entity\PortfolioProfile|null $portfolioProfile
     *
     * @return Innovation
     */
    public function setPortfolioProfile(\AppBundle\Entity\PortfolioProfile $portfolioProfile = null)
    {
        $this->portfolio_profile = $portfolioProfile;

        return $this;
    }

    /**
     * Get portfolioProfile.
     *
     * @return \AppBundle\Entity\PortfolioProfile|null
     */
    public function getPortfolioProfile()
    {
        return $this->portfolio_profile;
    }

    /**
     * Set entity.
     *
     * @param \AppBundle\Entity\Entity|null $entity
     *
     * @return Innovation
     */
    public function setEntity(\AppBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity.
     *
     * @return \AppBundle\Entity\Entity|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set category.
     *
     * @param string $category
     *
     * @return Innovation
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set isMultiBrand.
     *
     * @param bool $isMultiBrand
     *
     * @return Innovation
     */
    public function setIsMultiBrand($isMultiBrand)
    {
        $this->is_multi_brand = $isMultiBrand;

        return $this;
    }

    /**
     * Get isMultiBrand.
     *
     * @return bool
     */
    public function getIsMultiBrand()
    {
        return $this->is_multi_brand;
    }

    /**
     * Set isReplacingExistingProduct.
     *
     * @param bool $isReplacingExistingProduct
     *
     * @return Innovation
     */
    public function setIsReplacingExistingProduct($isReplacingExistingProduct)
    {
        $this->is_replacing_existing_product = $isReplacingExistingProduct;

        return $this;
    }

    /**
     * Get isReplacingExistingProduct.
     *
     * @return bool
     */
    public function getIsReplacingExistingProduct()
    {
        return $this->is_replacing_existing_product;
    }

    /**
     * Set replacingProduct.
     *
     * @param string|null $replacingProduct
     *
     * @return Innovation
     */
    public function setReplacingProduct($replacingProduct = null)
    {
        $this->replacing_product = $replacingProduct;

        return $this;
    }

    /**
     * Get replacingProduct.
     *
     * @return string|null
     */
    public function getReplacingProduct()
    {
        return $this->replacing_product;
    }

    /**
     * Set isInPrisma.
     *
     * @param bool $isInPrisma
     *
     * @return Innovation
     */
    public function setIsInPrisma($isInPrisma)
    {
        $this->is_in_prisma = $isInPrisma;

        return $this;
    }

    /**
     * Get isInPrisma.
     *
     * @return bool
     */
    public function getIsInPrisma()
    {
        return $this->is_in_prisma;
    }

    /**
     * Set whyInvestInThisInnovation.
     *
     * @param string $whyInvestInThisInnovation
     *
     * @return Innovation
     */
    public function setWhyInvestInThisInnovation($whyInvestInThisInnovation)
    {
        $this->why_invest_in_this_innovation = $whyInvestInThisInnovation;

        return $this;
    }

    /**
     * Get whyInvestInThisInnovation.
     *
     * @return string
     */
    public function getWhyInvestInThisInnovation()
    {
        return $this->why_invest_in_this_innovation;
    }

    /**
     * Set uniqueExperience.
     *
     * @param string $uniqueExperience
     *
     * @return Innovation
     */
    public function setUniqueExperience($uniqueExperience)
    {
        $this->unique_experience = $uniqueExperience;

        return $this;
    }

    /**
     * Get uniqueExperience.
     *
     * @return string
     */
    public function getUniqueExperience()
    {
        return $this->unique_experience;
    }

    /**
     * Set story.
     *
     * @param string $story
     *
     * @return Innovation
     */
    public function setStory($story)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story.
     *
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set uniqueness.
     *
     * @param string $uniqueness
     *
     * @return Innovation
     */
    public function setUniqueness($uniqueness)
    {
        $this->uniqueness = $uniqueness;

        return $this;
    }

    /**
     * Get uniqueness.
     *
     * @return string
     */
    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    /**
     * Set consumerInsight.
     *
     * @param string $consumerInsight
     *
     * @return Innovation
     */
    public function setConsumerInsight($consumerInsight)
    {
        $this->consumer_insight = $consumerInsight;

        return $this;
    }

    /**
     * Get consumerInsight.
     *
     * @return string
     */
    public function getConsumerInsight()
    {
        return $this->consumer_insight;
    }

    /**
     * Set earlyAdopterPersona.
     *
     * @param string $earlyAdopterPersona
     *
     * @return Innovation
     */
    public function setEarlyAdopterPersona($earlyAdopterPersona)
    {
        $this->early_adopter_persona = $earlyAdopterPersona;

        return $this;
    }

    /**
     * Get earlyAdopterPersona.
     *
     * @return string
     */
    public function getEarlyAdopterPersona()
    {
        return $this->early_adopter_persona;
    }

    /**
     * Set sourceOfBusiness.
     *
     * @param string $sourceOfBusiness
     *
     * @return Innovation
     */
    public function setSourceOfBusiness($sourceOfBusiness)
    {
        $this->source_of_business = $sourceOfBusiness;

        return $this;
    }

    /**
     * Get sourceOfBusiness.
     *
     * @return string
     */
    public function getSourceOfBusiness()
    {
        return $this->source_of_business;
    }

    /**
     * Set universalKeyInformation1.
     *
     * @param string|null $universalKeyInformation1
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation1($universalKeyInformation1 = null)
    {
        $this->universal_key_information_1 = $universalKeyInformation1;

        return $this;
    }

    /**
     * Get universalKeyInformation1.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation1()
    {
        return $this->universal_key_information_1;
    }

    /**
     * Set universalKeyInformation2.
     *
     * @param string|null $universalKeyInformation2
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation2($universalKeyInformation2 = null)
    {
        $this->universal_key_information_2 = $universalKeyInformation2;

        return $this;
    }

    /**
     * Get universalKeyInformation2.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation2()
    {
        return $this->universal_key_information_2;
    }

    /**
     * Set universalKeyInformation3.
     *
     * @param string|null $universalKeyInformation3
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation3($universalKeyInformation3 = null)
    {
        $this->universal_key_information_3 = $universalKeyInformation3;

        return $this;
    }

    /**
     * Get universalKeyInformation3.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation3()
    {
        return $this->universal_key_information_3;
    }

    /**
     * Set universalKeyInformation4.
     *
     * @param string|null $universalKeyInformation4
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation4($universalKeyInformation4 = null)
    {
        $this->universal_key_information_4 = $universalKeyInformation4;

        return $this;
    }

    /**
     * Get universalKeyInformation4.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation4()
    {
        return $this->universal_key_information_4;
    }

    /**
     * Set universalKeyInformation5.
     *
     * @param string|null $universalKeyInformation5
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation5($universalKeyInformation5 = null)
    {
        $this->universal_key_information_5 = $universalKeyInformation5;

        return $this;
    }

    /**
     * Get universalKeyInformation5.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation5()
    {
        return $this->universal_key_information_5;
    }

    /**
     * Set potLegend1.
     *
     * @param string|null $potLegend1
     *
     * @return Innovation
     */
    public function setPotLegend1($potLegend1 = null)
    {
        $this->pot_legend_1 = $potLegend1;

        return $this;
    }

    /**
     * Get potLegend1.
     *
     * @return string|null
     */
    public function getPotLegend1()
    {
        return $this->pot_legend_1;
    }

    /**
     * Set potLegend2.
     *
     * @param string|null $potLegend2
     *
     * @return Innovation
     */
    public function setPotLegend2($potLegend2 = null)
    {
        $this->pot_legend_2 = $potLegend2;

        return $this;
    }

    /**
     * Get potLegend2.
     *
     * @return string|null
     */
    public function getPotLegend2()
    {
        return $this->pot_legend_2;
    }

    /**
     * Set keyLearningSoFar.
     *
     * @param string $keyLearningSoFar
     *
     * @return Innovation
     */
    public function setKeyLearningSoFar($keyLearningSoFar)
    {
        $this->key_learning_so_far = $keyLearningSoFar;

        return $this;
    }

    /**
     * Get keyLearningSoFar.
     *
     * @return string
     */
    public function getKeyLearningSoFar()
    {
        return $this->key_learning_so_far;
    }

    /**
     * Set nextSteps.
     *
     * @param string $nextSteps
     *
     * @return Innovation
     */
    public function setNextSteps($nextSteps)
    {
        $this->next_steps = $nextSteps;

        return $this;
    }

    /**
     * Get nextSteps.
     *
     * @return string
     */
    public function getNextSteps()
    {
        return $this->next_steps;
    }

    /**
     * Set growthModel.
     *
     * @param string $growthModel
     *
     * @return Innovation
     */
    public function setGrowthModel($growthModel)
    {
        $this->growth_model = $growthModel;

        return $this;
    }

    /**
     * Get growthModel.
     *
     * @return string
     */
    public function getGrowthModel()
    {
        return $this->growth_model;
    }

    /**
     * Set isEarningAnyMoneyYet.
     *
     * @param bool $isEarningAnyMoneyYet
     *
     * @return Innovation
     */
    public function setIsEarningAnyMoneyYet($isEarningAnyMoneyYet)
    {
        $this->is_earning_any_money_yet = $isEarningAnyMoneyYet;

        return $this;
    }

    /**
     * Get isEarningAnyMoneyYet.
     *
     * @return bool
     */
    public function getIsEarningAnyMoneyYet()
    {
        return $this->is_earning_any_money_yet;
    }

    /**
     * Set planToMakeMoney.
     *
     * @param string $planToMakeMoney
     *
     * @return Innovation
     */
    public function setPlanToMakeMoney($planToMakeMoney)
    {
        $this->plan_to_make_money = $planToMakeMoney;

        return $this;
    }

    /**
     * Get planToMakeMoney.
     *
     * @return string
     */
    public function getPlanToMakeMoney()
    {
        return $this->plan_to_make_money;
    }

    /**
     * Set markets.
     *
     * @param string $markets
     *
     * @return Innovation
     */
    public function setMarkets($markets)
    {
        $this->markets = $markets;

        return $this;
    }

    /**
     * Set markets array.
     *
     * @param array $markets
     *
     * @return Innovation
     */
    public function setMarketsArray($markets)
    {
        $this->markets = json_encode($markets);

        return $this;
    }

    /**
     * Get markets.
     *
     * @return string
     */
    public function getMarkets()
    {
        return $this->markets;
    }

    /**
     * Get markets array.
     *
     * @return array
     */
    public function getMarketsArray()
    {
        if ($this->markets) {
            return json_decode($this->markets, true);
        }
        return array();
    }


    /**
     * Set videoUrl.
     *
     * @param string|null $videoUrl
     *
     * @return Innovation
     */
    public function setVideoUrl($videoUrl = null)
    {
        $this->video_url = $videoUrl;

        return $this;
    }

    /**
     * Get videoUrl.
     *
     * @return string|null
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * Set videoPassword.
     *
     * @param string|null $videoPassword
     *
     * @return Innovation
     */
    public function setVideoPassword($videoPassword = null)
    {
        $this->video_password = $videoPassword;

        return $this;
    }

    /**
     * Get videoPassword.
     *
     * @return string|null
     */
    public function getVideoPassword()
    {
        return $this->video_password;
    }

    /**
     * Set ibpUrl.
     *
     * @param string|null $ibpUrl
     *
     * @return Innovation
     */
    public function setIbpUrl($ibpUrl = null)
    {
        $this->ibp_url = $ibpUrl;

        return $this;
    }

    /**
     * Get ibpUrl.
     *
     * @return string|null
     */
    public function getIbpUrl()
    {
        return $this->ibp_url;
    }

    /**
     * Set mybrandsUrl.
     *
     * @param string|null $mybrandsUrl
     *
     * @return Innovation
     */
    public function setMybrandsUrl($mybrandsUrl = null)
    {
        $this->mybrands_url = $mybrandsUrl;

        return $this;
    }

    /**
     * Get mybrandsUrl.
     *
     * @return string|null
     */
    public function getMybrandsUrl()
    {
        return $this->mybrands_url;
    }

    /**
     * Set pressUrl.
     *
     * @param string|null $pressUrl
     *
     * @return Innovation
     */
    public function setPressUrl($pressUrl = null)
    {
        $this->press_url = $pressUrl;

        return $this;
    }

    /**
     * Get pressUrl.
     *
     * @return string|null
     */
    public function getPressUrl()
    {
        return $this->press_url;
    }

    /**
     * Set isNeedingFinancialUpdate.
     *
     * @param bool $isNeedingFinancialUpdate
     *
     * @return Innovation
     */
    public function setIsNeedingFinancialUpdate($isNeedingFinancialUpdate)
    {
        $this->is_needing_financial_update = $isNeedingFinancialUpdate;

        return $this;
    }

    /**
     * Get isNeedingFinancialUpdate.
     *
     * @return bool
     */
    public function getIsNeedingFinancialUpdate()
    {
        return $this->is_needing_financial_update;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Innovation
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set potPicture1.
     *
     * @param \AppBundle\Entity\Picture|null $potPicture1
     *
     * @return Innovation
     */
    public function setPotPicture1(\AppBundle\Entity\Picture $potPicture1 = null)
    {
        $this->pot_picture_1 = $potPicture1;

        return $this;
    }

    /**
     * Get potPicture1.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getPotPicture1()
    {
        return $this->pot_picture_1;
    }

    /**
     * Set potPicture2.
     *
     * @param \AppBundle\Entity\Picture|null $potPicture2
     *
     * @return Innovation
     */
    public function setPotPicture2(\AppBundle\Entity\Picture $potPicture2 = null)
    {
        $this->pot_picture_2 = $potPicture2;

        return $this;
    }

    /**
     * Get potPicture2.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getPotPicture2()
    {
        return $this->pot_picture_2;
    }

    /**
     * Set beautyshotPicture.
     *
     * @param \AppBundle\Entity\Picture|null $beautyshotPicture
     *
     * @return Innovation
     */
    public function setBeautyshotPicture(\AppBundle\Entity\Picture $beautyshotPicture = null)
    {
        $this->beautyshot_picture = $beautyshotPicture;

        return $this;
    }

    /**
     * Get beautyshotPicture.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getBeautyshotPicture()
    {
        return $this->beautyshot_picture;
    }

    /**
     * Set packshotPicture.
     *
     * @param \AppBundle\Entity\Picture|null $packshotPicture
     *
     * @return Innovation
     */
    public function setPackshotPicture(\AppBundle\Entity\Picture $packshotPicture = null)
    {
        $this->packshot_picture = $packshotPicture;

        return $this;
    }

    /**
     * Get packshotPicture.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getPackshotPicture()
    {
        return $this->packshot_picture;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->financial_datas = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add financialData.
     *
     * @param \AppBundle\Entity\FinancialData $financialData
     *
     * @return Innovation
     */
    public function addFinancialData(\AppBundle\Entity\FinancialData $financialData)
    {
        $this->financial_datas[] = $financialData;

        return $this;
    }

    /**
     * Remove financialData.
     *
     * @param \AppBundle\Entity\FinancialData $financialData
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFinancialData(\AppBundle\Entity\FinancialData $financialData)
    {
        return $this->financial_datas->removeElement($financialData);
    }

    /**
     * Get financialDatas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFinancialDatas()
    {
        return $this->financial_datas;
    }

    /**
     * Add additionalPicture.
     *
     * @param \AppBundle\Entity\AdditionalPicture $additionalPicture
     *
     * @return Innovation
     */
    public function addAdditionalPicture(\AppBundle\Entity\AdditionalPicture $additionalPicture)
    {
        $this->additional_pictures[] = $additionalPicture;

        return $this;
    }

    /**
     * Remove additionalPicture.
     *
     * @param \AppBundle\Entity\AdditionalPicture $additionalPicture
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAdditionalPicture(\AppBundle\Entity\AdditionalPicture $additionalPicture)
    {
        return $this->additional_pictures->removeElement($additionalPicture);
    }

    /**
     * Get additionalPictures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdditionalPictures()
    {
        return $this->additional_pictures;
    }

    /**
     * Get additionalPictures by order.
     *
     * @param int $order
     * @return AdditionalPicture|null
     */
    public function getAdditionalPictureByOrder($order)
    {
        return $this->getAdditionalPictures()->filter(function ($additional_picture) use ($order) {
            return $additional_picture->getOrder() === $order;
        })->first();
    }

    /**
     * Add performanceReview.
     *
     * @param \AppBundle\Entity\PerformanceReview $performanceReview
     *
     * @return Innovation
     */
    public function addPerformanceReview(\AppBundle\Entity\PerformanceReview $performanceReview)
    {
        $this->performance_reviews[] = $performanceReview;

        return $this;
    }

    /**
     * Remove performanceReview.
     *
     * @param \AppBundle\Entity\PerformanceReview $performanceReview
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerformanceReview(\AppBundle\Entity\PerformanceReview $performanceReview)
    {
        return $this->performance_reviews->removeElement($performanceReview);
    }

    /**
     * Get performanceReviews.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerformanceReviews()
    {
        return $this->performance_reviews;
    }

    /**
     * Add userInnovationRight.
     *
     * @param \AppBundle\Entity\UserInnovationRight $userInnovationRight
     *
     * @return Innovation
     */
    public function addUserInnovationRight(\AppBundle\Entity\UserInnovationRight $userInnovationRight)
    {
        $this->user_innovation_rights[] = $userInnovationRight;

        return $this;
    }

    /**
     * Remove userInnovationRight.
     *
     * @param \AppBundle\Entity\UserInnovationRight $userInnovationRight
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUserInnovationRight(\AppBundle\Entity\UserInnovationRight $userInnovationRight)
    {
        return $this->user_innovation_rights->removeElement($userInnovationRight);
    }

    /**
     * Get userInnovationRights.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserInnovationRights()
    {
        return $this->user_innovation_rights;
    }

    /**
     * Get users team.
     *
     * @param bool $only_write
     * @return array
     */
    public function getUsersTeam($only_write = true)
    {
        $ret = array();
        foreach ($this->getUserInnovationRights() as $user_innovation_right) {
            if ($only_write) {
                if ($user_innovation_right->getRight() == UserInnovationRight::RIGHT_WRITE) {
                    $ret[] = $user_innovation_right->getUser();
                }
            } else {
                $ret[] = $user_innovation_right->getUser();
            }
        }
        return $ret;
    }

    /**
     * Add activity.
     *
     * @param \AppBundle\Entity\Activity $activity
     *
     * @return Innovation
     */
    public function addActivity(\AppBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity.
     *
     * @param \AppBundle\Entity\Activity $activity
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeActivity(\AppBundle\Entity\Activity $activity)
    {
        return $this->activities->removeElement($activity);
    }

    /**
     * Get activities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set oldId.
     *
     * @param int $oldId
     *
     * @return Innovation
     */
    public function setOldId($oldId)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get oldId.
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->old_id;
    }

    /**
     * Set brand.
     *
     * @param \AppBundle\Entity\Brand|null $brand
     *
     * @return Innovation
     */
    public function setBrand(\AppBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return \AppBundle\Entity\Brand|null
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set financialGraphPicture.
     *
     * @param \AppBundle\Entity\Picture|null $financialGraphPicture
     *
     * @return Innovation
     */
    public function setFinancialGraphPicture(\AppBundle\Entity\Picture $financialGraphPicture = null)
    {
        $this->financial_graph_picture = $financialGraphPicture;

        return $this;
    }

    /**
     * Get financialGraphPicture.
     *
     * @return \AppBundle\Entity\Picture|null
     */
    public function getFinancialGraphPicture()
    {
        return $this->financial_graph_picture;
    }

    /**
     *
     * Return the number of years since the in market date
     *
     * @return int
     */
    public function getNbYearSinceLaunch()
    {
        $date = $this->in_market_date;

        if (!$date) {
            return 0;
        }
        $actualDate = new \DateTime();
        $actualDate->setTimezone(new \DateTimeZone('GMT'));
        $diff = $date->diff($actualDate);
        $nb_years = $diff->format("%y");
        if ($date > $actualDate) { // negative date
            $nb_years = -$nb_years;
            if ((abs($diff->format("%m%")) + abs($diff->format("%d%"))) >= 1) {
                $nb_years -= 1;
            }
            return intval($nb_years);
        } elseif ($nb_years >= 1) {
            if (($diff->format("%m%") + $diff->format("%d%")) >= 1) {
                $nb_years += 1;
            }
            return intval($nb_years);
        } else { // First year
            return 1;
        }
    }

    /**
     * Get innovation url.
     *
     * @return string
     */
    public function getInnovationUrl()
    {
        return "/explore/" . $this->getId();
    }


    /**
     * toArray.
     *
     * @param Settings $settings
     * @param null|object $liip
     *
     * @return array
     */
    public function toArray(Settings $settings, $liip = null)
    {
        if (array_key_exists('CURRENT_MODE', $_ENV) && $_ENV['CURRENT_MODE'] == 'dev') {
            $liip = null;
        }
        $ret = array();
        $ret['id'] = $this->getId();
        $ret['title'] = $this->getTitle();
        $ret['in_market_date'] = ($this->getInMarketDate()) ? $this->getInMarketDate()->getTimestamp() : null;
        $ret['sort_explore_date'] = $this->getSortExploreDate();
        $ret['start_date'] = ($this->getStartDate()) ? $this->getStartDate()->getTimestamp() : null;
        $ret['created_at'] = $this->getCreatedAt()->getTimestamp();
        $ret['updated_at'] = $this->getUpdatedAt()->getTimestamp();
        $ret['current_stage_id'] = ($this->getStage()) ? $this->getStage()->getId() : null;
        $ret['current_stage'] = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $ret['previous_stage_date'] = ($this->getOldStageDate()) ? $this->getOldStageDate()->getTimestamp() : null;
        $ret['previous_stage'] = ($this->getOldStage()) ? $this->getOldStage()->getCssClass() : '';
        $ret['last_a_stage_id'] = $this->getLastAStage($settings);
        $ret['last_a_is_frozen'] = $this->getLastAIsFrozen($settings);
        $ret['innovation_type'] = ($this->getType()) ? $this->getType()->getTitle() : null;
        $ret['classification_type'] = ($this->getClassification()) ? $this->getClassification()->getTitle() : null;
        $ret['new_to_the_world'] = $this->getIsNewToTheWorld();
        $ret['growth_model'] = $this->getGrowthModel();
        if ($this->isAService()) {
            $ret['growth_model'] = '';
        }
        $ret['brand'] = array(
            'title' => (($this->getBrand()) ? $this->getBrand()->getTitle() : ''),
            'id' => (($this->getBrand()) ? $this->getBrand()->getId() : ''),
            'group_id' => (($this->getBrand()) ? $this->getBrand()->getGroupId() : ''),
        );
        $ret['tags'] = array();
        $ret['tags_array'] = array();
        foreach ($this->getTags() as $tag) {
            $ret['tags'][] = $tag->toSelect2Array();
            $ret['tags_array'][] = $tag->getTitle();
        }

        // Pictures
        $ret['beautyshot_picture'] = ($this->getBeautyshotPicture()) ? $this->getBeautyshotPicture()->toArray($liip) : null;
        $ret['packshot_picture'] = ($this->getPackshotPicture()) ? $this->getPackshotPicture()->toArray($liip) : null;
        $ret['performance_picture'] = ($this->getPackshotPicture()) ? $this->getPackshotPicture()->resizeImage($liip, 'performance_picture') : null;
        $ret['ppt_beautyshot_quali_bg'] = ($this->getBeautyshotPicture()) ? $this->getBeautyshotPicture()->resizeImage($liip, 'beautyshot_quali_bg') : null;
        $ret['ppt_picture_quali'] = ($this->getBeautyshotPicture()) ? $this->getBeautyshotPicture()->resizeImage($liip, 'quali') : null;
        $ret['financial_graph_picture'] = ($this->getFinancialGraphPicture()) ? $this->getFinancialGraphPicture()->resizeImage($liip, 'financial_graph') : null;
        $ret['pot_picture_1'] = ($this->getPotPicture1()) ? $this->getPotPicture1()->toArray($liip) : null;
        $ret['pot_picture_2'] = ($this->getPotPicture2()) ? $this->getPotPicture2()->toArray($liip) : null;
        $ret['ppt_pot_picture_1'] = ($this->getPotPicture1()) ? $this->getPotPicture1()->resizeImage($liip, 'proofs_of_traction') : null;
        $ret['ppt_pot_picture_2'] = ($this->getPotPicture2()) ? $this->getPotPicture2()->resizeImage($liip, 'proofs_of_traction') : null;

        $ret['other_pictures'] = array();
        if ($this->getAdditionalPictures() && count($this->getAdditionalPictures()) > 0) {
            foreach ($this->getAdditionalPictures() as $additionalPicture) {
                if ($additionalPicture->getPicture()) {
                    $ret['other_pictures'][] = $additionalPicture->getPicture()->toArray($liip);
                }
            }
        }
        $ret['explore'] = array();
        $ret['explore']['images_detail'] = $this->getExplorePictures($liip, true, true);
        $ret['explore']['images'] = $this->getExplorePictures($liip);
        // End of pictures
        $ret['contact'] = array(
            'uid' => (($this->getContact()) ? $this->getContact()->getId() : null),
            'username' => (($this->getContact()) ? $this->getContact()->getProperUsername() : ''),
            'email' => (($this->getContact()) ? $this->getContact()->getEmail() : ''),
            'picture' => (($this->getContact()) ? $this->getContact()->getPictureUrl() : '')
        );
        $ret['entity'] = ($this->getEntity()) ? $this->getEntity()->toArray() : array('id' => -1, 'title' => '', 'group_id' => -1);

        $ret['is_new'] = $this->getIsNew();
        $ret['is_soon'] = $this->getIsSoon();
        $ret['empty_fields'] = $this->getNumberOfEmptyFields($settings);
        $ret['count_empty_fields'] = $ret['empty_fields']['total'];
        $ret['sort_manage_relevance'] = $this->getSortManageRelevance($ret['count_empty_fields']);
        $ret['sort_explore_nbm_relevance'] = $this->getSortNbmRelevance();

        // Quali data
        $ret['story'] = ($this->getStory()) ? $this->getStory() : '';
        $ret['value_proposition'] = ($this->getUniqueness()) ? $this->getUniqueness() : '';
        $ret['consumer_insight'] = ($this->getConsumerInsight()) ? $this->getConsumerInsight() : '';
        $ret['early_adopter_persona'] = ($this->getEarlyAdopterPersona()) ? $this->getEarlyAdopterPersona() : '';
        $ret['source_of_business'] = ($this->getSourceOfBusiness()) ? $this->getSourceOfBusiness() : '';
        $ret['growth_strategy'] = ($this->getPortfolioProfile()) ? $this->getPortfolioProfile()->getTitle() : 'Contributor';
        $ret['category'] = ($this->getCategory()) ? $this->getCategory() : '';
        $ret['abv'] = ($this->getAlcoholByVolume()) ? $this->getAlcoholByVolume() : '';
        $ret['is_multi_brand'] = ($this->getIsMultiBrand()) ? '1' : '0';
        $ret['unique_experience'] = ($this->getUniqueExperience()) ? $this->getUniqueExperience() : '';
        $ret['have_earned_any_money_yet'] = ($this->getIsEarningAnyMoneyYet()) ? '1' : '0';
        $ret['plan_to_make_money'] = ($this->getPlanToMakeMoney()) ? $this->getPlanToMakeMoney() : '';
        $ret['why_invest_in_this_innovation'] = ($this->getWhyInvestInThisInnovation()) ? $this->getWhyInvestInThisInnovation() : '';
        $ret['consumer_opportunity'] = ($this->getConsumerOpportunity()) ? $this->getConsumerOpportunity()->getId() : 0;
        $ret['consumer_opportunity_title'] = ($this->getConsumerOpportunity()) ? $this->getConsumerOpportunity()->getTitle() : '';
        $ret['replace_existing_product'] = ($this->getIsReplacingExistingProduct()) ? '1' : '0';
        $ret['in_prisma'] = ($this->getIsInPrisma()) ? '1' : '0';
        $ret['existing_product'] = ($this->getReplacingProduct()) ? $this->getReplacingProduct() : '';
        $ret['video_link'] = ($this->getVideoUrl()) ? $this->getVideoUrl() : '';
        $ret['video_password'] = ($this->getVideoPassword()) ? $this->getVideoPassword() : '';
        $ret['press_release_link'] = ($this->getPressUrl()) ? $this->getPressUrl() : '';
        $ret['ibp_link'] = ($this->getIbpUrl()) ? $this->getIbpUrl() : '';
        $ret['website_url'] = ($this->getMybrandsUrl()) ? $this->getMybrandsUrl() : '';
        $ret['universal_next_steps'] = ($this->getNextSteps()) ? $this->getNextSteps() : '';
        $ret['universal_key_learning_so_far'] = ($this->getKeyLearningSoFar()) ? $this->getKeyLearningSoFar() : '';
        $ret['universal_key_information_1'] = ($this->getUniversalKeyInformation1()) ? $this->getUniversalKeyInformation1() : '';
        $ret['universal_key_information_2'] = ($this->getUniversalKeyInformation2()) ? $this->getUniversalKeyInformation2() : '';
        $ret['universal_key_information_3'] = ($this->getUniversalKeyInformation3()) ? $this->getUniversalKeyInformation3() : '';
        $ret['universal_key_information_3_vs'] = ($this->getUniversalKeyInformation3Vs()) ? $this->getUniversalKeyInformation3Vs() : '';
        $ret['universal_key_information_4'] = ($this->getUniversalKeyInformation4()) ? $this->getUniversalKeyInformation4() : '';
        $ret['universal_key_information_4_vs'] = ($this->getUniversalKeyInformation4Vs()) ? $this->getUniversalKeyInformation4Vs() : '';
        $ret['universal_key_information_5'] = ($this->getUniversalKeyInformation5()) ? $this->getUniversalKeyInformation5() : '';
        $ret['proofs_of_traction_picture_1_legend'] = ($this->getPotLegend1()) ? $this->getPotLegend1() : '';
        $ret['proofs_of_traction_picture_2_legend'] = ($this->getPotLegend2()) ? $this->getPotLegend2() : '';
        $ret['markets_in'] = ($this->getMarkets()) ? $this->getMarkets() : '[]';
        $ret['markets_in_array'] = $this->getMarketsArray();
        $ret['is_frozen'] = $this->getIsFrozen();
        $ret['moc'] = ($this->getMomentOfConsumption()) ? $this->getMomentOfConsumption()->getTitle() : null;
        $ret['business_drivers'] = ($this->getBusinessDriver()) ? $this->getBusinessDriver()->getTitle() : null;
        $ret['monitor_status'] = $this->getMonitorStatus();
        $ret['big_bet'] = ($ret['growth_strategy'] == 'Big Bet') ? 'BB' : '';
        $ret['years_since_launch'] = $this->getNbYearSinceLaunch();
        // End Quali data

        
        if($this->isANewBusinessAcceleration()){
            // Start NBA data
            $ret['new_business_opportunity'] = ($this->getNewBusinessOpportunity()) ? $this->getNewBusinessOpportunity() : '';
            $ret['investment_model'] = ($this->getInvestmentModel()) ? $this->getInvestmentModel() : '';
            $ret['as_seperate_pl'] = ($this->getAsSeperatePl()) ? '1' : '0';
            $ret['idea_description'] = ($this->getIdeaDescription()) ? $this->getIdeaDescription() : '';
            $ret['strategic_intent_mission'] = ($this->getStrategicIntentMission()) ? $this->getStrategicIntentMission() : '';
            $ret['key_cities'] = $this->getKeyCitiesArray();
            $ret['open_question'] = ($this->getOpenQuestion()) ? $this->getOpenQuestion()->toArray() : null;
            $ret['project_owner_disponibility'] = ($this->getProjectOwnerDisponibility()) ? $this->getProjectOwnerDisponibility() : '';
            $ret['full_time_employees'] = ($this->getFullTimeEmployees()) ? $this->getFullTimeEmployees() : '';
            $ret['external_text'] = ($this->getExternalText()) ? $this->getExternalText() : '';
            $ret['canvas_collection'] = $this->getCanvasCollectionArray();
            // End NBA data
        }


        // Financial infos
        $ret['financial'] = $this->toFinancialArray($settings);
        $ret['financial_updated_at'] = $this->getFinancialUpdatedAt();
        $performance_review = $this->getPerformanceReviewsByKey('complement_' . $settings->getCurrentFinancialDate());
        $ret['performance_review'] = $performance_review ? $performance_review->getValue() : '';
        // End financials datas

        // Start proper
        $ret['proper'] = array();
        $ret['proper']['category'] = $ret['category'];
        $ret['proper']['total_volume'] = $this->getTotalVolumeForFilter($ret['financial']['data']['latest']['saved']['volume']);
        $ret['proper']['title'] = Settings::returnProperJsString($ret['title']);
        $ret['proper']['story'] = Settings::returnProperJsString($ret['story']);
        $financial_data_is_valid = $this->isValidFinancialData($settings);
        $ret['proper']['other'] = $this->getProperOtherValue($financial_data_is_valid, $ret['growth_strategy']);
        $ret['proper']['current_stage'] = Settings::returnProperJsString($ret['current_stage']);
        $ret['proper']['brand_title'] = Settings::returnProperJsString($ret['brand']['title']);
        $ret['proper']['entity_title'] = Settings::returnProperJsString($ret['entity']['title']);
        $ret['proper']['contact_username'] = ($ret['contact']) ? Settings::returnProperJsString($ret['contact']['username']) : '';
        $ret['proper']['innovation_type'] = Settings::returnProperJsString($ret['innovation_type']);
        $classification_type = ($this->getClassification()) ? $this->getClassification()->getTitle() : "Empty";
        $ret['proper']['classification_type'] = Settings::returnProperJsString($classification_type, true);
        // end proper

        $ap_vs_ns = FinancialData::calculateLevelOfInvestment(
            $ret['financial']['data']['latest']['calc']['total_ap'],
            $ret['financial']['data']['latest']['calc']['net_sales']
        );
        $ret['ppt'] = array(
            'volume' => $this->calculateVolume($settings),
            'Evol_VOL_vs' => $this->calculateEvolVolumeVersusLastA($settings),
            'ap_vs_ns' => $ap_vs_ns,
            'caap' => $this->calculateCAAP($settings),
            'template' => $this->getPPTTtemplate()
        );
        $ret['sort_score'] = $this->getSortScore();
        return $ret;
    }


    /**
     * toSearchArray
     *
     * @return array
     */
    public function toSearchArray(){
        $ret = array();
        $ret['id'] = $this->getId();
        $ret['title'] = $this->getTitle();
        $ret['classification_type'] = ($this->getClassification()) ? $this->getClassification()->getTitle() : null;
        $ret['current_stage'] = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $ret['in_market_date'] = ($this->getInMarketDate()) ? $this->getInMarketDate()->getTimestamp() : null;
        $ret['explore']['images'][0] = $this->getExplorePictures()[0];
        return $ret;
    }
    /**
     * array to frontend.
     *
     * @param array $innovation_array
     * @param bool $limited
     *
     * @return array
     */
    public static function arrayToFrontend($innovation_array, $limited = false)
    {
        if ($limited) {
            unset($innovation_array['previous_stage_date']);
            unset($innovation_array['previous_stage']);
            unset($innovation_array['last_a_stage_id']);
            unset($innovation_array['last_a_is_frozen']);
            unset($innovation_array['pot_picture_1']);
            unset($innovation_array['pot_picture_2']);
            unset($innovation_array['is_new']);
            unset($innovation_array['is_soon']);
            unset($innovation_array['count_empty_fields']);
            unset($innovation_array['empty_fields']);
            unset($innovation_array['in_prisma']);
            unset($innovation_array['performance_review']);
            unset($innovation_array['proper']['other']);
            unset($innovation_array['financial']['current_date']);
            unset($innovation_array['financial']['need_update']);
            unset($innovation_array['financial']['data']);
            unset($innovation_array['financial']['abs']);
            unset($innovation_array['financial']['dataset']);
            unset($innovation_array['financial']['is_incomplete']);
        }
        unset($innovation_array['ppt_beautyshot_quali_bg']);
        unset($innovation_array['ppt_picture_quali']);
        unset($innovation_array['financial_graph_picture']);
        unset($innovation_array['ppt_pot_picture_1']);
        unset($innovation_array['ppt_pot_picture_2']);
        unset($innovation_array['ppt']);
        return $innovation_array;
    }

    /**
     * getProperOtherValue
     * @param $financial_data_is_valid
     * @param $growth_strategy
     * @return int
     */
    function getProperOtherValue($financial_data_is_valid, $growth_strategy)
    {
        /**
         * Other value :
         * O : Rien
         * 1 : Données financières manquantes
         * 2 : Données financières complétés
         * 3 : Big Bet + Données financières complétés
         * 4 : Big Bet + Données financières manquantes
         * 5 : Top Contributor + Données financières complétés
         * 6 : Negative CAAP + Données financières complétés
         * 7 : Top Contributor + Données financières manquantes
         * 8 : Negative CAAP + Données financières manquantes
         * 9 : High Investment + Données financières complétés
         * 10 : High Investment + Données financières manquantes
         */
        $proper_other = (!$financial_data_is_valid) ? 1 : 2;
        $invalid_financial_data = ($proper_other == 1);
        if ($growth_strategy == 'Big Bet') {
            $proper_other = ($invalid_financial_data) ? 4 : 3;
        } elseif ($growth_strategy == 'Top contributor') {
            $proper_other = ($invalid_financial_data) ? 7 : 5;
        } elseif ($growth_strategy == 'Negative CAAP') {
            $proper_other = ($invalid_financial_data) ? 8 : 6;
        } elseif ($growth_strategy == 'High investment') {
            $proper_other = ($invalid_financial_data) ? 10 : 9;
        }
        return $proper_other;
    }


    function getPPTTtemplate()
    {

        $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $classification_type = ($this->getClassification()) ? $this->getClassification()->getTitle() : 'null';
        $innovation_type = ($this->getType()) ? $this->getType()->getTitle() : null;

        $retour = '';
        switch ($current_stage) {
            case 'discover';
                $retour .= 'ide-';
                break;
            case 'ideate';
                $retour .= 'ide-';
                break;
            case 'experiment';
                $retour .= 'exp-';
                break;
            case 'incube';
                $retour .= 'inc-';
                break;
            case 'scale_up';
                $retour .= 'sca-';
                break;
            default:
                return '';
        }
        switch ($classification_type) {
            case 'Product':
                $retour .= 'pro';
                break;
            case 'Service':
                $retour .= 'ser';
                break;
            default:
                return $retour . 'none-none.jpg';
        }
        switch ($innovation_type) {
            case 'Stretch':
                $retour .= '-str.jpg';
                break;
            case 'Incremental':
                $retour .= '-inc.jpg';
                break;
            case 'Breakthrough':
                $retour .= '-bre.jpg';
                break;
            default:
                $retour .= '-none.jpg';
                break;
        }
        return $retour;
    }

    /**
     * Get sort explore date.
     *
     * @return int|null
     */
    public function getSortExploreDate()
    {
        if (!$this->getInMarketDate() || !$this->getStage()) {
            return null;
        }
        if (in_array($this->getStage()->getId(), Stage::getEarlyStageIds())) {
            return null;
        }
        return $this->getInMarketDate()->getTimestamp();
    }

    /**
     * Get sort manage relevance
     * @param $nb_empty_fields
     * @return int
     */
    public function getSortManageRelevance($nb_empty_fields)
    {
        $sort = 0;
        if(!$this->getStage()){
            return $sort;
        }
        if (in_array($this->getStage()->getId(), Stage::getInnerStages()) && !$this->getIsFrozen()) {
            $sort += 1000;
            $sort += $nb_empty_fields;
        }
        if($this->getIsFrozen()){
            $sort += 500;
        }
        if($this->getStage()->getId() == Stage::STAGE_ID_PERMANENT_RANGE){
            $sort += 100;
        }
        if($this->getStage()->getId() == Stage::STAGE_ID_DISCONTINUED){
            $sort += 50;
        }
        return $sort;
    }

    /**
     * Get sort new business model relevance
     * @return int
     */
    public function getSortNbmRelevance()
    {
        $sort = 0;
        if(!$this->getStage()){
            return $sort;
        }
        /*
        if (in_array($this->getStage()->getId(), Stage::getInnerStages()) && !$this->getIsFrozen()) {
            $sort += 1000;
        }*/
        if($this->isANewBusinessAcceleration()){
            $sort += 1000000;
        }
        $sort += $this->getAlphabeticalScore('ASC');
        return $sort;
    }

    /**
     * Get alphabetical score.
     *
     * @param string $order
     * @param int $scale
     * @return float|int
     */
    public function getAlphabeticalScore($order = 'DESC', $scale = 100){
        $letters = $this->getTitle();
        $alphabet = ($order == 'ASC') ? range('Z', 'A') : range('A', 'Z');
        $number = 0;
        foreach(str_split($letters) as $key=>$char){
            if(in_array($char, $alphabet)) {
                $adding_score = (array_search($char, $alphabet) + 1) * $scale;
                $number = $number + $adding_score;
                $scale = $scale / 10;
            }
        }
        return $number;
    }

    /**
     * Get previous stage activity.
     *
     * @return Activity|null
     */
    public function getPreviousStageActivity()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_CHANGE_STAGE;
        })->first();
    }


    /**
     * Get number of promote exports
     *
     * @return int
     */
    public function getNumberOfPromoteExports()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_PROMOTE_INNOVATION_EXPORT;
        })->count();
    }

    /**
     * Get number of promote exports this week
     *
     * @return int
     */
    public function getNumberOfPromoteExportsThisWeek()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return (
                $activity->getActionId() == Activity::ACTION_PROMOTE_INNOVATION_EXPORT &&
                (time() - $activity->getCreatedAt()->getTimestamp()) <= 604800 // 1 semaine en secondes
            );
        })->count();
    }

    /**
     * Get number of promote views
     *
     * @return int
     */
    public function getNumberOfPromoteViews()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_PROMOTE_INNOVATION_VIEW;
        })->count();
    }

    /**
     * Get number of promote views this week
     *
     * @return int
     */
    public function getNumberOfPromoteViewsThisWeek()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return (
                $activity->getActionId() == Activity::ACTION_PROMOTE_INNOVATION_VIEW &&
                (time() - $activity->getCreatedAt()->getTimestamp()) <= 604800 // 1 semaine en secondes
            );
        })->count();
    }

    /**
     * Get number of shares
     *
     * @return int
     */
    public function getNumberOfShares()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_SHARE;
        })->count();
    }

    /**
     * Get number of share this week
     *
     * @return int
     */
    public function getNumberOfSharesThisWeek()
    {
        if (!$this->getActivities()) {
            return 0;
        }
        return $this->getActivities()->filter(function ($activity) {
            return (
                $activity->getActionId() == Activity::ACTION_INNOVATION_SHARE &&
                (time() - $activity->getCreatedAt()->getTimestamp()) <= 604800 // 1 semaine en secondes
            );
        })->count();
    }

    /**
     * Get financial data by key.
     *
     * @param $key
     * @return FinancialData|null
     */
    public function getFinancialDataByKey($key)
    {
        if (!$this->getFinancialDatas()) {
            return null;
        }
        return $this->getFinancialDatas()->filter(function ($financialData) use ($key) {
            return $financialData->getKey() === $key;
        })->first();
    }

    /**
     * Get financial data by array_key.
     *
     * @param array $array_key
     * @return FinancialData|null
     */
    public function getFinancialDataByArrayKey($array_key)
    {
        if (!$this->getFinancialDatas()) {
            return array();
        }
        return $this->getFinancialDatas()->filter(function ($financialData) use ($array_key) {
            return (in_array($financialData->getKey(), $array_key));
        });
    }

    /**
     * Get performanceReviews by key.
     *
     * @param $key
     * @return PerformanceReview|null
     */
    public function getPerformanceReviewsByKey($key)
    {
        if (!$this->getPerformanceReviews()) {
            return null;
        }
        return $this->getPerformanceReviews()->filter(function ($performanceReview) use ($key) {
            return $performanceReview->getKey() == $key;
        })->first();
    }

    /**
     * Get latest volume.
     *
     * @param string|null $post_libelle
     * @return int|null|string
     */
    public function getLatestVolume($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'volume_' . $post_libelle;

        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getValue() : 0;
        return $value_1;
    }

    /**
     * Get latest net_sales.
     *
     * @param string|null $post_libelle
     * @return int|null|string
     */
    public function getLatestNetSales($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'net_sales_' . $post_libelle;

        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getCalculableValue() : 0;
        return $value_1;
    }

    /**
     * Get latest net_sales.
     *
     * @param string|null $post_libelle
     * @return int|null|string
     */
    public function getLatestContributingMargin($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'contributing_margin_' . $post_libelle;

        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getCalculableValue() : 0;
        return $value_1;
    }

    /**
     * Get latest total A&P.
     *
     * @param string|null $post_libelle
     * @return int|null|string
     */
    public function getLatestTotalAP($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $total = 0;
        // ABS(AP + CI)
        $libelle_ap_LEnY = "advertising_promotion_" . $post_libelle;
        $libelle_ci_LEnY = "central_investment_" . $post_libelle;

        $quarterly_data_ci_LEnY = $this->getFinancialDataByKey($libelle_ci_LEnY);
        $quarterly_data_ap_LEnY = $this->getFinancialDataByKey($libelle_ap_LEnY);
        $ci_value = ($quarterly_data_ci_LEnY) ? $quarterly_data_ci_LEnY->getCalculableValue() : 0;
        $ap_value = ($quarterly_data_ap_LEnY) ? $quarterly_data_ap_LEnY->getCalculableValue() : 0;
        $total_ap_value = $ci_value + $ap_value;
        $total += abs($total_ap_value);
        return round($total);
    }

    /**
     * Get last a volume.
     *
     * @param null $post_libelle
     * @return int|null|string
     */
    public function getLastAVolume($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'volume_' . $post_libelle;
        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getValue() : 0;
        return $value_1;
    }

    /**
     * Get last a Net sales.
     *
     * @param null $post_libelle
     * @return int|null|string
     */
    public function getLastANetSales($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'net_sales_' . $post_libelle;
        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getValue() : 0;
        return $value_1;
    }

    /**
     * Get last a Contributing Margin
     *
     * @param null $post_libelle
     * @return int|null|string
     */
    public function getLastAContributingMargin($post_libelle = null)
    {
        if (!$post_libelle) {
            return null;
        }
        $libelle = 'contributing_margin_' . $post_libelle;
        $financial_data = $this->getFinancialDataByKey($libelle);
        $value_1 = ($financial_data) ? $financial_data->getValue() : 0;
        return $value_1;
    }

    /**
     * Get explore all volumes.
     * @param Settings $settings
     * @return array
     */
    public function getExploreAllVolumes($settings)
    {
        $all_volume_keys = $settings->getFinancialsLibellesForExploreVolumes();
        $ret = array();
        foreach ($all_volume_keys as $key) {
            $date = $this->getInMarketDate();
            $year = ($date) ? intval($date->format('y')) : null;
            $proper_key = str_replace('_initial', '', $key);
            $proper_key = str_replace('_final', '', $proper_key);
            $proper_key = str_replace('_', ' ', $proper_key);
            $possible_value = $this->getFinancialDataByKey('volume_' . $key);
            if (!$possible_value && (!$year || (strpos($key, 'A') !== false && ($year + 1) > (intval(str_replace("A", "", $key)))))) {
                $ret[$proper_key] = "N/A";
            } else {
                $ret[$proper_key] = (!$possible_value) ? 'Missing data' : $possible_value->getValue();
            }
        }
        return $ret;
    }

    /**
     * Get financial graph dataset.
     * TODO : REFACTOR
     * @param Settings $settings
     * @param null $el_date
     * @return array
     */
    public function getFinancialGraphDataset($settings, $el_date = null)
    {
        $the_date = ($el_date) ? $el_date : $settings->getCurrentFinancialDate();
        // CUMULATIVE A_P
        $main_line_points = $this->getFinancialsLibellesForMainLineGraph($settings, $the_date);
        $second_line_points = $this->getFinancialsLibellesForSecondLineGraph($settings, $the_date);
        $old_b_line_points = $this->getFinancialsLibellesForOldBLineGraph($settings, $the_date);


        $datas = array(
            'main' => array(),
            'second' => array(),
            'old_b' => array()
        );
        foreach ($main_line_points as $line_point) {
            $datas['main'][$line_point] = $this->getBarDataById($settings, $line_point, $the_date);
        }
        foreach ($second_line_points as $line_point) {
            $datas['second'][$line_point] = $this->getBarDataById($settings, $line_point, $the_date);
        }
        foreach ($old_b_line_points as $line_point) {
            $datas['old_b'][$line_point] = $this->getBarDataById($settings, $line_point, $the_date);
        }

        $max_value = 0;
        $min_value = 0;
        foreach ($datas['main'] as $key => $data) {
            $max_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) > $max_value) ? abs(intval($data['ap'])) : $max_value;
            $max_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) > $max_value) ? abs(intval($data['cm'])) : $max_value;
            $min_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) < $min_value) ? abs(intval($data['ap'])) : $min_value;
            $min_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) < $min_value) ? abs(intval($data['cm'])) : $min_value;
        }
        foreach ($datas['second'] as $key => $data) {
            $max_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) > $max_value) ? abs(intval($data['ap'])) : $max_value;
            $max_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) > $max_value) ? abs(intval($data['cm'])) : $max_value;

            $min_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) < $min_value) ? abs(intval($data['ap'])) : $min_value;
            $min_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) < $min_value) ? abs(intval($data['cm'])) : $min_value;
        }

        foreach ($datas['old_b'] as $data) {
            $max_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) > $max_value) ? abs(intval($data['ap'])) : $max_value;
            $max_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) > $max_value) ? abs(intval($data['cm'])) : $max_value;

            $min_value = ($data['ap'] !== 'N/A' && abs(intval($data['ap'])) < $min_value) ? abs(intval($data['ap'])) : $min_value;
            $min_value = ($data['cm'] !== 'N/A' && abs(intval($data['cm'])) < $min_value) ? abs(intval($data['cm'])) : $min_value;
        }

        $max_value = ceil($max_value * 1.05);
        $min_value = ceil($min_value * 0.98);
        if ($min_value < 100) {
            $min_value = 0;
        }
        $the_main_data = array();
        $i = 0;
        foreach ($datas['main'] as $key => $data) {
            $x = $data['ap'];
            $y = $data['cm'];
            if ($x !== 'N/A' && $y !== 'N/A') {
                $x = abs(intval($x));
                $y = abs(intval($y));
                $the_main_data[$i] = array(
                    'x' => $x,
                    'y' => $y,
                    'caap' => $data['caap'],
                    'cm' => $data['cm'],
                    'ap' => $data['ap'],
                    'label' => Settings::getProperToDisplayLibelle($key)
                );
                $i++;
            }
        }

        $the_second_data = array();
        $i = 0;
        foreach ($datas['second'] as $key => $data) {
            $x = $data['ap'];
            $y = $data['cm'];
            if ($x !== 'N/A' && $y !== 'N/A') {
                $x = abs(intval($x));
                $y = abs(intval($y));
                $the_second_data[$i] = array(
                    'x' => $x,
                    'y' => $y,
                    'caap' => $data['caap'],
                    'cm' => $data['cm'],
                    'ap' => $data['ap'],
                    'label' => Settings::getProperToDisplayLibelle($key)
                );
                $i++;
            }
        }

        $the_old_b_data = array();
        $i = 0;
        foreach ($datas['old_b'] as $key => $data) {
            $x = $data['ap'];
            $y = $data['cm'];
            if ($x !== 'N/A' && $y !== 'N/A') {
                $x = abs(intval($x));
                $y = abs(intval($y));
                $the_old_b_data[$i] = array(
                    'x' => $x,
                    'y' => $y,
                    'caap' => $data['caap'],
                    'cm' => $data['cm'],
                    'ap' => $data['ap'],
                    'label' => Settings::getProperToDisplayLibelle($key)
                );
                $i++;
            }
        }

        $ret = array(
            array(
                'label' => 'good_line',
                'line_type' => 'main',
                'fill' => false,
                'borderColor' => "#61bbff",
                'borderCapStyle' => 'butt',
                'pointBorderColor' => "#61bbff",
                'pointBackgroundColor' => "#fff",
                'pointBorderWidth' => 3,
                'pointStyle' => "circle",
                'pointRadius' => 5,
                "spanGaps" => true,
                'data' => $the_main_data
            ),
            /*
            array(
                'label' => 'good_line',
                'line_type' => 'second',
                'fill' => false,
                'borderDash' => array(2, 5),
                'borderColor' => "#61bbff",
                'borderCapStyle' => 'butt',
                'pointBorderColor' => "#61bbff",
                'pointBackgroundColor' => "#fff",
                'pointBorderWidth' => 3,
                'pointStyle' => "circle",
                'pointRadius' => 5,
                "spanGaps" => true,
                'data' => $the_second_data
            ),*/
            array(
                'label' => 'good_line',
                'line_type' => 'old_b',
                'fill' => false,
                'borderColor' => "#8ca0b3",
                'borderCapStyle' => 'butt',
                'pointBorderColor' => "#8ca0b3",
                'pointBackgroundColor' => "#fff",
                'pointBorderWidth' => 2,
                'pointStyle' => "cross",
                'pointRadius' => 6,
                "spanGaps" => false,
                "showLine" => false,
                'data' => $the_old_b_data
            ),
            array(
                'label' => '',
                'line_type' => 'average',
                'fill' => false,
                'min_value' => $min_value,
                'max_value' => ($max_value * 1.05),
                'lineTension' => 0,
                'borderColor' => "#4f6e99",
                'pointBorderColor' => "#4f6e99",
                'pointBorderWidth' => 0,
                'borderWidth' => 1,
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'pointHoverBorderWidth' => 0,
                'borderJoinStyle' => 'miter',
                "spanGaps" => true,
                'data' => array(
                    array(
                        'x' => $min_value,
                        'y' => $min_value
                    ),
                    array(
                        'x' => $max_value,
                        'y' => $max_value
                    )
                )
            ),
        );
        return $ret;
    }

    /**
     * Get financial graph dataset for stepped market graph.
     *
     * @return array
     */
    public function getMarketSteppedData()
    {
        $activities = $this->getMarketsUpdateActivities();

        $created_at_string = $this->getCreatedAt()->format("Y-m").'-15';
        $infos_tooltip = array();
        $previous_infos = array(
            'date' => $created_at_string,
            'nb' => 0,
            'markets' => [],
            'movements' => 0,
            'market_movements' => array(
                'added' => [],
                'removed' => []
            )
        );
        $infos_tooltip[$created_at_string] = $previous_infos;
        foreach ($activities as $activity){
            $data_array = $activity->getDataArray();
            $created_at_string = $activity->getCreatedAt()->format("Y-m").'-15';
            $new_value = json_decode($data_array['new_value'], true);
            if(!array_key_exists($created_at_string, $infos_tooltip)){
                $infos = array(
                    'date' => $created_at_string,
                    'nb' => 0,
                    'markets' => [],
                    'movements' => 0,
                );
            } else {
                $infos = $infos_tooltip[$created_at_string];
            }
            $infos['nb'] = count($new_value);
            $infos['markets'] = $new_value;
            $infos_tooltip[$created_at_string] = $infos;
        }
        $infos = $infos_tooltip[$created_at_string];
        $infos['nb'] = count($this->getMarketsArray());
        $infos['markets'] = $this->getMarketsArray();
        $infos_tooltip[$created_at_string] = $infos;

        $data = array();
        $previous_infos = null;
        foreach ($infos_tooltip as $key => $info){
            $infos = $info;
            if($previous_infos && $previous_infos['date'] != $key){
                $infos['movements'] =  $infos['nb'] - $previous_infos['nb'];
                $infos['market_movements'] = array(
                    'added' => array_values(array_diff($infos['markets'], $previous_infos['markets'])),
                    'removed' => array_values(array_diff($previous_infos['markets'], $infos['markets']))
                );
            }
            $data[] = array(
                'x' => $info['date'],
                'y' => $info['nb']
            );
            $infos_tooltip[$key] = $infos;
            $previous_infos = $infos;
        }
        $ret = array(
            "datasets" => array(
                array(
                    'label' => 'stepped_line',
                    'steppedLine' => true,
                    'fill' => false,
                    'borderColor' => "#1eb3ea",
                    'borderCapStyle' => 'butt',
                    'pointBorderColor' => "#1eb3ea",
                    'pointBackgroundColor' => "#fff",
                    'pointBorderWidth' => 3,
                    'pointStyle' => "circle",
                    'pointRadius' => 5,
                    "spanGaps" => true,
                    'data' => $data,
                    'infos_tooltip' => $infos_tooltip
                )
            ),
        );
        return $ret;
    }


    /**
     * Get markets update activities
     *
     * @return array
     */
    public function getMarketsUpdateActivities()
    {
        if(!$this->getActivities()){
            return [];
        }
        $collection =  $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_UPDATED && \strpos($activity->getData(), '{"key":"markets_in"') !== false;
        })->toArray();
        return array_reverse($collection);
    }

    /**
     * Get last financial update activity
     *
     * @return Activity|null;
     */
    public function getLastFinancialUpdateActivity()
    {
        if(!$this->getActivities()){
            return null;
        }
        $financialActivity =  $this->getActivities()->filter(function ($activity) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_UPDATED &&
                (
                    \strpos($activity->getData(), '{"key":"volume_') !== false ||
                    \strpos($activity->getData(), '{"key":"net_sales_') !== false ||
                    \strpos($activity->getData(), '{"key":"contributing_margin_') !== false ||
                    \strpos($activity->getData(), '{"key":"central_investment_') !== false ||
                    \strpos($activity->getData(), '{"key":"advertising_promotion_') !== false ||
                    \strpos($activity->getData(), '{"key":"cogs_') !== false
                );
        })->first();
        return $financialActivity;
    }


    /**
     * Get financial updated_at (timestamp).
     *
     * @return int|null
     */
    public function getFinancialUpdatedAt()
    {
        $financialActivity = $this->getLastFinancialUpdateActivity();
        if(!$financialActivity){
            return null;
        }
        return $financialActivity->getCreatedAt()->getTimestamp();
    }


    /**
     * To financial array.
     *
     * @param Settings $settings
     * @return array
     */
    public function toFinancialArray($settings)
    {
        $ret = array();
        $ret['current_date'] = $settings->getCurrentFinancialDate();
        $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $ret['need_update'] = ($current_stage && !in_array($current_stage, array('discontinued', 'permanent_range'))) ? (!$this->isValidFinancialData($settings)) : false;
        $current_libelle = $settings->getLatestEstimateLibelle();
        $libelle_last_a = $settings->getLibelleLastA();
        $ret['data']['latest'] = $this->getConsolidationByPostLibelle($current_libelle);
        $ret['data']['latest_a'] = $this->getConsolidationByPostLibelle($libelle_last_a);
        $value_cumulative_a_p = $this->getCumulativeEstimateAP($settings);
        // cumul values
        $ret['data']['cumul'] = array(
            'volume' => $this->getCumulativeEstimateVol($settings),
            'total_ap' => $value_cumulative_a_p,
            'caap' => $this->getCumulativeEstimateCaap($settings, $value_cumulative_a_p)
        );
        if($this->isANewBusinessAcceleration()){
            $ret['data']['cumul']['investment'] = $this->getCumulativeEstimateInvestment($settings);
            $ret['data']['cumul']['revenue'] = $this->getCumulativeEstimateRevenue($settings);
        }

        // absolute values
        $ret['abs'] = array(
            'cumul' => array(
                'total_ap' => abs($ret['data']['cumul']['total_ap']),
                'caap' => abs($ret['data']['cumul']['caap'])
            ),
            'latest' => array(
                'total_ap' => abs($ret['data']['latest']['calc']['total_ap'])
            )
        );
        $ret['dataset'] = $this->getFinancialGraphDataset($settings);
        $ret['market_stepped_data'] = $this->getMarketSteppedData();
        $ret['explore_volume'] = array(
            'latest' => array(
                'saved' => $ret['data']['latest']['saved']['volume'],
                'calc' => $ret['data']['latest']['calc']['volume']
            ),
            'latest_a' => array(
                'saved' => $ret['data']['latest_a']['saved']['volume'],
                'calc' => $ret['data']['latest_a']['calc']['volume']
            )
        );
        $ret['explore_all_volumes'] = $this->getExploreAllVolumes($settings);
        $ret['is_incomplete'] = $this->isFinancialDataIncompleted($settings);
        return $ret;
    }

    /**
     * Get is next financial data not null.
     *
     * @param Settings $settings
     * @return bool
     */
    public function getIsNextFinancialDataNotNull(Settings $settings)
    {
        $the_date = $settings->getNextFinancialDate();
        $last_le = $settings->getLatestEstimateLibelle($the_date);
        if (!$last_le) {
            return false;
        }
        $financial_data = $this->getProperFinancialDatas($settings, $the_date);
        $keys = array('central_investment', 'advertising_promotion', 'contributing_margin', 'volume', 'net_sales');
        if ($financial_data && is_array($financial_data)) {
            foreach ($keys as $key) {
                $the_key = $key . '_' . $last_le;
                if (array_key_exists($the_key, $financial_data)) {
                    return $financial_data;
                }
            }
        }
        return false;
    }


    /**
     * Get proper financial datas.
     *
     * @param Settings $settings
     * @param string|null $date
     * @param bool $add_previous_helper_field
     * @return array|null
     */
    public function getProperFinancialDatas($settings, $date = null, $add_previous_helper_field = false)
    {
        if (!$date) {
            $date = $settings->getCurrentFinancialDate();
        }
        $id = $this->getId();
        $fields = $this->getFinancialDataFields($settings, $date, true, false, $add_previous_helper_field);
        $ret = array();

        $creationDatetime = new \DateTime($date);
        $date = $creationDatetime->getTimestamp() + 7200;
        $ret['innovation_id'] = $id;
        $ret['date'] = $date;


        if (count($fields['list']) > 0) {
            foreach ($fields['list'] as $field) {
                $financialData = $this->getFinancialDataByKey($field);
                if ($financialData) {
                    $ret[$financialData->getKey()] = $financialData->getValue();
                }
            }
        }
        return (count($ret) == 2) ? null : $ret;
    }


    /**
     * Get all proper financial datas for excel.
     *
     * @param Settings $settings
     * @param string|null $date
     * @param bool $add_previous_helper_field
     * @return array|null
     */
    public function getAllProperFinancialDatasForExcel($settings, $date = null)
    {
        if (!$date) {
            $date = $settings->getCurrentFinancialDate();
        }

        $ret = array();
        $last_a = $settings->getLibelleLastA($date, true);
        $max_year = intval(str_replace('A', '', $last_a));
        $ret_dates = array();
        $ret_post_libelles = array();
        for ($year = 15; $year < $max_year; $year++) {
            $ret_dates[] = 'A' . $year;
            $ret_post_libelles[] = 'A' . $year;
        }
        if (!in_array($last_a, $ret_dates)) {
            $ret_dates[] = $last_a;
            $ret_post_libelles[] = 'A' . $year;
        }
        $last_b = "";
        $post_fields = $settings->getFinancialDataPostFields($date, true, true);
        foreach ($post_fields as $key => $value) {
            $libelle = str_replace(' (final)', '_final', $key);
            $libelle = str_replace(' (initial)', '_initial', $libelle);
            $libelle = str_replace(' ', '_', $libelle);

            $key = str_replace(' (final)', '', $key);
            $key = str_replace(' (initial)', '', $key);
            if (!in_array($key, $ret_dates)) {
                if ($key != 'N/A') {
                    $ret_dates[] = $key;
                    $last_b = $key;
                    $ret_post_libelles[] = $libelle;
                }
            }
        }

        $all_libelles = array();
        $pre_libelle_indexes = array('volume_', 'net_sales_', 'contributing_margin_', 'advertising_promotion_', 'central_investment_', 'caap_');
        $pre_key_indexes = array('Vol', 'NS', 'CM', 'A&P', 'CI', '', 'CAAP');
        foreach ($pre_key_indexes as $pre_index) {
            foreach ($ret_dates as $ret_date) {
                $ret[$pre_index . ' ' . $ret_date] = "";
            }
        }
        foreach ($pre_libelle_indexes as $pre_index) {
            foreach ($ret_post_libelles as $ret_post_libelle) {
                $all_libelles[] = $pre_index . $ret_post_libelle;
            }
        }
        $financialDatas = $this->getFinancialDataByArrayKey($all_libelles);
        foreach ($financialDatas as $financialData) {
            $key = $financialData->getProperKey();
            if (array_key_exists($key, $ret)) {
                $ret[$key] = $financialData->getCalculableValue(true);
            }
        }
        $last_ret_date = '';
        // Gestion du CAAP et de Total A&P
        foreach ($ret_dates as $ret_date) {
            // CAAP : Contributive Margin – Central Investment (brand owner) – A&P (markets)
            $contributing_margin_value = ($ret['CM ' . $ret_date] === "N/A") ? 0 : $ret['CM ' . $ret_date];
            $central_investment_value = ($ret['CI ' . $ret_date] === "N/A") ? 0 : $ret['CI ' . $ret_date];
            $advertising_promotion_value = ($ret['A&P ' . $ret_date] === "N/A") ? 0 : $ret['A&P ' . $ret_date];
            $total_ap = 0;
            $caap = 0;
            if (is_numeric($central_investment_value)) {
                $total_ap += $central_investment_value;
            }
            if (is_numeric($advertising_promotion_value)) {
                $total_ap += $advertising_promotion_value;
            }
            if (is_numeric($contributing_margin_value)) {
                $caap += $contributing_margin_value;
            }
            $caap -= abs($total_ap);

            $ret['CAAP ' . $ret_date] = $caap;
            $ret[' ' . $ret_date] = $total_ap;
            $last_ret_date = $ret_date;
        }
        $contributing_margin_value_is_valid = ($ret['CM ' . $last_ret_date] !== '' && $ret['CM ' . $last_ret_date] !== 'N/A');
        $net_sales_value_is_valid = ($ret['NS ' . $last_ret_date] !== '' && $ret['NS ' . $last_ret_date] !== 'N/A');
        $central_investment_value_is_valid = ($ret['CI ' . $last_ret_date] !== '' && $ret['CI ' . $last_ret_date] !== 'N/A');
        $advertising_promotion_value_is_valid = ($ret['A&P ' . $last_ret_date] !== '' && $ret['A&P ' . $last_ret_date] !== 'N/A');

        $total_ap = ($central_investment_value_is_valid && $advertising_promotion_value_is_valid) ?  $ret['CI ' . $last_ret_date] + $ret['A&P ' . $last_ret_date] : 0;
        $ap_ns = ($net_sales_value_is_valid && $total_ap != 0 && $ret['NS ' . $last_ret_date] != 0) ? $total_ap / $ret['NS ' . $last_ret_date] : '';
        $cm_ns = ($contributing_margin_value_is_valid && $net_sales_value_is_valid && $ret['CM ' . $last_ret_date] != 0 && $ret['NS ' . $last_ret_date] != 0) ? $ret['CM ' . $last_ret_date] / $ret['NS ' . $last_ret_date] : "";
        // LAST B A&P/NS
        $ret['A&P/NS'] = abs($ap_ns);
        $ret['CM/NS'] = $cm_ns;

        return $ret;
    }


    /**
     * Get financial data fields.
     * TODO : REFACTOR
     *
     * @param Settings $settings
     * @param string|null $date
     * @param bool $edition
     * @param bool $add_NA_fields
     * @param bool $add_previous_helper_field
     * @return array
     */
    function getFinancialDataFields($settings, $date = null, $edition = true, $add_NA_fields = false, $add_previous_helper_field = false)
    {
        $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : 'empty';
        $is_a_service = $this->isAService();
        $is_a_nba = $this->isANewBusinessAcceleration();
        $ret = array(
            'to_display_list' => array(),
            'list' => array(),
            'details' => array(),
        );
        $post_fields = $settings->getFinancialDataPostFields($date, $edition, $add_NA_fields, $add_previous_helper_field);
        if ($is_a_nba){
            $pre_fields = array('investment', 'revenue');
        }else if ($is_a_service) {
            $pre_fields = array('central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'ideate' || $current_stage == 'discover') {
            $pre_fields = array('central_investment');
        } elseif ($current_stage == 'experiment') {
            $pre_fields = array('volume', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'incubate') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'scale_up') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'empty') {
            return $ret;
        } elseif ($current_stage == 'permanent_range') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } elseif ($current_stage == 'discontinued') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion', 'cogs');
        } else {
            return $ret;
        }

        foreach ($post_fields as $post_libelle => $field) {
            $tr_libelle = (array_key_exists('libelle', $field)) ? $field['libelle'] : $post_libelle;
            $libelle = $post_libelle;
            $id = str_replace(' ', '_', $libelle);
            $id = str_replace('(', '', $id);
            $id = str_replace(')', '', $id);
            $ret['to_display_list'][] = array('libelle' => $tr_libelle, 'type' => 'tr', 'id' => $id);
            foreach ($pre_fields as $pre_field) {
                $libelle = $pre_field . '_' . $post_libelle;
                $id = str_replace(' ', '_', $libelle);
                $id = str_replace('(', '', $id);
                $id = str_replace(')', '', $id);
                $ret['list'][] = $id;
                $ret['to_display_list'][] = array('libelle' => $libelle, 'id' => $id, 'type' => 'td', 'infos' => $field, 'placeholder' => $settings->getFinancialDataPlaceholder($pre_field, $post_libelle));
                $ret['details'][$libelle] = $field;
            }
        }
        return $ret;
    }

    /**
     * Get financial libelles for main line graph.
     *
     * @param Settings $settings
     * @param string|null $el_date
     * @return array
     */
    public function getFinancialsLibellesForMainLineGraph($settings, $el_date = null)
    {
        $ret = array();
        $start_date_year = ($this->getStartDate()) ? $this->getStartDate()->format('Y') - 2000 : 15;
        $the_date = ($el_date) ? $el_date : $settings->getCurrentFinancialDate();
        $last_a = $settings->getLibelleLastA(null, true);
        $max_year = intval(str_replace('A', '', $last_a));
        for ($year = $start_date_year; $year < $max_year; $year++) {
            $ret[] = 'A' . $year;
        }
        if (!in_array($last_a, $ret)) {
            $ret[] = $last_a;
        }
        $last_le = $settings->getLatestEstimateLibelle($the_date, false);
        if ($last_le) {
            $ret[] = $last_le;
        }
        return $ret;
    }

    /**
     * Get financials libelles for second line graph.
     * TODO : REFACTOR
     *
     * @param Settings $settings
     * @param string|null $el_date
     * @return array
     */
    public function getFinancialsLibellesForSecondLineGraph($settings, $el_date = null)
    {
        $ret = array();
        $the_date = ($el_date) ? $el_date : $settings->getCurrentFinancialDate();
        // premier point
        $main_line = $this->getFinancialsLibellesForMainLineGraph($settings, $the_date);
        $id = count($main_line) - 1;
        if (strpos($main_line[$id], 'LE') !== false) {
            $id -= 1;
        }
        if ($id >= 0) {
            $ret[] = $main_line[$id];
        }

        if (count($ret) == 1) {
            $value = $ret[0];
            $year = intval((str_replace('A', '', $value))) + 1;
            $ret[] = 'B' . $year;
        }
        $the_date_explode = explode('-', $the_date);
        $date_now_year = intval($the_date_explode[0]);
        $trimestre = $settings->getTrimesterByFinancialDate($the_date);
        if ($trimestre == 4) {
            $the_year = ($trimestre == 3) ? $date_now_year + 1 : $date_now_year;
            $the_year += 1;
            $the_year -= 2000;
            $ret[] = 'B' . $the_year;
        }
        return $ret;
    }

    /**
     * Get financials libelles for old b line graph.
     * TODO : REFACTOR
     *
     * @param Settings $settings
     * @param string|null $el_date
     * @return array
     */
    public function getFinancialsLibellesForOldBLineGraph($settings, $el_date = null)
    {
        $ret = array();
        $start_date_year = ($this->getStartDate()) ? $this->getStartDate()->format('Y') : 2015;
        $the_date = ($el_date) ? $el_date : $settings->getCurrentFinancialDate();
        $the_date_explode = explode('-', $the_date);
        $date_now_month = intval($the_date_explode[1]);
        $date_now_year = intval($the_date_explode[0]);
        $last_year = null;
        if ($date_now_month > 7) { // après juin, le A courant doit être saisie peut être saisie
            $date_now_year++;
        }
        for ($a = $start_date_year; $a < $date_now_year; $a++) {
            $simple_year = $a - 2000;
            $last_year = $simple_year;
            $ret[] = 'B' . $simple_year;
        }
        $main_line = $this->getFinancialsLibellesForMainLineGraph($settings, $the_date);
        $id = count($main_line) - 1;
        if (strpos($main_line[$id], 'LE') !== false) {
            $id -= 1;
        }
        if (strpos($main_line[$id], 'B') !== false) {
            $id -= 2;
        }
        if ($id >= 0) {
            $value = $main_line[$id];
            if (strpos($main_line[$id], '_final') !== false || strpos($main_line[$id], '_initial') !== false) {
                $ret[] = str_replace('_initial', '', (str_replace('_final', '', $value)));
            } else {
                $the_value = str_replace('B', '', (str_replace('_initial', '', (str_replace('_final', '', (str_replace('A', '', $value)))))));
                $year = intval($the_value) + 1;
                while ($last_year < $year) {
                    $last_year += 1;
                    $ret[] = 'B' . $last_year;
                }

            }
        }
        $the_date_explode = explode('-', $the_date);
        $date_now_year = intval($the_date_explode[0]);
        $trimestre = $settings->getTrimesterByFinancialDate($the_date);
        if ($trimestre == 4) {
            $the_year = ($trimestre == 3) ? $date_now_year + 1 : $date_now_year;
            $the_year += 1;
            $the_year -= 2000;
            $ret[] = 'B' . $the_year;
        }

        return $ret;
    }

    /**
     * Get bar data by id.
     * TODO : REFACTOR
     *
     * @param Settings $settings
     * @param string $id
     * @param string|null $el_date
     * @return array
     */
    public function getBarDataById($settings, $id, $el_date = null)
    {
        $the_date = ($el_date) ? $el_date : $settings->getCurrentFinancialDate();
        $ret = array(
            'volume' => 0,
            'true_volume' => null,
            'cm' => 0,
            'net_sales' => 0,
            'ap' => 0,
            'caap' => 0
        );
        if (strpos($id, 'B') !== false && strpos($id, '_initial') === false && strpos($id, '_final') === false) {
            $id = $id . $settings->getBudgetLibelleForDate($the_date, $id);
        }

        $central_investment_value = $this->getFinancialDataByKey('central_investment_' . $id);
        $central_investment_value_is_valid = ($central_investment_value);
        $central_investment_value = ($central_investment_value) ? $central_investment_value->getCalculableValue() : 0;

        $advertising_promotion_value = $this->getFinancialDataByKey('advertising_promotion_' . $id);
        $advertising_promotion_value_is_valid = ($advertising_promotion_value);
        $advertising_promotion_value = ($advertising_promotion_value) ? $advertising_promotion_value->getCalculableValue() : 0;


        $contributing_margin_value = $this->getFinancialDataByKey('contributing_margin_' . $id);
        $contributing_margin_value_is_valid = ($contributing_margin_value);
        $contributing_margin_value = ($contributing_margin_value) ? $contributing_margin_value->getCalculableValue() : 0;


        $volume_value = $this->getFinancialDataByKey('volume_' . $id);
        $true_volume_value = (!$volume_value) ? 'Missing data' : $volume_value->getValue();
        $volume_value = (!$volume_value || ($volume_value && $volume_value->getValue() === "N/A")) ? 0 : $volume_value->getValue();
        $volume_value = ($volume_value === '') ? "N/A" : $volume_value;


        $net_sales_value = $this->getFinancialDataByKey('net_sales_' . $id);
        $net_sales_value = (!$net_sales_value || ($net_sales_value && $net_sales_value->getValue() === "N/A")) ? 0 : $net_sales_value->getValue();
        $net_sales_value = ($net_sales_value === '') ? "N/A" : $net_sales_value;

        $ret['volume'] = ($volume_value == 'N/A') ? 0 : $volume_value;
        $ret['true_volume'] = $true_volume_value;
        $ret['net_sales'] = ($net_sales_value == 'N/A') ? 0 : $net_sales_value;
        $ret['cm'] = ($contributing_margin_value_is_valid) ? $contributing_margin_value : "N/A";
        $ret['ap'] = ($central_investment_value_is_valid) ? $central_investment_value : "N/A";
        if ($advertising_promotion_value_is_valid) {
            $ret['ap'] = ($ret['ap'] == 'N/A') ? $advertising_promotion_value : $ret['ap'] + $advertising_promotion_value;
        }
        $ret['caap'] = ($contributing_margin_value_is_valid) ? $contributing_margin_value : "N/A";
        if ($ret['ap'] != 'N/A') {
            $ret['caap'] = ($ret['caap'] == 'N/A') ? -abs($ret['ap']) : $ret['caap'] - abs($ret['ap']);
        }
        return $ret;
    }


    /**
     * Calculate value evol volume versus last a.
     *
     * @param Settings $settings
     * @param string|null $date
     * @return float
     */
    public function calculateEvolVolumeVersusLastA(Settings $settings, $date = null)
    {

        $current_volume = $this->calculateVolume($settings, $date);
        $libelle_vol_LEnY_1 = "volume_" . $settings->getLibelleLastA($date, true);
        $last_volume = 0;

        $quarterly_data_vol_LEnY = $this->getFinancialDataByKey($libelle_vol_LEnY_1);
        $last_volume += ($quarterly_data_vol_LEnY) ? $quarterly_data_vol_LEnY->getCalculableValue() : 0;

        return $settings->specialRoundBy3($settings->getPercentEvolutionBetweenTwoValues($last_volume, $current_volume));
    }

    /**
     * Calculate volume.
     *
     * @param Settings $settings
     * @param string|null $date
     * @return float
     */
    public function calculateVolume($settings, $date = null)
    {
        $libelle_vol_LEnY = "volume_" . $settings->getLibelleLastEstimateNextYear($date);
        $total = 0;
        $quarterly_data_vol_LEnY = $this->getFinancialDataByKey($libelle_vol_LEnY);
        $total += ($quarterly_data_vol_LEnY) ? $quarterly_data_vol_LEnY->getCalculableValue() : 0;
        return round($total);
    }

    /**
     * Calculate CAAP.
     *
     * @param Settings $settings
     * @param string|null $date
     * @return float
     */
    public function calculateCAAP($settings, $date = null)
    {
        $libelle_cm_LEnY = "contributing_margin_" . $settings->getLibelleLastEstimateNextYear($date);
        $total_cm = 0;
        $quarterly_data_cm_LEnY = $this->getFinancialDataByKey($libelle_cm_LEnY);
        $cm_value = ($quarterly_data_cm_LEnY) ? $quarterly_data_cm_LEnY->getCalculableValue() : 0;
        $total_cm += $cm_value;
        $total_ap = abs($this->calculateTotalAP($settings, $date));
        $total = $total_cm - $total_ap;
        return round($total);
    }

    /**
     * Calculate Total AP.
     *
     * @param Settings $settings
     * @param string|null $date
     * @return float
     */
    public function calculateTotalAP($settings, $date = null)
    {
        $total = 0;
        // ABS(AP + CI)
        $libelle_ap_LEnY = "advertising_promotion_" . $settings->getLibelleLastEstimateNextYear($date);
        $libelle_ci_LEnY = "central_investment_" . $settings->getLibelleLastEstimateNextYear($date);

        $quarterly_data_ci_LEnY = $this->getFinancialDataByKey($libelle_ci_LEnY);
        $quarterly_data_ap_LEnY = $this->getFinancialDataByKey($libelle_ap_LEnY);
        $ci_value = ($quarterly_data_ci_LEnY) ? $quarterly_data_ci_LEnY->getCalculableValue() : 0;
        $ap_value = ($quarterly_data_ap_LEnY) ? $quarterly_data_ap_LEnY->getCalculableValue() : 0;
        $total_ap_value = $ci_value + $ap_value;
        $total += abs($total_ap_value);
        return round($total);
    }


    /**
     * Is financial data incompleted
     *
     * @param Settings $settings
     * @param string|null $the_date
     * @return bool
     */
    public function isFinancialDataIncompleted($settings, $the_date = null)
    {
        if (!$the_date) {
            $the_date = $settings->getCurrentFinancialDate();
        }
        $financial_data = $this->getProperFinancialDatas($settings, $the_date);
        $fields = $this->getFinancialDataFields($settings, $the_date, true, true);
        if (!$financial_data || ($financial_data && !is_array($financial_data))) {
            return true;
        }
        if ($financial_data && is_array($financial_data)) {
            foreach ($fields['list'] as $field) {
                if (!array_key_exists($field, $financial_data)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get cumulative estimate AP.
     *
     * @param Settings $settings
     * @param bool $special
     * @return int
     */
    public function getCumulativeEstimateAP($settings, $special = false)
    {
        $total = 0;
        $criteria = Criteria::create()->where(Criteria::expr()->contains("key", 'advertising_promotion_A'))
            ->orWhere(Criteria::expr()->contains("key", 'central_investment_A'));
        $financialDatas = $this->getFinancialDatas()->matching($criteria);
        foreach ($financialDatas as $financialData) {
            $total += $financialData->getCalculableValue();
        }
        $after_libelle = (!$special) ? $settings->getLibelleLastEstimateNextYear() : $settings->getLibelleBudgetNextYear();
        $libelle = 'advertising_promotion_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        if ($quarterly_data && $quarterly_data->getCalculableValue()) {
            $total += $quarterly_data->getCalculableValue();
        }
        $libelle = 'central_investment_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        if ($quarterly_data) {
            $total += $quarterly_data->getCalculableValue();
        }
        return $total;
    }


    /**
     * Get cumulative estimate Volume.
     *
     * @param Settings $settings
     * @param bool $special
     * @return int
     */
    public function getCumulativeEstimateVol($settings, $special = false)
    {
        $total = 0;
        $criteria = Criteria::create()->where(Criteria::expr()->contains("key", 'volume_A'));
        $financialDatas = $this->getFinancialDatas()->matching($criteria);
        foreach ($financialDatas as $financialData) {
            $total += abs($financialData->getCalculableValue());
        }
        $after_libelle = (!$special) ? $settings->getLibelleLastEstimateNextYear() : $settings->getLibelleBudgetNextYear();
        $libelle = 'volume_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        if ($quarterly_data) {
            $total += abs($quarterly_data->getCalculableValue());
        }
        return $total;
    }


    /**
     * Get cumulative estimate Investment.
     *
     * @param Settings $settings
     * @param bool $special
     * @return int
     */
    public function getCumulativeEstimateInvestment($settings, $special = false)
    {
        $total = 0;
        $criteria = Criteria::create()->where(Criteria::expr()->startsWith("key", 'investment_A'));
        $financialDatas = $this->getFinancialDatas()->matching($criteria);
        foreach ($financialDatas as $financialData) {
            $total += abs($financialData->getCalculableValue());
        }
        $after_libelle = (!$special) ? $settings->getLibelleLastEstimateNextYear() : $settings->getLibelleBudgetNextYear();
        $libelle = 'investment_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        if ($quarterly_data) {
            $total += abs($quarterly_data->getCalculableValue());
        }
        return $total;
    }

    /**
     * Get cumulative estimate Revenue.
     *
     * @param Settings $settings
     * @param bool $special
     * @return int
     */
    public function getCumulativeEstimateRevenue($settings, $special = false)
    {
        $total = 0;
        $criteria = Criteria::create()->where(Criteria::expr()->startsWith("key", 'revenue_A'));
        $financialDatas = $this->getFinancialDatas()->matching($criteria);
        foreach ($financialDatas as $financialData) {
            $total += abs($financialData->getCalculableValue());
        }
        $after_libelle = (!$special) ? $settings->getLibelleLastEstimateNextYear() : $settings->getLibelleBudgetNextYear();
        $libelle = 'revenue_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        if ($quarterly_data) {
            $total += abs($quarterly_data->getCalculableValue());
        }
        return $total;
    }

    /**
     * Get cumulative estimate CAAP.
     *
     * @param Settings $settings
     * @param null $cumul_total_ap
     * @param bool $special
     * @return int
     */
    public function getCumulativeEstimateCaap($settings, $cumul_total_ap = null, $special = false)
    {

        // Contributive Margin – ABS(Total A&P)
        if (!$cumul_total_ap) {
            $cumul_total_ap = $this->getCumulativeEstimateAP($settings, $special);
        }
        $contributive_margin = 0;
        $criteria = Criteria::create()->where(Criteria::expr()->contains("key", 'contributing_margin_A'));
        $financialDatas = $this->getFinancialDatas()->matching($criteria);
        foreach ($financialDatas as $financialData) {
            $contributive_margin += $financialData->getCalculableValue();
        }
        $after_libelle = (!$special) ? $settings->getLibelleLastEstimateNextYear() : $settings->getLibelleBudgetNextYear();
        $libelle = 'contributing_margin_' . $after_libelle;
        $quarterly_data = $this->getFinancialDataByKey($libelle);
        $value = 0;
        if ($quarterly_data) {
            $contributive_margin += $quarterly_data->getCalculableValue();
        }

        $total = $contributive_margin - abs($cumul_total_ap);

        return $total;
    }

    /**
     * Get explore pictures.
     *
     * @param null|object $liip
     * @param bool $detail_order
     * @param bool $full_size
     * @param bool $with_packshot
     * @param bool $with_default
     * @return array
     */
    function getExplorePictures($liip = null, $detail_order = false, $full_size = false, $with_packshot = true, $with_default = true)
    {
        $ret = array();
        $need_packshot = ($this->isAProduct());
        $need_other_images = true;
        if (!$detail_order && $with_packshot && $need_packshot) {
            if ($this->getPackshotPicture()) {
                if ($full_size) {
                    $url = $this->getPackshotPicture()->resizeImage($liip, 'thumbnail_picture');
                    $ret[] = $url;
                } else {
                    $url = $this->getPackshotPicture()->resizeImage($liip, 'thumbnail_explore_list');
                    $ret[] = $url;
                }
            }
        }
        if ($this->getBeautyshotPicture()) {
            if ($full_size) {
                $url = $this->getBeautyshotPicture()->resizeImage($liip, 'thumbnail_picture');
                $ret[] = $url;
            } else {
                $url = $this->getBeautyshotPicture()->resizeImage($liip, 'thumbnail_explore_list');
                $ret[] = $url;
            }
        }
        if ($detail_order && $with_packshot && $need_packshot) {
            if ($this->getPackshotPicture()) {
                if ($full_size) {
                    $url = $this->getPackshotPicture()->resizeImage($liip, 'thumbnail_picture');
                    $ret[] = $url;
                } else {
                    $url = $this->getPackshotPicture()->resizeImage($liip, 'thumbnail_explore_list');
                    $ret[] = $url;
                }
            }
        }
        if ($need_other_images) {
            foreach ($this->getAdditionalPictures() as $additionalPicture) {
                if ($additionalPicture->getPicture()) {
                    if ($full_size) {
                        $url = $additionalPicture->getPicture()->resizeImage($liip, 'thumbnail_picture');
                        $ret[] = $url;
                    } else {
                        $url = $additionalPicture->getPicture()->resizeImage($liip, 'thumbnail_explore_list');
                        $ret[] = $url;
                    }
                }
            }
        }
        if ($with_default && count($ret) == 0) {
            $ret[] = $this->getDefaultPicture();
        }
        return $ret;
    }

    /**
     * Get promote email picture url.
     *
     * @return string
     */
    public function getPromoteEmailPictureUrl()
    {
        $explore_pictures = $this->getExplorePictures();
        return $explore_pictures[0];
    }

    /**
     * Is valid financial data.
     *
     * @param Settings $settings
     * @param string|null $date
     * @return bool
     */
    public function isValidFinancialData($settings, $date = null)
    {
        if (!$date) {
            $date = $settings->getCurrentFinancialDate();
        }
        $financial_data = $this->getProperFinancialDatas($settings, $date);
        if (!$financial_data) {
            return false;
        }
        $fields = $this->getFinancialDataFields($settings, $date);

        $total = 0;
        $count = 0;
        foreach ($fields['to_display_list'] as $field) {
            if ($field['type'] == 'td') {
                if (!$field['infos']['disabled'] && $field['infos']['mandatory']) {
                    $the_field = $field['libelle'];
                    $the_field = str_replace(' ', '_', $the_field);
                    $the_field = str_replace('(', '', $the_field);
                    $the_field = str_replace(')', '', $the_field);
                    $total++;
                    if (array_key_exists($the_field, $financial_data) && $financial_data[$the_field] !== '') {
                        $count++;
                    }
                }
            }
        }
        return ($count == $total);
    }

    /**
     * Get total_volume for filter.
     *
     * @param $total_volume
     * @return int
     */
    public function getTotalVolumeForFilter($total_volume)
    {
        $nb_value = intval($total_volume);
        if ($total_volume === '' || $total_volume === null) {
            return -1500;
        } elseif ($nb_value === 0) {
            return -1000;
        } elseif ($total_volume == 'N/A') {
            return -500;
        } else {
            return $nb_value;
        }
    }

    /**
     * Get total budget next year.
     *
     * @param $settings
     * @return int
     */
    public function getTotalBudgetNextYear(Settings $settings)
    {
        $total = 0;
        $post_libelle = $settings->getLibelleBudgetNextYear();
        $financialData = $this->getFinancialDataByKey('central_investment_' . $post_libelle);
        if ($financialData) {
            $total += $financialData->getCalculableValue();
        }
        if ($this->getStage()->getCssClass() != 'ideate' && $this->getStage()->getCssClass() != 'discover') {
            $financialData = $this->getFinancialDataByKey('advertising_promotion_' . $post_libelle);
            if ($financialData) {
                $total += $financialData->getCalculableValue();
            }
        }
        return $total;
    }

    /**
     * Get consolidation by post libelle.
     *
     * @param string $post_libelle
     * @return array
     */
    public function getConsolidationByPostLibelle($post_libelle)
    {
        $libelle_v = 'volume_' . $post_libelle;
        $quarterly_data_v = $this->getFinancialDataByKey($libelle_v);
        $value_v = ($quarterly_data_v) ? $quarterly_data_v->getCalculableValue() : 0;
        $pure_v = ($quarterly_data_v) ? $quarterly_data_v->getValue() : null;

        $libelle_ns = 'net_sales_' . $post_libelle;
        $quarterly_data_ns = $this->getFinancialDataByKey($libelle_ns);
        $value_ns = ($quarterly_data_ns) ? $quarterly_data_ns->getCalculableValue() : 0;
        $pure_ns = ($quarterly_data_ns) ? $quarterly_data_ns->getValue() : null;


        $libelle_ci = 'central_investment_' . $post_libelle;
        $quarterly_data_ci = $this->getFinancialDataByKey($libelle_ci);
        $value_ci = ($quarterly_data_ci) ? $quarterly_data_ci->getCalculableValue() : 0;
        $pure_ci = ($quarterly_data_ci) ? $quarterly_data_ci->getValue() : null;

        $libelle_ap = 'advertising_promotion_' . $post_libelle;
        $quarterly_data_ap = $this->getFinancialDataByKey($libelle_ap);
        $value_ap = ($quarterly_data_ap) ? $quarterly_data_ap->getCalculableValue() : 0;
        $pure_ap = ($quarterly_data_ap) ? $quarterly_data_ap->getValue() : null;

        $libelle_cm = 'contributing_margin_' . $post_libelle;
        $quarterly_data_cm = $this->getFinancialDataByKey($libelle_cm);
        $value_cm = ($quarterly_data_cm) ? $quarterly_data_cm->getCalculableValue() : 0;
        $pure_cm = ($quarterly_data_cm) ? $quarterly_data_cm->getValue() : null;

        $libelle_cogs = 'cogs_' . $post_libelle;
        $quarterly_data_cogs = $this->getFinancialDataByKey($libelle_cogs);
        $value_cogs = ($quarterly_data_cogs) ? $quarterly_data_cogs->getCalculableValue() : 0;
        $pure_cogs = ($quarterly_data_cogs) ? $quarterly_data_cogs->getValue() : null;

        $result = array(
            'calc' => array(
                'volume' => $value_v,
                'net_sales' => $value_ns,
                'contributing_margin' => $value_cm,
                'central_investment' => $value_ci,
                'advertising_promotion' => $value_ap,
                'cogs' => $value_cogs,
                'total_ap' => 0,
                'caap' => 0
            ),
            'saved' => array(
                'volume' => $pure_v,
                'net_sales' => $pure_ns,
                'contributing_margin' => $pure_cm,
                'central_investment' => $pure_ci,
                'advertising_promotion' => $pure_ap,
                'cogs' => $pure_cogs
            ),
        );
        $result['calc']['total_ap'] = $value_ci + $value_ap;
        $result['calc']['caap'] = $value_cm - abs($value_ci + $value_ap);
        return $result;
    }

    /**
     * Get latest consolidation.
     *
     * @param Settings $settings
     * @return array
     */
    public function getLatestConsolidation(Settings $settings)
    {
        $post_libelle = $settings->getLatestEstimateLibelle();
        return $this->getConsolidationByPostLibelle($post_libelle);
    }


    /**
     * Get picto stage innovation dashboard.
     *
     * @param string|null $current_stage
     * @param bool $is_frozen
     * @return string
     */
    function getPictoStageInnovationDashboard($current_stage = null, $is_frozen = false)
    {
        if (!$current_stage) {
            $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : 'empty';
        }
        switch ($current_stage) {
            case "discover":
                return "stage-dis-large@2x.png";
            case "ideate":
                return "stage-ide-large@2x.png";
            case "experiment":
                return "stage-exp-large@2x.png";
            case "incubate":
                return "stage-inc-large@2x.png";
            case "scale_up":
                return "stage-sca-large@2x.png";
            case "permanent_range":
                return "stage-permanent-large@2x.png";
            case "discontinued":
                return "stage-discont-large@2x.png";
            case "frozen":
                return "fro-light.png";
            default:
                return "stage-none-large@2x.png";
        }
    }

    /**
     * Get relative in_market_date date.
     *
     * @param bool $only_time
     * @param bool $minimal
     * @return string
     */
    public function getRelativeInMarketDate()
    {
        $date = $this->getInMarketDate();
        if (!$date) {
            return null;
        }
        return Activity::getRelativeDate($date);
    }

    /**
     * To dashboard array.
     *
     * @return array
     */
    public function toDashboardArray()
    {
        $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $infos = array(
            'stage' => $current_stage,
            'stage_name' => '',
            'stage_icon_class' => 'light ',
            'user' => (($this->getContact()) ? $this->getContact()->getProperUsername() : ''),
            'innovation_name' => $this->getTitle(),
            'created_at' => (($this->getInMarketDate()) ? $this->getInMarketDate()->getTimestamp() : null),
            'relative_created' => $this->getRelativeInMarketDate(),
            'placeholder_date' => (($this->getInMarketDate()) ? $this->getInMarketDate()->setTimezone(new \DateTimeZone('GMT'))->format("m/d/Y") : null),
            'entity_brand' => (($this->getEntity()) ? $this->getEntity()->getTitle() : ''),
            'innovation_url' => $this->getInnovationUrl(),
        );
        $infos['stage_icon_class'] .= $infos['stage'];
        $infos['stage_name'] = ($this->getStage()) ? $this->getStage()->getTitle() : '';
        if ($this->getIsFrozen()) {
            $infos['stage_name'] .= ' (frozen)';
            $infos['stage_icon_class'] .= ' frozen';
        }
        if ($this->getBrand()) {
            $infos['entity_brand'] .= ($infos['entity_brand'] != '') ? ', ' . $this->getBrand()->getTitle() : $this->getBrand()->getTitle();
        }
        return $infos;
    }

    /**
     * Is enabled on explore.
     *
     * @return bool
     */
    public function isEnabledOnExplore()
    {
        return (
            $this->getStage() && in_array($this->getStage()->getId(), Stage::getExploreStageIds()) &&
            !$this->getIsFrozen() &&
            $this->isAProduct()
        );
    }

    /**
     * array is enabled on explore.
     *
     * @param $innovation_array
     * @return bool
     */
    public static function arrayIsEnabledOnExplore($innovation_array)
    {
        return (
            in_array($innovation_array['current_stage_id'], Stage::getExploreStageIds()) &&
            !$innovation_array['is_frozen'] &&
            $innovation_array['proper']['classification_type'] == 'product'
        );
    }

    /**
     * Is a product.
     *
     * @return bool
     */
    public function isAProduct()
    {
        return $this->getClassification() && $this->getClassification()->getId() == Classification::CLASSIFICATION_ID_PRODUCT;
    }

    /**
     * Is a service.
     *
     * @return bool
     */
    public function isAService()
    {
        return $this->getClassification() && $this->getClassification()->getId() == Classification::CLASSIFICATION_ID_SERVICE;
    }

    /**
     * Is a new business acceleration.
     *
     * @return bool
     */
    public function isANewBusinessAcceleration()
    {
        return $this->getPortfolioProfile() && $this->getPortfolioProfile()->getId() == PortfolioProfile::PORTFOLIO_PROFILE_ID_NEW_BUSINESS_ACCELERATION;
    }

    /**
     * Is early stage.
     *
     * @return bool
     */
    public function isEarlyStage()
    {
        return ($this->getStage() && in_array($this->getStage()->getId(), Stage::getEarlyStageIds()));
    }

    /**
     * Get excel brand title.
     *
     * @return null|string
     */
    public function getExcelBrandTitle()
    {
        if ($this->getIsNewToTheWorld()) {
            return 'New To The World';
        } elseif ($this->getBrand()) {
            return $this->getBrand()->getTitle();
        }
        return '';
    }


    /**
     * to Excel Array.
     *
     * @param Settings $settings
     * @param array $all_stages
     *
     * @return array
     */
    public function toExcelArray(Settings $settings)
    {
        $ret = array();
        $ret['id'] = $this->getId();
        $ret['new_to_the_world'] = $this->getIsNewToTheWorld();
        $ret['years_since_launch'] = $this->getNbYearSinceLaunch();


        $post_current_stage = ($this->getIsFrozen()) ? ' (FROZEN)' : '';

        $value_cumulative_a_p = $this->getCumulativeEstimateAP($settings);
        $value_cumulative_caap = ($this->isEarlyStage()) ? 0 : $this->getCumulativeEstimateCaap($settings, $value_cumulative_a_p);

        $ret['status'] = ($this->getPortfolioProfile()) ? $this->getPortfolioProfile()->getExcelStatus() : '';
        $ret['name'] = ($this->getTitle()) ? strtoupper($this->getTitle()) : '';
        $ret['current_stage'] = ($this->getStage()) ? $this->getStage()->getExcelLibelle($post_current_stage) : '';
        $ret['type'] = ($this->getStage()) ? $this->getStage()->getCssClass() : '';
        $ret['is_early_stage'] = $this->isEarlyStage();
        $ret['brand'] = $this->getExcelBrandTitle();
        $ret['in_market_date'] = ($this->getInMarketDate()) ? $this->getInMarketDate()->setTimezone(new \DateTimeZone('GMT'))->format('m/Y') : '';
        $ret['growth_model'] = ($this->getGrowthModel() == 'fast_growth') ? 'Fast Growth' : 'Slow Build';
        if ($this->isAService()) {
            $ret['growth_model'] = '';
        }
        $ret['entity'] = ($this->getEntity()) ? $this->getEntity()->getTitle() : '';
        $ret['innovation_type'] = ($this->getType()) ? $this->getType()->getTitle() : '';
        $ret['classification_type'] = ($this->getClassification()) ? $this->getClassification()->getTitle() : '';
        $ret['category'] = ($this->getCategory()) ? $this->getCategory() : '';
        $ret['consumer_opportunity'] = ($this->getConsumerOpportunity()) ? $this->getConsumerOpportunity()->getTitle() : '';
        $ret['Total A&P'] = $value_cumulative_a_p;
        $ret['CAAP'] = $value_cumulative_caap;
        $ret['replace_sku'] = ($this->getIsReplacingExistingProduct()) ? 'YES' : 'NO';
        $ret['in_prisma'] = ($this->getIsInPrisma()) ? 'YES' : 'NO';
        $ret['existing_product'] = ($this->getIsReplacingExistingProduct() && $this->getReplacingProduct()) ? $this->getReplacingProduct() : '';
        $ret['moc'] = ($this->getMomentOfConsumption()) ? $this->getMomentOfConsumption()->getTitle() : '';
        $ret['business_drivers'] = ($this->getBusinessDriver()) ? $this->getBusinessDriver()->getTitle() : '';


        $financial_data_array = $this->getAllProperFinancialDatasForExcel($settings);
        $result = array_merge($ret, $financial_data_array);
        return $result;
    }


    /**
     * Get proper stage for url.
     *
     * @return string
     */
    public function getProperStageForUrl()
    {
        if ($this->getStage()) {
            return "&stage=" . $this->getStage()->getUrlLibelle();
        }
        return "";
    }

    /**
     * Get proper classification for url.
     *
     * @return string
     */
    public function getProperClassificationForUrl()
    {
        if ($this->getClassification()) {
            return "&classification=".$this->getClassification()->getUrlLibelle();
        }
        return "";
    }


    /**
     * Get monitor status
     * @return string
     */
    public function getMonitorStatus()
    {
        if ($this->getIsNew() && $this->getIsSoon()) {
            return 'NEW & SOON';
        } elseif ($this->getIsNew()) {
            return 'NEW';
        } elseif ($this->getIsSoon()) {
            return 'SOON';
        }
        return '';
    }

    /**
     * import created_at
     */
    public function importCreatedAt($created_at = null)
    {
        if ($created_at) {
            $this->created_at = $created_at;
        }
    }

    /**
     * Is New ?
     *
     * @return bool
     */
    public function getIsNew()
    {
        if ($this->getIsFrozen()) {
            return false;
        }
        if (!$this->getStage() || ($this->getStage() && !in_array($this->getStage()->getId(), Stage::getInnerStages()))) {
            return false;
        }
        $date = $this->getCreatedAt();
        $now = time();
        $datediff = $now - $date->getTimestamp();
        $nb_days = round($datediff / (60 * 60 * 24));
        return ($nb_days <= 60);
    }

    /**
     * Is Soon ?
     *
     * @return bool
     */
    public function getIsSoon()
    {
        if ($this->getIsFrozen()) {
            return false;
        }
        if (!$this->getStage() || ($this->getStage() && !in_array($this->getStage()->getId(), Stage::getInnerStages()))) {
            return false;
        }
        $date = $this->getInMarketDate();
        if (!$date) {
            return false;
        }
        $now = time();
        $datediff = $now - $date->getTimestamp();
        if ($date->getTimestamp() < $now) {
            return false;
        }
        $nb_days = round($datediff / (60 * 60 * 24));
        return ($nb_days <= 60);
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getTitle() : 'New innovation';
    }


    /**
     * Get number of empty Project ID Fields.
     *
     * @return int
     */
    public function getNumberOfEmptyProjectIDFields()
    {
        $nb_fields = 0;
        $stage_id = ($this->getStage()) ? $this->getStage()->getId() : -1;
        
        // Si NBA
        if($this->isANewBusinessAcceleration()){
            if (!$this->getInvestmentModel()) {
                $nb_fields++;
            }
            if (!$this->getNewBusinessOpportunity()) {
                $nb_fields++;
            }
        }
        
        if (!$this->getStartDate()) {
            $nb_fields++;
        }
        if (!$this->getInMarketDate()) {
            $nb_fields++;
        }
        if ($this->isAProduct() && !$this->getConsumerOpportunity()) {
            $nb_fields++;
        }
        if ($this->isAProduct() && !$this->getCategory()) {
            $nb_fields++;
        }
        if (!$this->getClassification()) {
            $nb_fields++;
        }
        if (!$this->getType()) {
            $nb_fields++;
        }
        if ($this->isAProduct()) {
            if (!$this->getBrand()) {
                $nb_fields++;
            }
            if ($this->getIsReplacingExistingProduct() && !$this->getReplacingProduct()) {
                $nb_fields++;
            }
            if (!$this->getMomentOfConsumption()) {
                $nb_fields++;
            }
            if (!$this->getBusinessDriver()) {
                $nb_fields++;
            }
            if(!in_array($stage_id, array(Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE))){
                if(!$this->getAlcoholByVolume()){
                    $nb_fields++;
                }
            }

        } elseif (!$this->getIsMultiBrand() && !$this->getBrand()) {
            $nb_fields++;
        }
        return $nb_fields;
    }

    /**
     * Get number of empty Elevator Pitch Fields.
     *
     * @return int
     */
    public function getNumberOfEmptyElevatorPitchFields()
    {
        $nb_fields = 0;

        // Si NBA
        if($this->isANewBusinessAcceleration()){
            if (!$this->getIdeaDescription()) {
                $nb_fields++;
            }
            if (!$this->getStrategicIntentMission()) {
                $nb_fields++;
            }
            return $nb_fields;
        }

        $stage_id = ($this->getStage()) ? $this->getStage()->getId() : -1;
        if (!$this->getWhyInvestInThisInnovation()) {
            $nb_fields++;
        }
        if ($this->isAProduct() && !$this->getStory()) {
            $nb_fields++;
        }

        if($this->isAProduct() && !$this->getUniqueness()) {
            $nb_fields++;
        }
        if (!$this->isAProduct() && !$this->getUniqueExperience()) {
            $nb_fields++;
        }
        if ($this->isAProduct() && !in_array($stage_id, array(Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE))) {
            if (!$this->getEarlyAdopterPersona()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation1()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation2()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation3()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation3Vs()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation4()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation4Vs()) {
                $nb_fields++;
            }
            if (!$this->getUniversalKeyInformation5()) {
                $nb_fields++;
            }
            if (!$this->getKeyLearningSoFar()) {
                $nb_fields++;
            }
            if (!$this->getNextSteps()) {
                $nb_fields++;
            }
        }
        if (!$this->getConsumerInsight()) {
            $nb_fields++;
        }
        if (!$this->getSourceOfBusiness()) {
            $nb_fields++;
        }
        return $nb_fields;
    }


    /**
     * Get number of empty Assets Fields.
     *
     * @return int
     */
    public function getNumberOfEmptyAssetsFields()
    {
        $nb_fields = 0;
        if (!$this->getBeautyshotPicture()) {
            $nb_fields++;
        }
        if ($this->isAProduct() && !$this->getPackshotPicture()) {
            $nb_fields++;
        }
        return $nb_fields;
    }

    /**
     * Get number of empty Financial Fields.
     *
     * @param Settings $settings
     * @return int
     */
    public function getNumberOfEmptyFinancialFields(Settings $settings)
    {
        $nb_fields = 0;
        $stage_id = ($this->getStage()) ? $this->getStage()->getId() : -1;
        $current_stage = ($this->getStage()) ? $this->getStage()->getCssClass() : 'empty';
        $is_nba = $this->isANewBusinessAcceleration();
        $is_a_service = $this->isAService();
        $date = $settings->getCurrentFinancialDate();
        $financial_datas = $this->getProperFinancialDatas($settings, $date, true);
        $fields = $settings->getFinancialDataFieldsForStage($current_stage, $date, true, false, true, $is_a_service, $is_nba)['to_display_list'];
        foreach ($fields as $field) {
            if ($field['type'] != 'tr' && !($financial_datas && array_key_exists($field['id'], $financial_datas))) {
                $nb_fields++;
            }
        }
        if (!$is_nba && $is_a_service && !$this->getPlanToMakeMoney()) {
            $nb_fields++;
        }
        if($is_nba && !$this->getProjectOwnerDisponibility()){
            $nb_fields++;
        }
        if($is_nba && !$this->getFullTimeEmployees()){
            $nb_fields++;
        }
        if($is_nba && !$this->getExternalText()){
            $nb_fields++;
        }
        return $nb_fields;
    }

    /**
     * Get number of empty fields
     * @param Settings $settings
     * @return array
     */
    public function getNumberOfEmptyFields(Settings $settings)
    {
        $project_id = $this->getNumberOfEmptyProjectIDFields();
        $elevator_pitch = $this->getNumberOfEmptyElevatorPitchFields();
        $assets = $this->getNumberOfEmptyAssetsFields();
        $market = (count($this->getMarketsArray()) == 0) ? 1 : 0;
        $financial = $this->getNumberOfEmptyFinancialFields($settings);
        $total = $project_id + $elevator_pitch + $assets + $financial;

        if($this->isOutOfFunnel()){
            return array(
                'edit' => 0,
                'market' => 0,
                'financial' => 0,
                'total' => 0
            );
        }

        return array(
            'edit' => ($project_id + $elevator_pitch + $assets),
            'market' => $market,
            'financial' => $financial,
            'total' => $total
        );
    }

    /**
     * Is out of funnel.
     *
     * @return bool
     */
    public function isOutOfFunnel(){
        return ($this->getIsFrozen() || (in_array($this->getStage()->getId(), Stage::getOutOfFunnelStageIds())));
    }

    /**
     * Get last a stage
     * @param Settings $settings
     * @return int|null
     */
    public function getLastAStage(Settings $settings)
    {
        $last_a_date = $settings->getLastADate();
        $last_a_datetime = new \DateTime($last_a_date);
        if ($last_a_datetime < $this->getCreatedAt()) {
            return null;
        }
        $stage_activity = $this->getActivities()->filter(function ($activity) use ($last_a_datetime) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_CHANGE_STAGE && $activity->getCreatedAt() >= $last_a_datetime;
        })->last();
        if ($stage_activity) {
            return intval($stage_activity->getDataArray()['old_value']);
        }
        return ($this->getStage()) ? $this->getStage()->getId() : null;
    }

    /**
     * Get last a is_frozen
     * @param Settings $settings
     * @return int|null
     */
    public function getLastAIsFrozen(Settings $settings)
    {
        $last_a_date = $settings->getLastADate();
        $last_a_datetime = new \DateTime($last_a_date);
        if ($last_a_datetime < $this->getCreatedAt()) {
            return null;
        }
        $stage_activity = $this->getActivities()->filter(function ($activity) use ($last_a_datetime) {
            return $activity->getActionId() == Activity::ACTION_INNOVATION_FROZEN && $activity->getCreatedAt() >= $last_a_datetime;
        })->last();
        if ($stage_activity) {
            $value = $stage_activity->getDataArray()['old_value'];
            if (!$value || $value == 0) {
                return false;
            }
            return true;
        }
        return $this->getIsFrozen();
    }

    /**
     * Add tag.
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return Innovation
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        if ($this->hasTag($tag)) {
            return $this;
        }
        $this->tags[] = $tag;
        return $this;
    }

    /**
     * has tag.
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return bool
     */
    public function hasTag(\AppBundle\Entity\Tag $tag)
    {
        foreach ($this->tags as $a_tag) {
            if ($tag->getId() === $a_tag->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove all tags.
     *
     * @return Innovation
     */
    public function removeAllTags()
    {
        $this->tags = [];
        return $this;
    }

    /**
     * Remove tag.
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTag(\AppBundle\Entity\Tag $tag)
    {
        return $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }


    /**
     * getDefaultPicture
     * @return string
     */
    public function getDefaultPicture()
    {
        $dir = Settings::getWebsiteBaseUrl();
        $dir .= "/images/default/";
        $dir .= ($this->isAService()) ? 'service/' : 'product/';
        return $dir . 'default-' . (($this->getId() % 6) + 1) . '.png';
    }

    /**
     * Set sortScore.
     *
     * @param int|null $sortScore
     *
     * @return Innovation
     */
    public function setSortScore($sortScore = null)
    {
        $this->sort_score = $sortScore;

        return $this;
    }

    /**
     * Get sortScore.
     *
     * @return int|null
     */
    public function getSortScore()
    {
        return $this->sort_score;
    }

    /**
     * Update sort score.
     *
     * @ORM\PreUpdate
     * @return $this
     */
    public function updateSortScore(){
        $scale = 100000000;
        $score = 0;
        if($this->isAProduct()){
            $score += 2*$scale;
        }else{
            $score += $scale;
        }
        $this->sort_score = $score;
        return $this;
    }


    /**
     * Set universalKeyInformation3Vs.
     *
     * @param string|null $universalKeyInformation3Vs
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation3Vs($universalKeyInformation3Vs = null)
    {
        $this->universal_key_information_3_vs = $universalKeyInformation3Vs;

        return $this;
    }

    /**
     * Get universalKeyInformation3Vs.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation3Vs()
    {
        return $this->universal_key_information_3_vs;
    }

    /**
     * Set universalKeyInformation4Vs.
     *
     * @param string|null $universalKeyInformation4Vs
     *
     * @return Innovation
     */
    public function setUniversalKeyInformation4Vs($universalKeyInformation4Vs = null)
    {
        $this->universal_key_information_4_vs = $universalKeyInformation4Vs;

        return $this;
    }

    /**
     * Get universalKeyInformation4Vs.
     *
     * @return string|null
     */
    public function getUniversalKeyInformation4Vs()
    {
        return $this->universal_key_information_4_vs;
    }

    /**
     * Set alcoholByVolume.
     *
     * @param string|null $alcoholByVolume
     *
     * @return Innovation
     */
    public function setAlcoholByVolume($alcoholByVolume = null)
    {
        $this->alcohol_by_volume = $alcoholByVolume;

        return $this;
    }

    /**
     * Get alcoholByVolume.
     *
     * @return string|null
     */
    public function getAlcoholByVolume()
    {
        return $this->alcohol_by_volume;
    }
    
    /**
     * Add keyCity.
     *
     * @param \AppBundle\Entity\City $keyCity
     *
     * @return Innovation
     */
    public function addKeyCity(\AppBundle\Entity\City $keyCity)
    {
        $this->key_cities[] = $keyCity;
        
        return $this;
    }
    
    /**
     * Remove keyCity.
     *
     * @param \AppBundle\Entity\City $keyCity
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeKeyCity(\AppBundle\Entity\City $keyCity)
    {
        return $this->key_cities->removeElement($keyCity);
    }
    
    /**
     * Get keyCities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKeyCities()
    {
        return $this->key_cities;
    }
    
    /**
     * has city.
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return bool
     */
    public function hasKeyCity(\AppBundle\Entity\City $city)
    {
        foreach ($this->key_cities as $a_city) {
            if ($city->getId() === $a_city->getId()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get keyCities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKeyCitiesArray()
    {
        $ret = array();
        foreach ($this->getKeyCities() as $keyCity){
            $ret[] = $keyCity->toArray();
        }
        return $ret;
    }

    /**
     * Set newBusinessOpportunity.
     *
     * @param string|null $newBusinessOpportunity
     *
     * @return Innovation
     */
    public function setNewBusinessOpportunity($newBusinessOpportunity = null)
    {
        $this->new_business_opportunity = $newBusinessOpportunity;

        return $this;
    }

    /**
     * Get newBusinessOpportunity.
     *
     * @return string|null
     */
    public function getNewBusinessOpportunity()
    {
        return $this->new_business_opportunity;
    }

    /**
     * Set investmentModel.
     *
     * @param string|null $investmentModel
     *
     * @return Innovation
     */
    public function setInvestmentModel($investmentModel = null)
    {
        $this->investment_model = $investmentModel;

        return $this;
    }

    /**
     * Get investmentModel.
     *
     * @return string|null
     */
    public function getInvestmentModel()
    {
        return $this->investment_model;
    }

    /**
     * Set asSeperatePl.
     *
     * @param bool $asSeperatePl
     *
     * @return Innovation
     */
    public function setAsSeperatePl($asSeperatePl)
    {
        $this->as_seperate_pl = $asSeperatePl;

        return $this;
    }

    /**
     * Get asSeperatePl.
     *
     * @return bool
     */
    public function getAsSeperatePl()
    {
        return $this->as_seperate_pl;
    }

    /**
     * Set ideaDescription.
     *
     * @param string|null $ideaDescription
     *
     * @return Innovation
     */
    public function setIdeaDescription($ideaDescription = null)
    {
        $this->idea_description = $ideaDescription;

        return $this;
    }

    /**
     * Get ideaDescription.
     *
     * @return string|null
     */
    public function getIdeaDescription()
    {
        return $this->idea_description;
    }

    /**
     * Set strategicIntentMission.
     *
     * @param string|null $strategicIntentMission
     *
     * @return Innovation
     */
    public function setStrategicIntentMission($strategicIntentMission = null)
    {
        $this->strategic_intent_mission = $strategicIntentMission;

        return $this;
    }

    /**
     * Get strategicIntentMission.
     *
     * @return string|null
     */
    public function getStrategicIntentMission()
    {
        return $this->strategic_intent_mission;
    }

    /**
     * Add canvasCollection.
     *
     * @param \AppBundle\Entity\Canvas $canvasCollection
     *
     * @return Innovation
     */
    public function addCanvasCollection(\AppBundle\Entity\Canvas $canvasCollection)
    {
        $this->canvas_collection[] = $canvasCollection;

        return $this;
    }

    /**
     * Remove canvasCollection.
     *
     * @param \AppBundle\Entity\Canvas $canvasCollection
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCanvasCollection(\AppBundle\Entity\Canvas $canvasCollection)
    {
        return $this->canvas_collection->removeElement($canvasCollection);
    }

    /**
     * Get canvasCollection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCanvasCollection()
    {
        return $this->canvas_collection;
    }

    /**
     * Get Canvas collection array.
     *
     * @return array
     */
    public function getCanvasCollectionArray()
    {
        $ret = array();
        foreach ($this->canvas_collection as $canvas){
            $ret[] = $canvas->toArray();
        }
        return $ret;
    }

    /**
     * Set openQuestion.
     *
     * @param \AppBundle\Entity\OpenQuestion|null $openQuestion
     *
     * @return Innovation
     */
    public function setOpenQuestion(\AppBundle\Entity\OpenQuestion $openQuestion = null)
    {
        $this->open_question = $openQuestion;

        return $this;
    }

    /**
     * Get openQuestion.
     *
     * @return \AppBundle\Entity\OpenQuestion|null
     */
    public function getOpenQuestion()
    {
        return $this->open_question;
    }

    /**
     * Set projectOwnerDisponibility.
     *
     * @param string|null $projectOwnerDisponibility
     *
     * @return Innovation
     */
    public function setProjectOwnerDisponibility($projectOwnerDisponibility = null)
    {
        $this->project_owner_disponibility = $projectOwnerDisponibility;

        return $this;
    }

    /**
     * Get projectOwnerDisponibility.
     *
     * @return string|null
     */
    public function getProjectOwnerDisponibility()
    {
        return $this->project_owner_disponibility;
    }

    /**
     * Set fullTimeEmployees.
     *
     * @param string|null $fullTimeEmployees
     *
     * @return Innovation
     */
    public function setFullTimeEmployees($fullTimeEmployees = null)
    {
        $this->full_time_employees = $fullTimeEmployees;

        return $this;
    }

    /**
     * Get fullTimeEmployees.
     *
     * @return string|null
     */
    public function getFullTimeEmployees()
    {
        return $this->full_time_employees;
    }


    /**
     * Set externalText.
     *
     * @param string|null $externalText
     *
     * @return Innovation
     */
    public function setExternalText($externalText = null)
    {
        $this->external_text = $externalText;

        return $this;
    }

    /**
     * Get externalText.
     *
     * @return string|null
     */
    public function getExternalText()
    {
        return $this->external_text;
    }

    /**
     * Innovation_array is a service ?
     *
     * @param $innovation_array
     * @return bool
     */
    public static function innovationArrayIsAService($innovation_array){
        if(!$innovation_array || !is_array($innovation_array)){
            return false;
        }
        return ($innovation_array['classification_type'] == 'Service');
    }
}
