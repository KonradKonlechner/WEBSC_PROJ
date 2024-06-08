<?php

namespace product;

use JsonSerializable;

class product implements JsonSerializable
{
    private $id;
    private $name;
    private $description;
    private $category;
    private $price;
    private $imagePath;
    private $thumbnailPath;

    /**
     * @param $id
     * @param $name
     * @param $description
     * @param $price
     * @param $category
     * @param $imagePath
     * @param $thumbnailPath
 */
    public function __construct($id, $name, $description, $category, $price, $imagePath, $thumbnailPath)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->category = $category;
        $this->price = $price;
        $this->imagePath = $imagePath;
        $this->thumbnailPath = $thumbnailPath;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'imagePath' => $this->imagePath,
            'thumbnailPath' => $this->thumbnailPath
        ];
    }
}