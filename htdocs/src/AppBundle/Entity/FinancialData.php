<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinancialData
 *
 * @ORM\Table(name="financial_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FinancialDataRepository")
 */
class FinancialData
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
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="financial_datas")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="current_key", type="string", length=255, nullable=true)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="current_value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="financial_data")
     */
    protected $activities;


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
     * Set key.
     *
     * @param string|null $key
     *
     * @return FinancialData
     */
    public function setKey($key = null)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return FinancialData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get calculable value.
     *
     * @param bool $with_na
     * @return float
     */
    public function getCalculableValue($with_na = false)
    {
        if(!$this->value || ($this->value == "N/A" && !$with_na)){
            return 0;
        }
        if($with_na && $this->value == "N/A"){
            return "N/A";
        }
        $calculable_value = (int) str_replace(' ', '', $this->value);
        if ( // Cases always negative
            $calculable_value > 0 && (
            strpos($this->key, 'advertising_promotion_') !== false ||
            strpos($this->key, 'central_investment_') !== false ||
            strpos($this->key, 'cogs_') !== false
            )
        ) {
            $calculable_value = -$calculable_value;
        }
        return $calculable_value;
    }


    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation|null $innovation
     *
     * @return FinancialData
     */
    public function setInnovation(\AppBundle\Entity\Innovation $innovation = null)
    {
        $this->innovation = $innovation;

        return $this;
    }

    /**
     * Get innovation.
     *
     * @return \AppBundle\Entity\Innovation|null
     */
    public function getInnovation()
    {
        return $this->innovation;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add activity.
     *
     * @param \AppBundle\Entity\Activity $activity
     *
     * @return FinancialData
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
     * @return FinancialData
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
     * Truncate financial libelle.
     *
     * @param string $libelle
     * @return array|null
     */
    public static function truncateFinancialLibelle($libelle)
    {
        if (strpos($libelle, 'volume') !== false) {
            return array(
                'type' => 'volume',
                'value' => str_replace('volume_', '', $libelle),
            );
        } elseif (strpos($libelle, 'net_sales') !== false) {
            return array(
                'type' => 'net_sales',
                'value' => str_replace('net_sales_', '', $libelle),
            );
        } elseif (strpos($libelle, 'contributing_margin') !== false) {
            return array(
                'type' => 'contributing_margin',
                'value' => str_replace('contributing_margin_', '', $libelle),
            );
        } elseif (strpos($libelle, 'central_investment') !== false) {
            return array(
                'type' => 'central_investment',
                'value' => str_replace('central_investment_', '', $libelle),
            );
        } elseif (strpos($libelle, 'cogs') !== false) {
            return array(
                'type' => 'cogs',
                'value' => str_replace('cogs_', '', $libelle),
            );
        } elseif (strpos($libelle, 'investment') !== false) {
            return array(
                'type' => 'investment',
                'value' => str_replace('investment_', '', $libelle),
            );
        } elseif (strpos($libelle, 'revenue') !== false) {
            return array(
                'type' => 'revenue',
                'value' => str_replace('revenue_', '', $libelle),
            );
        } elseif (strpos($libelle, 'advertising_promotion') !== false) {
            return array(
                'type' => 'advertising_promotion',
                'value' => str_replace('advertising_promotion_', '', $libelle),
            );
        } elseif (strpos($libelle, 'caap') !== false) {
            return array(
                'type' => 'caap',
                'value' => str_replace('caap_', '', $libelle),
            );
        }
        return null;
    }

    /**
     * Get proper key.
     *
     * @return string
     */
    function getProperKey()
    {
        $libelle = $this->getKey();
        $key = str_replace('_final', '', $libelle);
        $key = str_replace('_initial', '', $key);

        $key = str_replace('volume', 'Vol', $key);
        $key = str_replace('net_sales', 'NS', $key);
        $key = str_replace('contributing_margin', 'CM', $key);
        $key = str_replace('advertising_promotion', 'A&P', $key);
        $key = str_replace('central_investment', 'CI', $key);
        $key = str_replace('investment', 'I', $key);
        $key = str_replace('revenue', 'R', $key);
        $key = str_replace('cogs', 'COGS', $key);
        $key = str_replace('caap', 'CAAP', $key);

        $key = str_replace('_', ' ', $key);
        return $key;
    }

    /**
     * Get financial data class
     * @param $value
     * @return string
     */
    public static function  getFinancialDataClass($value){
        if($value == 'NEW'){
            return 'positive';
        }
        return ($value === 'N/A' || $value >= 0) ? 'positive' : 'negative';
    }

    /**
     * Calculate value creation.
     * 
     * @param $net_sales_latest_a
     * @param $net_sales_latest
     * @return float|int|string
     */
    public static function calculateValueCreation($net_sales_latest_a, $net_sales_latest){
        if ($net_sales_latest_a == 0) {
            return "NEW";
        }
        if ($net_sales_latest_a != 0 && $net_sales_latest != 0) {
            return (($net_sales_latest - $net_sales_latest_a) / $net_sales_latest_a) * 100;
        }
        return 0;
    }

    /**
     * Calculate value creation.
     *
     * @param $volume_latest_a
     * @param $volume_latest
     * @return float|int|string
     */
    public static function calculateGrowthDynamism($volume_latest_a, $volume_latest){
        if ($volume_latest_a == 0) {
            return "NEW";
        }
        if ($volume_latest_a != 0 && $volume_latest != 0) {
            return (($volume_latest - $volume_latest_a) / $volume_latest_a) * 100;
        }
        return 0;
    }


    /**
     * Calculate level of investment.
     * 
     * @param $total_ap_latest
     * @param $net_sales_latest
     * @return float|int|string
     */
    public static function calculateLevelOfInvestment($total_ap_latest, $net_sales_latest){
        if ($net_sales_latest == 0) {
            return 'NEW';
        }
        if ($total_ap_latest != 0 && $net_sales_latest != 0) {
            return (abs($total_ap_latest) / $net_sales_latest) * 100;
        }
        return 0;
    }

    /**
     * Calculate level of profitability.
     *
     * @param $contributing_margin_latest
     * @param $net_sales_latest
     * @return float|int|string
     */
    public static function calculateLevelOfProfitability($contributing_margin_latest, $net_sales_latest){
        if(!$contributing_margin_latest || !$net_sales_latest){
            return 0;
        }
        if ($contributing_margin_latest != 0 && $net_sales_latest != 0) {
            return floatval(($contributing_margin_latest / $net_sales_latest) * 100);
        }
        return 0;
    }

    /**
     * Calculate CM per case.
     *
     * @param $volume_latest
     * @param $contributing_margin_latest $date
     * @return float|int
     */
    public static function calculateCmPerCase($volume_latest, $contributing_margin_latest)
    {
        if ($volume_latest && $contributing_margin_latest) {
            return $contributing_margin_latest / $volume_latest;
        }
        return 0;
    }

    /**
     * Calculate CM per case percent
     * @param $volume_latest_a
     * @param $volume_latest
     * @param $contributing_margin_latest_a
     * @param $contributing_margin_latest
     * @return float|string
     */
    public static function calculateCmPerCasePercent($volume_latest_a, $volume_latest, $contributing_margin_latest_a, $contributing_margin_latest){
        $cm_per_case_latest_a = self::calculateCmPerCase($volume_latest_a, $contributing_margin_latest_a);
        $cm_per_case_latest = self::calculateCmPerCase($volume_latest, $contributing_margin_latest);
        if ($cm_per_case_latest_a == 0) {
            return "NEW";
        }
        $total = ($cm_per_case_latest - $cm_per_case_latest_a) / $cm_per_case_latest_a * 100;
        return round($total);
    }

    /**
     * cleanFieldLibelle
     * 
     * @param string $libelle
     * @return string
     */
    public static function cleanFieldLibelle($libelle){
        $id = str_replace(' ', '_', $libelle);
        $id = str_replace('(', '', $id);
        $id = str_replace(')', '', $id);
        return $id;
    }

    /**
     * returnFinancialDateFormatted
     *
     * @param int|string $value
     * @return string
     */
    public static function returnFinancialDateFormatted($value){
        if($value == 'N/A'){
            return $value;
        }
        $is_negative = ($value < 0);
        $value_string = number_format(abs($value));
        $value_string =  ($is_negative) ? '('.$value_string.')' : $value_string;
        return $value_string;
    }
}
