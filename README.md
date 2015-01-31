### Introduction

From the [CLDR project site](http://cldr.unicode.org/index):

> The Unicode CLDR provides key building blocks for software to support the world's languages, with the largest and most extensive standard repository of locale data available. This data is used by a wide spectrum of companies for their software internationalization and localization, adapting software to the conventions of different languages [...]

The core g11n implementation in Lithium already utilizes the CLDR indirectly through the [intl php extension](http://php.net/manual/en/book.intl.php), which makes use of [ICU](http://site.icu-project.org/), which in turn is based upon the data provided by the [CLDR](http://cldr.unicode.org/index). So at first sight there seems to be no need to access the CLDR directly. As more and more features are added to the intl extension it does not provide a way to access certain data yet. With the CLDR Catalog adapter contained in this plugin you can query the CLDR for this data. Currently there's support for:

 * Postal code validation rules for most countries (`validation.postalCode`).
 * List and translation of currencies (`currency`).
 * List and translation of languages (`language`).
 * List and translation of territories (`territory`).
 * List and translation of scripts (`script`).

### Installation

Clone the project into your libraries directory.
```
git clone code@rad-dev.org:li3_cldr.git /path/to/project/libraries/li3_cldr
```

Make your Lithium app aware of the plugin by adding the following line to your bootstrap/libraries.php file.
```
Libraries::add('li3_cldr');
```

## Resource Dependencies

Currently the plugin does not contain the actual CLDR data. We must take the following steps to install it. Download the ZIP file of the latest release, unpack it into a temporary directory and move the common directory into place.

```sh
curl http://unicode.org/Public/cldr/1.8.0/core.zip --O core.zip
unzip core.zip -d /tmp

mv /tmp/common /path/to/project/libraries/li3_cldr/resources/g11n
# ... or ...
mv /tmp/common /path/to/project/app/resource/g11n/cldr
```

