# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-03-16

### Changed
- Added explicit `void` return types to command `handle()` methods
- Added explicit `void` return types to service provider `register()` and `boot()` methods
- Added parameter types to `CustomDumper::register()` method
- Made `compiledViewPath` nullable to handle edge cases
- Moved `DumpTestOnly` command to test-only service provider
- Added `prefer-stable: true` to composer.json
- Added Laravel 13 compatibility to Composer constraints and the test matrix
- Updated the release workflow to accept `vX.Y.Z` inputs and use the normalized tag in run names and releases
- Updated the release workflow to promote `[Unreleased]` into the requested version before tagging

### Fixed
- Fixed risky test warning by removing `ob_get_clean()` call in BasicTest
- Fixed `.phpunit.cache` gitignore pattern to ignore entire directory
- Fixed changelog release note parsing when the workflow is triggered with a `v`-prefixed version

### Removed
- Removed redundant `CliDumper` import alias
- Removed empty `dev` script from composer.json


## [1.0.1] - 2024

### Fixed
- PHP 8.1 compatibility fixes

## [1.0.0] - 2024

- Initial release

[Unreleased]: https://github.com/soloterm/dumps/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/soloterm/dumps/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/soloterm/dumps/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/soloterm/dumps/releases/tag/v1.0.0
