# Email API - Quick Start Guide

## What's Been Implemented

A complete REST API that allows external AI agents to search and retrieve emails from your email client database.

## Endpoints

### Search Emails
```
GET /api/emails/search
```

**Query Parameters**:
- `keyword` - Search in subject and body
- `sender` - Filter by sender email address
- `from_date` - Start date (YYYY-MM-DD)
- `to_date` - End date (YYYY-MM-DD)
- `unread_only` - Boolean, only unread emails

**Example**:
```bash
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/search?keyword=lunch&from_date=2026-02-05"
```

### Get Full Email
```
GET /api/emails/{uuid}
```

**Example**:
```bash
curl -H "Authorization: Bearer test-ai-agent-token" \
  "http://localhost:8000/api/emails/550e8400-e29b-41d4-a716-446655440000"
```

## Configuration

The API is configured via these environment variables (already set in `.env`):

```env
EMAIL_API_BASE_URL=http://localhost:3000
EMAIL_API_AUTH_TOKEN=test-ai-agent-token
```

**To disable authentication** (allow anyone to call the API):
- Remove or comment out `EMAIL_API_AUTH_TOKEN` in `.env`

**To change the token**:
- Set `EMAIL_API_AUTH_TOKEN=your-custom-token` in `.env`

## Testing

Run the test suite:
```bash
php artisan test tests/Feature/EmailApiTest.php
```

## What AI Agents Can Do

1. **Search emails** with flexible filters
2. **Read full email content** by UUID
3. **Find unread emails** only
4. **Filter by date range** and sender
5. **Search by keywords** in subject and body

## Response Format

### Search Response
```json
{
  "count": 2,
  "emails": [
    {
      "id": "uuid-string",
      "subject": "Email Subject",
      "sender": "sender@example.com",
      "date": "2026-02-05",
      "preview": "First 100 characters of email...",
      "unread": false
    }
  ]
}
```

### Full Email Response
```json
{
  "id": "uuid-string",
  "subject": "Email Subject",
  "sender": "sender@example.com",
  "date": "2026-02-05",
  "body": "Full email body content..."
}
```

## Error Responses

| Status | Meaning |
|--------|---------|
| 200 | Success |
| 400 | Invalid parameters |
| 401 | Invalid/missing token |
| 404 | Email not found |

## Files Added

- `app/Http/Controllers/EmailApiController.php` - Main API controller
- `app/Http/Middleware/EmailApiAuth.php` - Authentication middleware
- `tests/Feature/EmailApiTest.php` - Test suite
- `API_IMPLEMENTATION.md` - Detailed documentation

## Next Steps

1. Test the API locally:
   ```bash
   php artisan serve
   ```

2. Call the endpoints from your AI agent with the Bearer token

3. Review `API_IMPLEMENTATION.md` for complete documentation

## Security Notes

- **Use HTTPS in production** - Bearer tokens can be intercepted
- **Keep your token secret** - Treat it like a password
- **Consider rate limiting** for public APIs
- **Token is optional** - If not set, API is open to everyone
