# 👤 Users

This resource handles user registration and access to user information via the API.

---

## 🔐 Access Control

| Operation             | Route         | Access Condition                                                                  |
|-----------------------|---------------|-----------------------------------------------------------------------------------|
| `GET` (item)          | `/users/{id}` | **Publicly accessible**, but the exposed fields depend on the current user's role |
| `GET` (collection)    | `/users`      | Restricted to users with **ROLE\_ADMIN**                                          |
| `POST` (registration) | `/users`      | Allowed if **unauthenticated** or user has **ROLE\_ADMIN**                        |

## 👁️ Fields Visibility

### 🔎 GET `/users/{id}`

| Field           | Anonymous / Normal User | Admin |
|-----------------|-------------------------|-------|
| `id`            | ✅                       | ✅     |
| `username`      | ✅                       | ✅     |
| `country`       | ✅                       | ✅     |
| `email`         | ❌                       | ✅     |
| `emailVerified` | ❌                       | ✅     |
| `createdAt`     | ✅                       | ✅     |
| `updatedAt`     | ✅                       | ✅     |
| `password`      | ❌                       | ❌     |
| `roles`         | ❌                       | ❌     |

### 🔎 GET `/users/`

- Returns a **collection of users.**
- Access restricted to **admins only.**
- Each user in the collection includes all fields visible to admins (same as in the Admin column of `GET /users/{id}`).

## 🧾 Registering a User

To register a new user:

- Send a POST request to `/users`
- You must be:
    - Not authenticated (`IS_AUTHENTICATED_ANONYMOUSLY`), or
    - An admin (`ROLE_ADMIN`)

The request uses the `UserRegistrationDto` and is processed via `UserRegistrationPersistProcessor`.

Payload must follow the structure defined in `groups: ['user:write']`
Extra attributes are not allowed (`allow_extra_attributes: false`).

> 🛡️ The processor will handle logic such as hashing the password and persisting the user.

## 📧 Email Verification Flow
Once the registration is complete:

- A verification email is sent automatically to the provided address.

- The user must click the verification link to activate their account.

- The link contains:
  - A secure hash
  - An expiration timestamp

### 🔗 Example verification URL

```bash
GET /users/{id}/verify/email?_expiration=123456789&_hash=abc123
```

The account is considered "**unverified**" until the user clicks the *verification link*.
