<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Picture
 *
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PictureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Picture
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
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

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
    public function setCreatedAt() {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt() {
        $this->updated_at = new \DateTime();
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set filename.
     *
     * @param string|null $url
     *
     * @return Picture
     */
    public function setFilename($filename = null)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get path
     *
     * @return null|string
     */
    public function getPath()
    {
        if(!$this->getFilename()){
            return null;
        }
        return $this->getUploadDirectory().$this->getFilename();
    }

    /**
     * Delete picture file.
     */
    public function deleteFile(){
        if($this->getFilename() && file_exists($this->getAbsoluteUploadDirectory().$this->getFilename())){
            unlink($this->getAbsoluteUploadDirectory().$this->getFilename());
        }
    }
    /**
     * Get absolute upload directory.
     *
     * @return string
     */
    public function getAbsoluteUploadDirectory()
    {
        return __DIR__ . "/../../../web/uploads/";
    }

    /**
     * Get upload directory.
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return "/uploads/";
    }

    /**
     * To array.
     *
     * @param $liip
     * @return array
     */
    public function toArray($liip = null)
    {
        return array(
            'id' => $this->getId(),
            'filename' => $this->getFilename(),
            'path' => $this->getPath(),
            'thumbnail' => $this->resizeImage($liip, 'thumbnail_picture')
        );
    }

    /**
     * Guess thumbnail.
     *
     * @return string
     */
    public function guessThumbnail()
    {
        return $_ENV['AWS_BASE_URL'].$_ENV['CURRENT_PLATFORM']."/thumbnail_picture".$this->getPath();
    }


    /**
     * Resize image depending on filter using liip
     * https://symfony.com/doc/2.0/bundles/LiipImagineBundle/filters.html
     *
     * @param $liip
     * @param string $thumb_name
     * @param null|array $filter
     * @return mixed
     */
    public function resizeImage($liip, $thumb_name = 'custom', $filter = null) {
        $url = $_ENV['AWS_BASE_URL'].$_ENV['CURRENT_PLATFORM']."/".$thumb_name.$this->getPath();
        if(!$liip){
            return $url;
        }
        try{
            $headers=get_headers($url);
            if(stripos($headers[0],"200 OK")){ // thumbnail already exist
                return $url;
            }else{
                if(!$filter){
                    $path = $liip->getUrlOfFilteredImage($this->getPath(), $thumb_name);
                }else {
                    $path = $liip->getUrlOfFilteredImageWithRuntimeFilters(
                        $this->getPath(),
                        $thumb_name,
                        $filter
                    );
                }
                return $path;
            }
        }catch (\Exception $e){
            return $url;
        }
    }

    
}
