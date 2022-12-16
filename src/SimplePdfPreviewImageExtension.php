<?php

namespace Ivoba\SilverStripe\SimplePdfPreview;

use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Filesystem;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

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
        
        $tmpDir = tempnam(sys_get_temp_dir(), 'pdf');

        $image = DataObject::get_one(Image::class, "`Name` = '{$saveImage}'");

        FileSystem::makeFolder('assets/' . $this->folderToSave);
        $folderObject = Folder::find_or_make($this->folderToSave);

        if (!$image || !$image->exists()) {
            if ($this->generator->generatePreviewImage($pdfFile, $tmpFile)) {
                $image = new Image();
                $image->setFromLocalFile($tmpFile, $this->folderToSave . '/' .$saveImage);
                $image->ParentID = $folderObject->ID;
                $image->write();
                $image->publishRecursive();
                $this->generateThumbnails($image);
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

    /**
     * Generate thumbnails for use in the CMS
     */
    public function generateThumbnails($image)
    {
        $assetAdmin = AssetAdmin::singleton();
        $image->FitMax(
            $assetAdmin->config()->get('thumbnail_width'),
            $assetAdmin->config()->get('thumbnail_height')
        );
        $image->FitMax(
            UploadField::config()->uninherited('thumbnail_width'),
            UploadField::config()->uninherited('thumbnail_height')
        );
    }

}
