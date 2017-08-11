# users-service


## Endpoints

**PUT /users** creates user
```json
{
  "name": "John Doe",
  "email": "john.doe@domain.com",
  "isActive": true
}
```

When success it returns:
```json
{
  "status": "success"
}
```
... and it creates an event:
```
{
  "name": "UserCreated",
  "occuredAt": "2017-07-16T11:02:05+02:00",
  "data": {
    "name": "John Doe",
    "email": "john.doe@domain.com",
    "isActive": true
  }
}
```

When request failed it returns:
```json
{
  "status":"failed",
  "name": [
    //  errors list
  ],
  "email": [
    //  errors list
  ],
  "isActive": [
    //  errors list
  ]
}
```

**POST /users** update user with specified email address
```json
{
  "name": "John Doe",
  "email": "john.doe@domain.com",
  "isActive": false
}
```


When success it returns:
```json
{
  "status": "success"
}
```
... and it creates an event:
```
{
  "name": "UserUpdated",
  "occuredAt": "2017-07-16T11:02:05+02:00",
  "data": {
    "name": "John Doe",
    "email": "john.doe@domain.com",
    "isActive": true
  }
}
```

When request failed it returns:
```json
{
  "status":"failed",
  "name": [
    //  errors list
  ],
  "email": [
    //  errors list
  ],
  "isActive": [
    //  errors list
  ]
}
```


**GET /users?email=john.doe@domain.com** fetches user with specified email address

It returns:
```json
{
  "status": "success",
  "name": "John Doe",
  "email": "john.doe@domain.com",
  "isActive": false
}
```

When request failed it returns:
```json
{
  "status":"failed",
  "email": [
    //  errors list
  ]
}
```
