# Introduction

    
Documentation for the INCIDENsly 𝒘ebApp API.

This API provides endpoints for managing incidents, comments, tags, and user administration.
Authentication is handled via OAuth2 (Bearer token). Use the /login endpoint to obtain your token.
All responses are returned in JSON format.

## Quick Start

1. Register a new user via the /register endpoint to create an account and obtain an API token.
2. Use the /login endpoint to authenticate and receive an access token.
3. Include the token in the Authorization header (e.g. `Authorization: Bearer {YOUR_TOKEN}`) for all subsequent requests to protected endpoints.
4. Explore the available endpoints for managing incidences, comments, tags, and users.
5. Refer to the endpoint documentation for details on request parameters, response formats, and example requests and responses.

## Roles

- `user` — puede gestionar sus propias incidencias, comentarios y tags.
- `admin` — tiene acceso adicional a la gestión de usuarios.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>



