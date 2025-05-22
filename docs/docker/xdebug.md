# 🐞 Xdebug

Xdebug is installed but disabled by default via the `XDEBUG_MODE=off`
environment variable.

To enable it, update the `compose.override.yaml` file with :

```yaml
    php:
        environment:
            XDEBUG_MODE: "debug"
```

### 🔧 Configure your navigator

To enable debugging, you need to install a browser extension compatible with
your IDE.

For detailed instructions and download links, visit the official JetBrains
guide:

[Browser Debugging Extensions](https://www.jetbrains.com/help/phpstorm/browser-debugging-extensions.html)

### 🐘 Configure PhpStorm

Coming soon (promise).
