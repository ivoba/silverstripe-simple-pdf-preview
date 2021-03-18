<?php

namespace Ivoba\SilverStripe\SimplePdfPreview;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\Image;

class SimplePdfPreviewImageExtension extends DataExtension
{

    private $generator;
    private $folderToSave;
    private $imagePrefix;

//@Todo cant get Silverstripe DI to work with constructor injection
//    function __construct(SimplePdfPreviewGeneratorInterface $generator,
//                         $folderToSave = null,
//                         $imagePrefix = null,
//                         $savePath = null)
//    {
//        $this->generator = $generator;
//        $this->folderToSave = $folderToSave;
//        $this->imagePrefix = $imagePrefix;
//        $this->savePath = $savePath;
//    }

    public function getPdfPreviewImage()
    {
        $url = $this->owner->getUrl();
        $url = ltrim($url, '/');
        $pdfFile = Director::getAbsFile($url);
        $pathInfo = pathinfo($pdfFile);
        if (!isset($pathInfo['extension']) || strtolower($pathInfo['extension']) != 'pdf') {
            //@Todo if dev then exception? else fail silently
            return File::create();
        }
        $fileName = $pathInfo['filename'];

        $saveImage = $this->imagePrefix.'-'.$fileName.'.jpg';

        // Fix illegal characters
        $filter    = FileNameFilter::create();
        $saveImage = $filter->filter($saveImage);
        $tmpFile   = tempnam("/tmp", "pdf");

        $image = DataObject::get_one(Image::class, "`Name` = '{$saveImage}'");

        if (!$image) {
            $folderObject = Folder::find_or_make($this->folderToSave);
            if ($folderObject) {
                if ($this->generator->generatePreviewImage($pdfFile, $tmpFile)) {
                    $image = new Image();
                    $image->setFromLocalFile($tmpFile, $saveImage);
                    $image->ParentID = $folderObject->ID;
                    $image->setFilename($saveImage);
                    $image->write();
                }
            }
        } else {
            //check LastEdited to update
            $cacheInValid = false;
            if (strtotime($image->LastEdited) < strtotime($this->owner->LastEdited)) {
                $cacheInValid = true;
            }
            if ($cacheInValid) {
                $this->generator->generatePreviewImage($pdfFile, $tmpFile);
                $image->setName($saveImage);
                $image->setFromLocalFile($tmpFile, $saveImage);
                $image->write(false, false, true);
            }
        }
        unlink($tmpFile);

        return $image;
    }

    /**
     * @param $folderToSave
     */
    public function setFolderToSave($folderToSave)
    {
        $this->folderToSave = $folderToSave;
    }

    /**
     * @param SimplePdfPreviewGeneratorInterface $generator
     */
    public function setGenerator(SimplePdfPreviewGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param $imagePrefix
     */
    public function setImagePrefix($imagePrefix)
    {
        $this->imagePrefix = $imagePrefix;
    }

}
