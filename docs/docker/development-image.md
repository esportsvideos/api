# 🐘 Docker – Development Image

The `php` service in the development `compose.yaml` uses a dedicated image
tagged with the `-dev` suffix:
[`ghcr.io/esportsvideos/php:X.X.X-dev`](https://github.com/esportsvideos/php).

This development image is based on the same base as the production image, but
includes fewer packages — only what's needed for local development and
debugging.

---

## 🔧 `su-exec`

The `su-exec` binary is included to solve permission issues when mounting
volumes between the host and container.

By default, files created inside containers might be owned by `root`, especially
if your local user has a UID like `1001` or higher. To prevent this,
we [adjust the UID/GID of the
`www-data` user at container startup](https://github.com/esportsvideos/php/blob/main/docker-entrypoint.dev.sh),
so files created inside the container match your host user's permissions.

---

## 🐞 Xdebug

Xdebug is installed but disabled by default via the `XDEBUG_MODE=off`
environment variable.

To enable it in development, update the `compose.override.yaml` file with :

```yaml
    php:
        environment:
            XDEBUG_MODE: "debug"
```

See [this file](xdebug.md) for more information about xdebug.
