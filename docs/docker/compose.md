# 📝 Docker Compose – Development Environment

This `compose.yaml` file defines the local development environment for the
project. It includes essential services: Traefik (reverse proxy), PostgreSQL,
PHP, Nginx, and Adminer.

**In most cases, you don’t need to modify your hosts file because
the `.localhost` domain automatically resolves to `127.0.0.1`.**

---

## 📃 Links

If you keep port 80 defined in `compose.override.yaml`, you’ll need to specify
the port manually in the URL (e.g., http://api.esv.localhost:8080 for 8080
port).

| Service           | Link                         | Profile |
|-------------------|------------------------------|---------|
| Nginx             | http://api.esv.localhost     |         |
| Traefik Dashboard | http://traefik.esv.localhost |         |
| Adminer           | http://adminer.esv.localhost | debug   |

## 🛠️ Profiles

We use a `debug` profile to optionally include development-only services.
You can enable profiles when running Compose using the `--profile` flag:

```bash
docker compose --profile debug up -d
```

To start **all** services regardless of their profile:

```bash
docker compose --profile="*" up -d
```

Alternatively, you can use the provided Makefile command:

```bash
make start-all
```

For more information about Compose profiles, check
the [official documentation](https://docs.docker.com/compose/how-tos/profiles/)

## Services

### 🔀 Traefik (Reverse Proxy)

- **Image**: `traefik:v3.4`
- Handles routing to other services (Nginx, Adminer, ...).
- Exposes the Traefik dashboard at [
  `http://traefik.esv.localhost`](http://traefik.esv.localhost).
- Uses Docker as a dynamic configuration provider.

> ⚠️ The Traefik dashboard is exposed with `--api.insecure=true`. This should *
*only be used in development**.

---

### 🌐 Nginx (Web Server)

- **Image**: `nginx:1.27.5-alpine`
- Loads configuration from a custom template* at
  `docker/nginx/templates/default.conf.template`.
- Routed through Traefik at [
  `http://api.esv.localhost`](http://api.esv.localhost).

*See [docker nginx documentation] `fastcgi_pass` need to have a static string of
the php fpm host, so we use the environment variable.

---

### 🐘 PHP (Application Runtime)

- **Image**: `ghcr.io/esportsvideos/php:1.0.0-dev`
- Mounts the full project directory to `/var/www` inside the container.
- `XDEBUG_MODE` is disabled by default.
- Adds a host entry for `host.docker.internal` to allow communication with the
  host machine (useful for debugging tools).

We maintain a dedicated PHP image in a separate repository because the specific
parts of the build are always the same. This allows us to cache them and
significantly reduce build times.

---

### 🛠️ Adminer (Database GUI – optional)

- **Image**: `adminer:4.17.1`
- Only started when the `debug` profile is enabled with `make start-all``
- We use the version 4.17.1 because newer versions have an issue with the design
  env var
- Routed through Traefik at [
  `http://adminer.esv.localhost`](http://adminer.esv.localhost).

[docker nginx documentation]: https://github.com/docker-library/docs/tree/master/nginx#using-environment-variables-in-nginx-configuration-new-in-119
