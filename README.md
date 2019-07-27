silverstripe-simple-pdf-preview
===============================

Simple Pdf Preview Extension for SilverStripe CMS

[![Build Status](http://img.shields.io/travis/ivoba/silverstripe-simple-pdf-preview.svg)](https://travis-ci.org/ivoba/silverstripe-simple-pdf-preview)
[![Total Downloads](https://poser.pugx.org/ivoba/silverstripe-simple-pdf-preview/downloads.png)](https://packagist.org/packages/ivoba/silverstripe-simple-pdf-preview)

Create Jpg Preview Images of Pdf files on the fly.


## Install

Via Composer

``` json
{
    "require": {
        "ivoba/silverstripe-simple-pdf-preview": "~2.0"
    }
}
```

## Requirements
- SilverStripe 4
- you will need the Imagick extension.

For SilverStripe 3 use v1.

## Usage
In your template just call ```$Pdf.getPdfPreviewImage``` where $Pdf is your File Object, containing a pdf file.
You then have a normal Image object, on which you can apply all methods you usually can apply on an image in SilverStripe.
F.e.: ```$Pdf.getPdfPreviewImage.Fill(60,60)```

If you call this method on a non-pdf file, null will be returned.

## Config
You can override all Params in your config.yml.

``` yaml
Ivoba\SilverStripe\SimplePdfPreview\SimplePdfPreviewImageExtension:
  dependencies:
    generator: %$Ivoba\SilverStripe\SimplePdfPreview\SimplePdfPreviewImagickGenerator
    folderToSave: "assets/someOtherFolder/"
    imagePrefix: "pdf-foobar"

Injector:
  Ivoba\SilverStripe\SimplePdfPreview\SimplePdfPreviewImagickGenerator:
    class: Ivoba\SilverStripe\SimplePdfPreview\SimplePdfPreviewExecGenerator
```
You can create your own Generator class, simply implement ```SimplePdfPreviewGeneratorInterface```.
This could be the case, when we you dont want to use Imagick but ImageMagick directly via exec.

## Tests

To run tests for bundle standalone:

start the docker container:

    docker-compose run bash

inside the container run:

    SS_DATABASE_NAME=ss SS_DATABASE_PASSWORD=ss SS_DATABASE_SERVER=db SS_DATABASE_USERNAME=ss vendor/bin/phpunit

## Disclaimer

This extension is "simple" because it will just create a loose Image object.
Mapping happens over the filename. So its rather risky, but sufficient for most cases.

Somebody please make a better PDF extension, with a PDF File type, a PDF FileField and a preview image generation after upload. :)

## License

The MIT License (MIT). Please see [License File](https://github.com/ivoba/silverstripe-simple-pdf-preview/blob/master/LICENSE) for more information.
