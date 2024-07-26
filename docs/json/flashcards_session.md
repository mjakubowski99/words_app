
## Create flashcard session

Example payload
```json
{
    "data": {
        "flashcard_set_id": "123"
    }
}
```

Example responses:

Status: 200
```json
{
    "data": {
        "session_id": "1234"
    }
}
```
Status: 400
```json
{
    "message": "One session can be created at time"
}
```

## Get current session id

```json
{
    "data": {
        "flashcard_set_id": "123"
    }
}
```
