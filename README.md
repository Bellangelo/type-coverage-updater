# Type Coverage Updater

## Overview
Type Coverage Updater is a helper script designed to automatically update the type coverage 
of your PHP project, ensuring your codebase maintains a specified level of type safety. 
It works as an extension of [Tomas Votruba's type-coverage](https://github.com/TomasVotruba/type-coverage),
automating the process of checking and updating the type coverage 
percentage in your project's configuration.

## Installation
Install the package via Composer:

```bash
composer require bellangelo/type-coverage-updater
```

## Usage
Run the updater script to automatically check and update the type coverage:

```bash
vendor/bin/type-coverage-updater
```
This script will analyze your project, update the type coverage percentage in 
your configuration file, and ensure your codebase meets the specified type safety standards.