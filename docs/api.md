# Todo API仕様書

## Base URL
```
http://localhost/api
```

## 認証について
すべてのAPIリクエストには以下のヘッダーが必要：
```
Authorization: Bearer {token}
```
トークンはログインAPIで取得できます。

## エンドポイント一覧

### 認証系API

#### ユーザー登録
- **URL**: `/register`
- **Method**: POST
- **Headers**:
  - Accept: application/json
  - Content-Type: application/json
- **Request Body**:
```json
{
  "name": "ユーザー名",
  "email": "メールアドレス",
  "password": "パスワード",
  "password_confirmation": "パスワード(確認用)"
}
```
- **Response**: 201 Created
```json
{
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "メールアドレス",
    "created_at": "2025-04-13T12:00:00.000000Z",
    "updated_at": "2025-04-13T12:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz..."
}
```

#### ログイン
- **URL**: `/login`
- **Method**: POST
- **Headers**:
  - Accept: application/json
  - Content-Type: application/json
- **Request Body**:
```json
{
  "email": "メールアドレス",
  "password": "パスワード"
}
```
- **Response**: 200 OK
```json
{
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "メールアドレス",
    "created_at": "2025-04-13T12:00:00.000000Z",
    "updated_at": "2025-04-13T12:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz..."
}
```

#### ログアウト
- **URL**: `/logout`
- **Method**: POST
- **Headers**:
  - Accept: application/json
  - Authorization: Bearer {token}
- **Response**: 200 OK
```json
{
  "message": "Tokens Revoked"
}
```

### Todo系API

#### Todo一覧取得
- **URL**: `/todos`
- **Method**: GET
- **Headers**:
  - Accept: application/json
  - Authorization: Bearer {token}
- **Response**: 200 OK
```json
{
  "data": [
    {
      "id": 1,
      "title": "買い物に行く",
      "completed": false,
      "completed_at": null,
      "user_id": 1,
      "created_at": "2025-04-13T12:00:00.000000Z",
      "updated_at": "2025-04-13T12:00:00.000000Z"
    },
    {
      "id": 2,
      "title": "筋トレする",
      "completed": true,
      "completed_at": "2025-04-13T13:00:00.000000Z",
      "user_id": 1,
      "created_at": "2025-04-13T11:00:00.000000Z",
      "updated_at": "2025-04-13T13:00:00.000000Z"
    }
  ]
}
```

#### 特定のTodo取得
- **URL**: `/todos/{id}`
- **Method**: GET
- **Headers**:
  - Accept: application/json
  - Authorization: Bearer {token}
- **Response**: 200 OK
```json
{
  "data": {
    "id": 1,
    "title": "買い物に行く",
    "completed": false,
    "completed_at": null,
    "user_id": 1,
    "created_at": "2025-04-13T12:00:00.000000Z",
    "updated_at": "2025-04-13T12:00:00.000000Z"
  }
}
```

#### Todo新規作成
- **URL**: `/todos`
- **Method**: POST
- **Headers**:
  - Accept: application/json
  - Content-Type: application/json
  - Authorization: Bearer {token}
- **Request Body**:
```json
{
  "title": "新しいタスク"
}
```
- **Response**: 201 Created
```json
{
  "data": {
    "id": 3,
    "title": "新しいタスク",
    "completed": false,
    "completed_at": null,
    "user_id": 1,
    "created_at": "2025-04-13T14:00:00.000000Z",
    "updated_at": "2025-04-13T14:00:00.000000Z"
  }
}
```

#### Todo更新
- **URL**: `/todos/{id}`
- **Method**: PUT
- **Headers**:
  - Accept: application/json
  - Content-Type: application/json
  - Authorization: Bearer {token}
- **Request Body**:
```json
{
  "title": "更新後のタイトル"
}
```
- **Response**: 200 OK
```json
{
  "data": {
    "id": 1,
    "title": "更新後のタイトル",
    "completed": false,
    "completed_at": null,
    "user_id": 1,
    "created_at": "2025-04-13T12:00:00.000000Z",
    "updated_at": "2025-04-13T14:30:00.000000Z"
  }
}
```

#### Todo削除
- **URL**: `/todos/{id}`
- **Method**: DELETE
- **Headers**:
  - Accept: application/json
  - Authorization: Bearer {token}
- **Response**: 204 No Content

#### Todo完了状態トグル
- **URL**: `/todos/{id}/toggle`
- **Method**: PUT
- **Headers**:
  - Accept: application/json
  - Authorization: Bearer {token}
- **Response**: 200 OK
```json
{
  "data": {
    "id": 1,
    "title": "買い物に行く",
    "completed": true, // falseから変更
    "completed_at": "2025-04-13T15:00:00.000000Z", // nullから変更
    "user_id": 1,
    "created_at": "2025-04-13T12:00:00.000000Z",
    "updated_at": "2025-04-13T15:00:00.000000Z"
  }
}
```

## エラーレスポンス

### 401 Unauthorized
```json
{
  "message": "あなたのトークンは無効です"
}
```

### 403 Forbidden
```json
{
  "message": "このTodoへのアクセス権がないです"
}
```

### 404 Not Found
```json
{
  "message": "指定されたTodoは見つかりませんでした"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The title field is required.",
  "errors": {
    "title": [
      "The title field is required."
    ]
  }
}
```

## 実装例（React）

```jsx
// ログイン
const login = async (email, password) => {
  try {
    const response = await fetch('http://localhost.nyudogum.work/api/login', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });

    const data = await response.json();
    localStorage.setItem('token', data.token);
    return data;
  } catch (error) {
    console.error('ログイン失敗:', error);
  }
};

// Todo一覧取得
const fetchTodos = async () => {
  try {
    const response = await fetch('http://localhost.nyudogum.work/api/todos', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });

    const data = await response.json();
    return data.data;
  } catch (error) {
    console.error('Todo取得失敗:', error);
  }
};
```