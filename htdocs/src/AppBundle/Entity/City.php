<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CityRepository")
 */
class City
{
    const DEFAULT_PICTURE_URL = '/images/default/city.png';

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
     * @ORM\Column(name="geoname_id", type="integer", nullable=true)
     */
    private $geoname_id;

    /**
     * @var string
     *
     * @ORM\Column(name="continent_code", type="string", length=255, nullable=true)
     */
    private $continent_code;

    /**
     * @var string
     *
     * @ORM\Column(name="continent_name", type="string", length=255, nullable=true)
     */
    private $continent_name;


    /**
     * @var string
     *
     * @ORM\Column(name="country_iso_code", type="string", length=255, nullable=true)
     */
    private $country_iso_code;

    /**
     * @var string
     *
     * @ORM\Column(name="country_name", type="string", length=255, nullable=true)
     */
    private $country_name;


    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=255, nullable=true)
     */
    private $city_name;

    /**
     * @var string
     *
     * @ORM\Column(name="time_zone", type="string", length=255, nullable=true)
     */
    private $time_zone;


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_in_european_union", type="boolean")
     */
    private $is_in_european_union = false;


    /**
     * @var string
     *
     * @ORM\Column(name="picture_url", type="string", length=510, nullable=true)
     */
    private $picture_url;


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
     * Set geonameId.
     *
     * @param int|null $geonameId
     *
     * @return City
     */
    public function setGeonameId($geonameId = null)
    {
        $this->geoname_id = $geonameId;

        return $this;
    }

    /**
     * Get geonameId.
     *
     * @return int|null
     */
    public function getGeonameId()
    {
        return $this->geoname_id;
    }

    /**
     * Set continentCode.
     *
     * @param string|null $continentCode
     *
     * @return City
     */
    public function setContinentCode($continentCode = null)
    {
        $this->continent_code = $continentCode;

        return $this;
    }

    /**
     * Get continentCode.
     *
     * @return string|null
     */
    public function getContinentCode()
    {
        return $this->continent_code;
    }

    /**
     * Set continentName.
     *
     * @param string|null $continentName
     *
     * @return City
     */
    public function setContinentName($continentName = null)
    {
        $this->continent_name = $continentName;

        return $this;
    }

    /**
     * Get continentName.
     *
     * @return string|null
     */
    public function getContinentName()
    {
        return $this->continent_name;
    }

    /**
     * Set countryIsoCode.
     *
     * @param string|null $countryIsoCode
     *
     * @return City
     */
    public function setCountryIsoCode($countryIsoCode = null)
    {
        $this->country_iso_code = $countryIsoCode;

        return $this;
    }

    /**
     * Get countryIsoCode.
     *
     * @return string|null
     */
    public function getCountryIsoCode()
    {
        return $this->country_iso_code;
    }

    /**
     * Set countryName.
     *
     * @param string|null $countryName
     *
     * @return City
     */
    public function setCountryName($countryName = null)
    {
        $this->country_name = $countryName;

        return $this;
    }

    /**
     * Get countryName.
     *
     * @return string|null
     */
    public function getCountryName()
    {
        return $this->country_name;
    }

    /**
     * Set cityName.
     *
     * @param string|null $cityName
     *
     * @return City
     */
    public function setCityName($cityName = null)
    {
        $this->city_name = $cityName;

        return $this;
    }

    /**
     * Get cityName.
     *
     * @return string|null
     */
    public function getCityName()
    {
        return $this->city_name;
    }

    /**
     * Set timeZone.
     *
     * @param string|null $timeZone
     *
     * @return City
     */
    public function setTimeZone($timeZone = null)
    {
        $this->time_zone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone.
     *
     * @return string|null
     */
    public function getTimeZone()
    {
        return $this->time_zone;
    }

    /**
     * Set isInEuropeanUnion.
     *
     * @param bool $isInEuropeanUnion
     *
     * @return City
     */
    public function setIsInEuropeanUnion($isInEuropeanUnion)
    {
        $this->is_in_european_union = $isInEuropeanUnion;

        return $this;
    }

    /**
     * Get isInEuropeanUnion.
     *
     * @return bool
     */
    public function getIsInEuropeanUnion()
    {
        return $this->is_in_european_union;
    }

    /**
     * getWikipediaInfos.
     *
     * @return bool|mixed|string|null
     */
    public function getWikipediaInfos(){
        try{
            $url = "https://en.wikipedia.org/w/api.php?action=query&format=json&formatversion=2&prop=pageimages|pageterms&piprop=thumbnail&pithumbsize=600&titles=";
            $url .= $this->getCityName();

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            if($result){
                return json_decode($result, true);
            }
            return $result;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Update picture url.
     *
     * @param bool $force_update
     */
    public function updatePictureUrl($force_update = false){
        if(!$force_update && $this->getPictureUrl()){
            return false;
        }
        $wikipediaInfos = $this->getWikipediaInfos();
        $picture_url = self::DEFAULT_PICTURE_URL;
        if($wikipediaInfos && array_key_exists('query', $wikipediaInfos)){
            if(array_key_exists('pages', $wikipediaInfos['query'])){
                if(count($wikipediaInfos['query']['pages']) > 0){
                    if(array_key_exists('thumbnail', $wikipediaInfos['query']['pages'][0])){
                        if(array_key_exists('source', $wikipediaInfos['query']['pages'][0]['thumbnail'])){
                            $picture_url = $wikipediaInfos['query']['pages'][0]['thumbnail']['source'];
                        }
                    }
                }
            }
        }
        if($picture_url){
            $this->setPictureUrl($picture_url);
            return true;
        }
    }


    /**
     * Set pictureUrl.
     *
     * @param string|null $pictureUrl
     *
     * @return City
     */
    public function setPictureUrl($pictureUrl = null)
    {
        $this->picture_url = $pictureUrl;

        return $this;
    }

    /**
     * Get pictureUrl.
     *
     * @return string|null
     */
    public function getPictureUrl()
    {
        return $this->picture_url;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'city_name' => $this->getCityName(),
            'country_name' => $this->getCountryName(),
            'picture_url' => (($this->getPictureUrl()) ? $this->getPictureUrl() : self::DEFAULT_PICTURE_URL),
        );
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getId()) ? $this->getCityName().' ('.$this->getCountryName().')' : 'New city';
    }


    /**
     * To select2 array.
     *
     * @return array
     */
    public function toSelect2Array(){
        return [
            "id" => $this->getId(),
            "text" => $this->getCityName()." (".$this->getCountryName().")",
            "type" => "city"
        ];
    }
}
