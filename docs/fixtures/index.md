# 🧪 Fixtures Overview

---

## 📏 Available Sizes

You can adjust the amount of generated data using the `FIXTURES_SIZE` environment variable:

| Size        | Description                                     | Number of Records |
|-------------|-------------------------------------------------|-------------------|
| 🕳️ `none`  | No data (useful for cold start / clean testing) | 0                 |
| 🟢 `small`  | Minimal data                                    | 10                |
| 🟡 `medium` | Standard data for development                   | 100               |
| 🔵 `large`  | More complete dataset                           | 1000              |
| 🟣 `xl`     | Used for stress/performance tests               | 10000             |
| 🔴 `xxl`    | Heavy dataset (e.g. for pagination)             | 100000            |


## ⚙️ Usage

Run the following Make commands to load fixtures:

```bash
make db-fixtures # for none
make db-fixtures-small
make db-fixtures-medium
make db-fixtures-large
make db-fixtures-xl
make db-fixtures-xxl
```

## 📚 Fixture Files

Each of these documents a specific fixture class:

- 👤 [UserFixtures](UserFixtures.md)
- 🎬 [VideoFixtures](VideoFixtures.md)

## 📝 Notes

- Fixtures use 🧙 [Foundry](https://github.com/zenstruck/foundry) and 🤖 [Faker](https://fakerphp.github.io/).
