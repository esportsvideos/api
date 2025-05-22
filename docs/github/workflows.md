# ⚙️ GitHub Actions Workflow Overview

## Pull Requests

- On every pull request, the `qa.yaml` workflow is triggered.
- This workflow runs quality assurance checks (php-cs-fixer, phpstan &
  hadolint).

## Tagged Releases

- When a new tag or release is pushed:
    - A new PHP and Nginx Docker image is built and published.
    - The image is tagged with both the version (from the tag) and `latest`.
    - The previous `latest` image is re-tagged as `rollback`.

## Pull Requests with "Build Image" Label

- If a pull request is labeled with **`Build Image`**:
    - A new PHP/Nginx image is built.
    - The image is tagged as `PR-<ID_OF_PULL_REQUEST>`.
