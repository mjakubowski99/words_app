## Flashcard categories

GET /api/flashcards/user/categories?page=1

This endpoint will return user general category data and generated categories 
with assigned active learning sessions to them if active session was returned 
there is no need to create a new learning session.

```json
{
  "data": {
    "general_category": {
      "id": "id",
      "name": "string",
      "nice_name": "string",
      "active_session_id": "uuid|null"
    },
    "categories": [
          {
            "id": "id",
            "name": "string",
            "nice_name": "string",
            "active_session_id": "uuid|null"
          }
    ],
    "page": 1,
    "per_page": 15
  }
}
```

## Get all flashcards for category(optional)
GET /api/flashcards/user/categories/{category_id}?page=1

```json
{
  "data": [
    {
      "id": "uuid",
      "base_lang": "str",
      "base_word": "string",
      "translation_lang": "string",
      "translation_word": "string"
    }
  ]
}
```

## Create flashcards session

This endpoint will only create new session in category when other session does not exists.

POST /api/flashcards/session

Request:
```json
{
  "category_id": "id",
  "cards_per_session": "int"
}
```

Response 200:
```json
{
  "data": {
    "session": {
      "id": "uuid",
      "category_id": "uuid",
      "is_finished": "bool",
      "cards_progress": "int",
      "cards_per_session": "int"
    },
    "next_flashcards": [
      {
        "id": "uuid",
        "base_lang": "str",
        "base_word": "string",
        "translation_lang": "string",
        "translation_word": "string"
      }
    ],
    "stats": {}
  }
}
```

Response 400:
```json
{
  "message": "Other active session exists"
}
```

## Get flashcards session

This endpoint will get flashcards session details

GET /api/flashcards/sessions/{session_id}

Same format as /api/flashcards/sessions

## Flashcards bulk rate

POST /api/flashcards/sessions/{session_id}/bulk-rate

Request:
```json
{
  "ratings": [
    {
        "session_flashcard_id": "string",
        "rating": "FlashcardRating"
    }
  ]
}
```

Response 200:
```json
{
  "data": {
    "session": {
      "id": "uuid",
      "is_finished": "bool",
      "rated_count": "int",
      "per_session": "int"
    },
    "next_flashcards": [
      {
        "id": "uuid",
        "base_lang": "str",
        "base_word": "string",
        "translation_lang": "string",
        "translation_word": "string"
      }
    ],
    "stats": {}
  }
}
```

Response 400:
```json
{
  "message": "Not all flashcards belongs to user or session"
}
```




