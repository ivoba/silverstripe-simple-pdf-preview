silverstripe-simple-pdf-preview
===============================

Simple Pdf Preview Extension for SilverStripe CMS

[![Total Downloads](https://poser.pugx.org/ivoba/silverstripe-simple-pdf-preview/downloads.png)](https://packagist.org/packages/ivoba/silverstripe-simple-pdf-preview)

Create Jpg Preview Images of Pdf files on the fly.  


## Install

Via Composer

``` json
{
    "require": {
        "ivoba/silverstripe-simple-pdf-preview": "~1.0"
    }
}
```

## Requirements
- currently only tested in SilverStripe 3.1
- you will need the Imagick extension.


## Usage
In your template just call ```$Pdf.getPdfPreviewImage``` where $Pdf is your File Object, containing a pdf file.  
You then have a normal Image object, on which you can apply all methods you usually can apply on an image in SilverStripe.  
F.e.: ```$Pdf.getPdfPreviewImage.CroppedImage(60,60)```  

If you call this method on a non-pdf file, null will be returned.  

## Config
You can override all Params in your config.yml.  

``` yaml
SimplePdfPreviewImageExtension:
  dependencies:
    generator: %$SimplePdfPreviewExecGenerator
    folderToSave: "assets/someOtherFolder/"
    imagePrefix: "pdf-foobar"

Injector:
  SimplePdfPreviewExecGenerator:
    class: SimplePdfPreviewExecGenerator
```
You can create your own Generator class, simply implement ```SimplePdfPreviewGeneratorInterface```.  
This could be the case, when we you dont want to use Imagick but ImageMagick directly via exec.


## Disclaimer

This extension is "simple" because it will just create a loose Image object.  
Mapping happens over the filename. So its rather risky, but sufficient for most cases.  

Somebody please make a better PDF extension, with a PDF File type, a PDF FileField and a preview image generation after upload. :)  

## License

The MIT License (MIT). Please see [License File](https://github.com/ivoba/silverstripe-simple-pdf-preview/blob/master/LICENSE) for more information.
