# 👤 UserFixtures

This fixture class creates test users with various roles and attributes.

All users share the same default password: **`esvESV123456`**.

---

## 👥 User Accounts Overview

| Ulid                                   | Email                      | Password       | Role       |
|----------------------------------------|----------------------------|----------------|------------|
| `019712ef-fb77-347f-1cd9-2b1c22d259e6` | `admin@esports-videos.com` | `esvESV123456` | ROLE_ADMIN |
| `019712ef-fb78-602c-a546-f7c694bd83be` | `user@esports-videos.com`  | `esvESV123456` | ROLE_USER  |

Depending on the fixture size, the class generates additional users as follows:

- For **small** size, it creates **10 users** with the role `ROLE_USER`.
- For *larger* sizes, the number of users increases accordingly (see [index.md](index.md) - *Available Sizes*).
