# TYPO3 Extension ayacoo_soundcloud

## 1 Features

* Soundcloud audios can be created as a file in the TYPO3 file list
* Soundcloud audios can be used and output with the text with media element

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your [Composer][1] based TYPO3 project:

```
composer require ayacoo/ayacoo-soundcloud
```

### 2.2 Hints

#### Output

For the output, the HTML is used directly from [Soundcloud][4].

#### SQL changes

In order not to have to access the oEmbed interface permanently, four fields are added to the sys_file_metadata table

## 3 Administration corner

### 3.1 Versions and support

| AyacooSoundcloud | TYPO3       | PHP       | Support / Development                   |
|------------------|-------------| ----------|---------------------------------------- |
| 1.x              | 11.x | 7.4 - 8.0 | features, bugfixes, security updates    |

### 3.2 Release Management

ayacoo_soundcloud uses [**semantic versioning**][2], which means, that

* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking
  changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes which can be refactorings, features or bugfixes.

### 3.3 Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an issue and connect it to your pull
requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

**Bugfixes**: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue. We're
going
to accept only bugfixes if we can reproduce the issue.

## 4 Thanks / Notices

Special thanks to Georg Ringer and his [news][3] extension. A good template to build a TYPO3 extension. Here, for
example, the structure of README.md is used.


[1]: https://getcomposer.org/

[2]: https://semver.org/

[3]: https://github.com/georgringer/news

[4]: https://developers.soundcloud.com/docs/oembed
