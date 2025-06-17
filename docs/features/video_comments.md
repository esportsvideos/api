💬 VideoComment

This resource manages comments attached to videos, including their moderation status.

---

## 🔐 Access Control

| Operation           | Route                                            | Access Condition     |
|---------------------|--------------------------------------------------|----------------------|
| `GET` (collection)  | `/videos/{videoId}/video_comments`               | Publicly accessible  |
| `POST` (collection) | `/videos/{videoId}/video_comments`               | Restricted to users  |
| `GET` (item)        | `/videos/{videoId}/video_comments/{id}`          | Publicly accessible  |
| `PATCH` (item)      | `/videos/{videoId}/video_comments/{id}`          | Restricted to users  |
| `DELETE` (item)     | `/videos/{videoId}/video_comments/{id}`          | Restricted to users  |
| `POST`              | `/videos/{videoId}/video_comments/{id}/moderate` | Restricted to admins |
| `DELETE`            | `/videos/{videoId}/video_comments/{id}/moderate` | Restricted to admins |

## 👁️ Fields Visibility

### 🔎 GET `/videos/{videoId}/video_comments`

Retrieves all comments associated with a given video, sorted by `createdAt` in **ascending order**:

| Field         | Anonymous / Normal User | Admin |
|---------------|-------------------------|-------|
| `id`          | ✅                       | ✅     |
| `comment`     | ✅ *                     | ✅     |
| `user`        | ✅                       | ✅     |
| `createdAt`   | ✅                       | ✅     |
| `updatedAt`   | ✅                       | ✅     |
| `moderatedAt` | ✅                       | ✅     |
| `moderatedBy` | ❌                       | ✅     |

*If the comment is **moderated** then `Anonymous / Normal User` will see the message : `This comment has been moderated.`.

### 🔎 GET `/videos/{videoId}/video_comments/{id}`

Returns detailed information about a single video comment, including:

- All fields listed in the collection response

### 📨 POST `/videos/{videoId}/video_comments/`

- Only authenticated users are allowed to post a comment.

The request uses the `VideoCommentDto` and is processed via `VideoCommentPostPersistProcessor`.

Payload must follow the structure defined in `groups: ['video_comment:write']`

Extra attributes are not allowed (`allow_extra_attributes: false`).

```
POST /videos/01975f6a-d76c-a6dd-fe26-10c5d1e87006/video_comments
{
    "comment": "Great video!"
}
```


### 🛠️ PATCH `/videos/{videoId}/video_comments/{id}`

- Only authenticated users are allowed to post a comment.
- Users cannot edit a moderated comment (will provide a `403`)

🔐 Admins can edit any comment.

The request uses the `VideoCommentDto` and is processed via `VideoCommentPatchPersistProcessor`.

Payload must follow the structure defined in `groups: ['video_comment:write']`

Extra attributes are not allowed (`allow_extra_attributes: false`).


```
PATCH /videos/01975f6a-d76c-a6dd-fe26-10c5d1e87006/video_comments/01976990-74be-6ff7-3d7c-b0ea9f351702
{
    "comment": "My new comment"
}
```

### 🗑️ DELETE `/videos/{videoId}/video_comments/{id}`

🚫 Users can delete only their own comments.

🔐 Admins can delete any comment.

## 🛠️ Moderate / Unmoderate a Comment

Moderation actions on comments are performed via dedicated endpoints that require no request body.

### Moderate a comment

- Send a **POST** request to `/videos/{videoId}/video_comments/{id}/moderate`.
- This marks the comment as moderated by setting `moderatedBy` and `moderatedAt`.
- If the comment is already moderated, the endpoint returns a `409 Conflict error`.

### Unmoderate a comment

- Send a **DELETE** request to the same endpoint `/videos/{videoId}/video_comments/{id}/moderate`.
- This removes moderation metadata, effectively restoring the comment to an unmoderated state by setting `moderatedBy` and `moderatedAt` at null.
- If the comment is not moderated, the endpoint returns a `409 Conflict error`.

> 🔒 Both actions require the user to have `ROLE_ADMIN` privileges.
> No request payload is needed; the operation is triggered solely by the HTTP method.
