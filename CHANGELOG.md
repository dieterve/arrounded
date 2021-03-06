# CHANGELOG

## 0.3.0 - 2014-10-10

### Added
- Added core "Arrounded" class with various reflection methods
- Added AbstractTransformer and DefaultTransformer
- Added `IllustrableInterface` for models implementing `Illustrable`

### Changed
- Abstract controllers were moved to `Abstracts\Controllers` (and all prefixed with Abstract)
- `Models\Upload` was moved to `Abstracts\Models\AbstractUploadModel`
- `Abstracts\AbstractModel` was moved to `Abstracts\Models\AbstractModel`

### Fixed
- Fixed a bug in ReflectionModel::hasTrait

## 0.2.2 - 2014-09-26

### Added
- Allow string boolean presenters

### Changed
- Changes to how soft-deletes are handled
- Work on presenters

## 0.2.1 - 2014-09-19

### Added
- Add statistics and charts classes
- Added `Collection::distribution`

### Changed
- Make administration templates compatible with Angular
- Pass the received attributes to `AbstractForm::getRules`

## 0.2.0 - 2014-09-15

### Added
- Added Chart and Statistics classes
- Added Metadata class
- Add support for placeholders in uploads

### Changed
- Delegate everything API related to Dingo API
- Delegate everything uploads related to Stapler
- Delegate foreign keys migration to ForeignKeysMigrator

### Removed
- Remove some unused traits

### Fixed
- Various bugfixes

## 0.1.0 - 2014-09-10

### Added
- Initial tagged release
