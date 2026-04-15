# Changelog

All notable changes to this project will be documented in this file.

## [0.0.1] - 2026-04-15

### Added
- Initial release.
- GET /_health/live — liveness probe.
- GET /_health/ready — readiness probe (DB, cache, maintenance mode).
- GET /_health/detail — full subsystem report.
- Checks: database read/write, cache, maintenance mode, disk space, queue, Elasticsearch/OpenSearch, session.
- Optional token protection for /_health/detail.
- Full page cache bypass for all /_health/* routes.
- Kubernetes and Docker examples in README.
