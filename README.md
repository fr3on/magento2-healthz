# Magento 2 Healthz

Professional health check endpoints for Magento 2. Designed for Kubernetes liveness/readiness probes, Docker healthchecks, and load balancer monitoring.

## Features

- **Three Endpoints**: `/live`, `/ready`, and `/detail`.
- **Zero Configuration**: Works out of the box.
- **Fail-Safe**: Every check is wrapped in try/catch and has hard timeouts.
- **Fast**: Bypasses Magento layout rendering and Full Page Cache.
- **Extensible**: Add custom checks via `di.xml`.

## Installation

```bash
composer require fr3on/magento2-healthz
bin/magento module:enable Fr3on_Healthz
bin/magento setup:upgrade
```

## Endpoints

### 1. Liveness Probe: `GET /_health/live`
Answers: "Is the PHP process alive and Magento bootstrapped?"
- **Success**: `200 OK`
- **Use for**: Kubernetes `livenessProbe`.

### 2. Readiness Probe: `GET /_health/ready`
Answers: "Can this instance serve traffic right now?"
- **Checks**: Database connectivity, Cache backend, Maintenance mode.
- **Success**: `200 OK`
- **Failure**: `503 Service Unavailable`
- **Use for**: Kubernetes `readinessProbe`, Load Balancer health checks.

### 3. Detail Probe: `GET /_health/detail`
Answers: "What is the status of all subsystems?"
- **Checks**: All readiness checks + Disk space, Queue, Elasticsearch, Sessions.
- **Protection**: Can be protected by a token in Admin Config.
- **Use for**: Ops dashboards, post-deployment verification.

## Kubernetes Configuration

```yaml
livenessProbe:
  httpGet:
    path: /_health/live
    port: 80
  initialDelaySeconds: 30
  periodSeconds: 10

readinessProbe:
  httpGet:
    path: /_health/ready
    port: 80
  initialDelaySeconds: 10
  periodSeconds: 5
  failureThreshold: 3
```

## Configuration

Settings are available in **Stores > Configuration > Fr3on > Healthz**:
- **Enabled**: Enable/Disable endpoints.
- **Detail Token**: Secret token for the `/detail` endpoint.
- **Disk Threshold (GB)**: Warning threshold for free space in `var/`.
- **Check Timeout (ms)**: Max duration for any single check.

## License

MIT
