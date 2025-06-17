💬 VideoCommentFixtures

This fixture class creates sample comments for videos, including simple & moderated examples.
It is designed to test various behaviors such as moderation and visibility filtering.

---

## 🧾️ Video Comment Entries Overview

| Ulid                                   | Comment                                       | User         | Moderated |
|----------------------------------------|-----------------------------------------------|--------------|-----------|
| `01976990-74be-6ff7-3d7c-b0ea9f351702` | `This is a simple comment`                    | User         | false     |
| `01976453-4432-0a4c-fdf9-85897c60b81d` | `This is a simple moderated comment.`         | User         | true      |
| `019779c6-bed8-116e-a5d9-0081d6e2edd5` | `This is a simple comment from another user.` | Another User | false     |

Depending on the fixture size, the class generates additional videos as follows:

- For **small** size, it creates **10 videos** with random data.
- For *larger* sizes, the number of videos increases accordingly (see [index.md](index.md) - *Available Sizes*).
