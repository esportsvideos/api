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

### 🔧 Configure Postman

To enable Xdebug debugging when sending requests via Postman, you simply need to add a specific cookie in the request headers. This cookie tells the server to start an Xdebug session.

You can automate this by using a `Pre-request Script` in Postman:

```javascript
if (pm.collectionVariables.get("IS_XDEBUG_ACTIVE") == "1") {
    pm.request.headers.add({
        key: "Cookie",
        value: "XDEBUG_SESSION=YOUR-REQUEST"
    });
}
```

`IS_XDEBUG_ACTIVE`: a collection variable you create in Postman to easily toggle debugging on or off without changing the script.

1. Create a collection variable named IS_XDEBUG_ACTIVE with value "1" to enable debugging, or "0" to disable it.
2. Add this script in the Pre-request Script tab of your Postman collection or individual requests.
3. Ensure that `XDEBUG_MODE` is "debug" in the php container
4. Listen for debug in PhpStorm
5. Send the request from Postman — if debugging is enabled, Xdebug will start and PhpStorm will catch the debug session.

### 🐘 Configure PhpStorm

Coming soon (promise).
