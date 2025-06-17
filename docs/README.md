# 📚 Documentation

Welcome to the documentation ! Below you'll find key guides to help you
understand (or not) and work with our Docker setup and GitHub workflows.

---

## 🌟 Features

Documentation of API features and business rules per domain.

- [👤 Users](features/users.md)

This resource handles user registration and access to user information via the API.

- [🎬 Videos](features/videos.md)

This resource exposes video entities with their main attributes and associated comments.

- [💬 Video Comments](features/video_comments.md)

This resource manages comments attached to videos, including their moderation status.

---

## 🐳 Docker

- 📝 [compose](docker/compose.md)

Overview of the Docker Compose setup, including services, volumes, and
networking for local development.

- 🐘 [development-image](docker/development-image.md)

Details about the custom PHP development image, its differences with production,
and how it handles permissions and debugging tools.

- 🐞 [Xdebug](docker/xdebug.md)

Guide to configuring and using Xdebug for PHP debugging within the Docker
environment.

---

## 🧪 Fixtures

- [📚 Fixture Sizes & Strategy](fixtures/index.md)

### 📂 Available Fixtures

- [👤 UserFixtures](fixtures/UserFixtures.md)
- [🎬 VideoFixtures](fixtures/VideoFixtures.md)
- [💬 VideoCommentFixtures](fixtures/VideoCommentFixtures.md)

---

## 🛠️ Github

- ⚙️ [workflows](github/workflows.md)

Explanation of the GitHub Actions workflows.
