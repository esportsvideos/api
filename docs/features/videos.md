# 🎬 Video

This resource exposes video entities with their main attributes and associated comments.

---

## 🔐 Access Control

| Operation          | Route          | Access Condition    |
|--------------------|----------------|---------------------|
| `GET` (collection) | `/videos`      | Publicly accessible |
| `GET` (item)       | `/videos/{id}` | Publicly accessible |


## 👁️ Fields Visibility

### 🔎 GET `/video/`

Returns the collection of videos with the following fields visible:

| Field                | Anonymous / Normal User | Admin | Description      |
|----------------------|-------------------------|-------|------------------|
| `id`                 | ✅                       | ✅     |                  |
| `title`              | ✅                       | ✅     |                  |
| `description`        | ✅                       | ✅     |                  |
| `duration`           | ✅                       | ✅     |                  |
| `releaseDate`        | ✅                       | ✅     |                  |
| `createdAt`          | ✅                       | ✅     |                  |
| `updatedAt`          | ✅                       | ✅     |                  |
| `createdBy`          | ✅                       | ✅     |                  |
| `updatedBy`          | ✅                       | ✅     |                  |
| `videoComments`      | ✅                       | ✅     | Link to comments |
| `videoCommentsCount` | ✅                       | ✅     |                  |

### 🔎 GET `/video/{id}`

Returns detailed information about a single video, including:

- All fields listed in the collection response
- The list of associated video comments (via `/videos/{videoId}/video_comments` endpoint)
