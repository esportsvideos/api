<h1 align="center">Esports Videos - API</h1>

<div align="center">
    <a href="https://www.esports-videos.com">Website</a> •
    <a href="https://api.esports-videos.com">API</a>
</div>

💿 Prerequisites
---------------

- Install [Docker]

⚙️ Installation
---------------

1. Clone the repository to your local machine.
2. Run the following command to install and configure the necessary components:

```bash
make install
```

📃 Links
--------

| Service           | Link                         |
|-------------------|------------------------------|
| Nginx             | http://api.esv.localhost     |
| Traefik Dashboard | http://traefik.esv.localhost |
| Adminer*          | http://adminer.esv.localhost |

*Only available with [specific docker profile](docs/docker/compose.md)

## 📘 Documentation

Head over to the ~~full~~ documentation:

👉 [docs/README.md](docs/README.md)

## License

This project is under the MIT license. See the complete
license [in the bundle](LICENSE)

[Docker]: https://docs.docker.com/engine/install/
