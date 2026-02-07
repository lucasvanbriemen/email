# Email Plugin API Implementation

This document describes the implementation of the Email API endpoints that allow external AI agents to query and retrieve email data from the email client database.

## Overview

The API provides two main endpoints that AI agents can call to search emails and retrieve full email content:

- **Search Emails**: `GET /api/emails/search` - Search emails with various filters
- **Get Full Email**: `GET /api/emails/{id}` - Retrieve the complete content of an email

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
EMAIL_API_BASE_URL=http://localhost:3000
EMAIL_API_AUTH_TOKEN=your-secret-token-here
```

- `EMAIL_API_BASE_URL`: The base URL where the email client API is hosted (used by AI agents)
- `EMAIL_API_AUTH_TOKEN`: Bearer token for API authentication (optional - if not set, API is open)

## Endpoints

### 1. Search Emails

**Endpoint**: `GET /api/emails/search`

**Authentication**: Bearer token (optional, configured via `EMAIL_API_AUTH_TOKEN`)

**Query Parameters**:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `keyword` | string | No | Search in subject and body |
| `sender` | string | No | Filter by sender email address |
| `from_date` | string | No | Start date (YYYY-MM-DD format) |
| `to_date` | string | No | End date (YYYY-MM-DD format) |
| `unread_only` | boolean | No | Only return unread emails |

**Example Requests**:

```bash
# Search for keyword
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/search?keyword=lunch"

# Filter by sender and date
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/search?sender=john@company.com&from_date=2026-02-05&to_date=2026-02-06"

# Get unread emails only
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/search?unread_only=true"
```

**Response** (HTTP 200):

```json
{
  "count": 2,
  "emails": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "subject": "Team Lunch This Friday",
      "sender": "hr@company.com",
      "date": "2026-02-05",
      "preview": "Hi Everyone, We're organizing a team lunch this Friday at noon at the Italian restaurant downtown...",
      "unread": false
    },
    {
      "id": "6ba7b810-9dad-11d1-80b4-00c04fd430c8",
      "subject": "Lunch Plans",
      "sender": "john@company.com",
      "date": "2026-02-06",
      "preview": "Let's grab lunch at the Italian place downtown...",
      "unread": true
    }
  ]
}
```

**Response Fields**:
- `count` (integer): Total number of matching emails
- `emails` (array): List of email summaries (max 10 per request)
  - `id` (string): Email UUID identifier
  - `subject` (string): Email subject
  - `sender` (string): Sender email address
  - `date` (string): Email date (YYYY-MM-DD format)
  - `preview` (string): First ~100 characters of email body (HTML tags stripped)
  - `unread` (boolean): Whether the email is unread

---

### 2. Get Full Email

**Endpoint**: `GET /api/emails/{id}`

**Authentication**: Bearer token (optional, configured via `EMAIL_API_AUTH_TOKEN`)

**Path Parameters**:
- `id` (string): The email UUID to retrieve

**Example Request**:

```bash
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/550e8400-e29b-41d4-a716-446655440000"
```

**Response** (HTTP 200):

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "subject": "Team Lunch This Friday",
  "sender": "hr@company.com",
  "date": "2026-02-05",
  "body": "Hi Everyone,\n\nWe're organizing a team lunch this Friday at noon at the Italian restaurant downtown. Please RSVP with your dietary preferences.\n\nLooking forward to seeing you there!"
}
```

**Response Fields**:
- `id` (string): Email UUID
- `subject` (string): Email subject
- `sender` (string): Sender email address
- `date` (string): Email date (YYYY-MM-DD format)
- `body` (string): Full email body (HTML tags stripped, newlines preserved)

**Error Response** (HTTP 404):

```json
{
  "error": "Email with ID '550e8400-e29b-41d4-a716-446655440000' not found"
}
```

---

## Error Handling

All endpoints return appropriate HTTP status codes:

| Status | Meaning | Example |
|--------|---------|---------|
| 200 | Success | Request processed successfully |
| 400 | Bad Request | Invalid date format or query parameter |
| 401 | Unauthorized | Invalid or missing authentication token |
| 404 | Not Found | Email with specified ID not found |

**Error Response Format**:

```json
{
  "error": "Human-readable error message"
}
```

---

## Authentication

### Enabling Token Authentication

To require authentication for the API, set the `EMAIL_API_AUTH_TOKEN` environment variable:

```env
EMAIL_API_AUTH_TOKEN=your-secret-token
```

Clients must include the token in the `Authorization` header:

```
Authorization: Bearer your-secret-token
```

### Disabling Authentication

If `EMAIL_API_AUTH_TOKEN` is not set or is empty, the API endpoints are publicly accessible without authentication.

---

## Implementation Details

### Files Added/Modified

**New Files**:
- `app/Http/Controllers/EmailApiController.php` - Controller handling search and retrieval logic
- `app/Http/Middleware/EmailApiAuth.php` - Bearer token authentication middleware
- `tests/Feature/EmailApiTest.php` - Feature tests for the API

**Modified Files**:
- `routes/api.php` - Added routes for the email API endpoints
- `.env` - Added EMAIL_API configuration variables

### Key Features

1. **Flexible Search**: Query emails by keyword, sender, date range, and read status
2. **Authentication**: Optional bearer token authentication
3. **HTML Stripping**: HTML tags are automatically removed from email bodies for plain text
4. **Date Handling**: Automatic conversion of dates to YYYY-MM-DD format
5. **Error Handling**: Detailed error messages for invalid requests

### Database Queries

The implementation uses Laravel Eloquent ORM with efficient queries:

- Search queries use `where` and `orWhere` clauses for flexible filtering
- Related models (sender information) are loaded with `load()` for optimal performance
- Results are limited to 10 items per search request

---

## Testing

Run the test suite to verify the API:

```bash
php artisan test tests/Feature/EmailApiTest.php
```

Tests cover:
- Searching without filters
- Searching with keywords
- Filtering by sender
- Filtering by date range
- Filtering unread emails only
- Retrieving full email content
- Error handling for non-existent emails
- Authentication validation

---

## Usage Examples

### Python Example

```python
import requests
from datetime import datetime, timedelta

BASE_URL = "http://localhost:8000"
AUTH_TOKEN = "test-ai-agent-token"

headers = {
    "Authorization": f"Bearer {AUTH_TOKEN}",
    "Content-Type": "application/json"
}

# Search for emails from a specific sender
response = requests.get(
    f"{BASE_URL}/api/emails/search",
    params={
        "sender": "john@example.com",
        "unread_only": "true"
    },
    headers=headers
)

emails = response.json()
print(f"Found {emails['count']} unread emails from john@example.com")

# Get the full content of the first email
if emails['emails']:
    email_id = emails['emails'][0]['id']
    response = requests.get(
        f"{BASE_URL}/api/emails/{email_id}",
        headers=headers
    )
    email = response.json()
    print(f"Subject: {email['subject']}")
    print(f"Body: {email['body']}")
```

### JavaScript Example

```javascript
const BASE_URL = "http://localhost:8000";
const AUTH_TOKEN = "test-ai-agent-token";

const headers = {
  "Authorization": `Bearer ${AUTH_TOKEN}`,
  "Content-Type": "application/json"
};

// Search for emails with a keyword
const searchParams = new URLSearchParams({
  keyword: "meeting",
  from_date: "2026-02-05",
  to_date: "2026-02-07"
});

fetch(`${BASE_URL}/api/emails/search?${searchParams}`, { headers })
  .then(res => res.json())
  .then(data => {
    console.log(`Found ${data.count} emails`);
    data.emails.forEach(email => {
      console.log(`- ${email.subject} (from ${email.sender})`);
    });
  });
```

---

## Security Considerations

1. **Bearer Token**: The API uses HTTP Bearer token authentication. In production, use HTTPS to prevent token interception.
2. **CORS**: If the API is accessed from a different domain, ensure CORS is properly configured.
3. **Rate Limiting**: Consider implementing rate limiting for public/shared API tokens.
4. **Data Access**: All email data is returned as-is. Implement additional access controls if needed.

---

## Future Enhancements

- Pagination support for large result sets
- Advanced filtering (by attachment presence, priority, etc.)
- Email marking as read/unread
- Attachment retrieval endpoints
- Webhook notifications for new emails
- API key management dashboard
