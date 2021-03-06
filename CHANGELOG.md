# Change Log
[Package Checklist](http://phppackagechecklist.com/#1,2,3,4,6,7,8,9,10,11,12,13,14)

## [0.4.0] - 4 Jul 2017
### Added
- Laravel 5.4 compatibility.
- automatic policy detection, making Entity seeding unnecessary.
- automatic adding of `created_by` column in tables, regardless of models.
- PHPCI integration.
- initial set of tests, with more to come.
- config file documentation.

### Fixed
- role editing form.

### Changed
- views to use the projects master layout file and be easier to publish.
- config settings to be more appropriate.
- controllers and models with PHP7 stuff and general clean up.
- routes to use dedicated folder.
- traits to use dedicated folder.
- policies to be much more concise, no methods needed.
- config variables to be more consistent.
- updated README documentation.

## [0.3.0] - 24 Jan 2016
### Added
- `$incrementing = false;` to classes using `name` as primary key.
- use statements in migrations.

### Changed
- added routes to `web` route group.
- creation of `$user` using helper methods `app()` and `config()`.

## [0.2.0 - 0.2.1] - 2 Dec 2015
### Fixed
- reference to User model primary key to not use `$user->id`, but instead `$user->getKey()`.

### Changed
- installation instructions to make sure a User exists in the system.
- installation instructions for adding Entities.
- views to better resemble Spark Settings components, so they can be integrated into Spark.

### Removed
- entities view, as it wasn't conducive to a good workflow.
- requirement to manually add `Collective\Html` package.

## [0.1.8] - 29 Sep 2015
### Added
- "laravelcollective/html" as a package dependency.
- moved documentation to https://governor.forlaravel.com.

## [0.1.0] - 1 Sep 2015
### Added
- Commit initial code, derived from the Bones-Keeper package.
