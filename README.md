
# Library Management System API

This project provides a RESTful API for managing a digital library system. The system allows users to manage books, categories, roles, users, comments, and downloads.

## Features

- **User Management**: Create, update, delete, and view user profiles.
- **Role Management**: Assign roles to users and manage access control.
- **Book Management**: Add, update, and delete books. Manage book details such as author, cover image, and categories.
- **Category Management**: Categorize books for easier filtering and searching.
- **Comments & Ratings**: Allow users to comment and rate books.
- **Download Tracking**: Track when and who downloads the books.

## Requirements

- PHP 8.x
- Laravel 10.x
- MySQL
- Composer

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/library-management-api.git
   ```

2. Navigate to the project folder:

   ```bash
   cd library-management-api
   ```

3. Install dependencies:

   ```bash
   composer install
   ```

4. Copy `.env.example` to `.env` and configure your database:

   ```bash
   cp .env.example .env
   ```

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Run the database migrations:

   ```bash
   php artisan migrate
   ```

## API Endpoints

### Users

- `GET /api/users`: Retrieve a list of all users.
- `GET /api/users/{user}`: Get details of a specific user.
- `POST /api/users`: Create a new user.
- `PUT /api/users/{user}`: Update an existing user.
- `DELETE /api/users/{user}`: Delete a user.

### Roles

- `GET /api/roles`: Retrieve a list of all roles.
- `GET /api/roles/{role}`: Get details of a specific role.
- `POST /api/roles`: Create a new role.
- `PUT /api/roles/{role}`: Update an existing role.
- `DELETE /api/roles/{role}`: Delete a role.

### Books

- `GET /api/books`: Retrieve a list of all books.
- `GET /api/books/{book}`: Get details of a specific book.
- `POST /api/books`: Create a new book.
- `PUT /api/books/{book}`: Update an existing book.
- `DELETE /api/books/{book}`: Delete a book.

### Categories

- `GET /api/categories`: Retrieve a list of all categories.
- `GET /api/categories/{category}`: Get details of a specific category.
- `POST /api/categories`: Create a new category.
- `PUT /api/categories/{category}`: Update an existing category.
- `DELETE /api/categories/{category}`: Delete a category.

### Comments

- `GET /api/books/{book}/comments`: Retrieve all comments for a specific book.
- `POST /api/books/{book}/comments`: Add a new comment to a book.
- `DELETE /api/comments/{comment}`: Delete a comment.

### Downloads

- `GET /api/users/{user}/downloads`: Retrieve all downloads made by a specific user.
- `POST /api/books/{book}/downloads`: Record a book download.

## Database Structure

The project uses several database tables:

- **users**: Stores user information.
- **roles**: Manages user roles.
- **permissions**: Stores permissions linked to roles.
- **books**: Holds book data including title, author, and file.
- **categories**: Categorizes books.
- **comments**: Manages book comments and ratings.
- **downloads**: Tracks user downloads.

## Book Management Controller

### Overview

The `BookController` class is responsible for managing the books in the application. It provides functionality for CRUD operations, file uploads, and file handling. The controller utilizes three main traits: `HandleFile`, `CrudOperationsTrait`, and `HasPermissions`, which modularize different aspects of its operations.

### Traits Overview

1. **HandleFile Trait**

   This trait handles file-related operations, including uploading, updating, and deleting files from the server. It also processes PDF files to extract the size and number of pages.

   - **Functions**:
     - `UploadFiles($file, $name = null, $fileType)`
     - `uploadFile($uploadedFile, $name, $folder, $disk)`
     - `createFile(Request $request, $input, $fileName, $type)`
     - `updateFile(Request $request, $input, $currentPath, $fileName, $type)`

   - **Usage Example**: 
     To upload a cover image or a PDF file when storing a new book, the controller calls `UploadFiles()` with the file and type parameters.

2. **CrudOperationsTrait**

   This trait handles common CRUD operations for any model. It provides methods to validate request data, retrieve records (with or without relations), and create, update, or delete records.

   - **Functions**:
     - `validateRequestData($request, $rules)`
     - `getAllWithRelation(Model $model, array $relation, array $columns = ['*'])`
     - `getAllRecords(Model $model, array $columns = ['*'])`
     - `findWithRelation(Model $model, array $relation, int $id)`
     - `findById(Model $model, int $id)`
     - `createRecord(Model $model, array $data)`
     - `updateRecord(Model $model, int $id, array $data)`
     - `deleteRecord(Model $model, int $id, array $fileFields = [])`

   - **Usage Example**: 
     In the `store()` method, the `createRecord()` function is called to insert a new book into the database. The `deleteRecord()` function is used in the `destroy()` method to delete a book.

3. **HasPermissions Trait**

   This trait handles role-based permissions for different CRUD operations. It automatically applies middleware for permissions based on the controller’s actions (e.g., index, show, create, update, destroy).

   - **Functions**:
     - `authorizePermissions(string $permissionClass, callable $permissionsCallback = null)`
     - `getDefaultPermissions(string $permissionClass)`

   - **Usage Example**: 
     In the constructor of the controller, `authorizePermissions('book')` can be called to apply permission checks for actions like viewing, creating, editing, and deleting books.

### Endpoints and Methods

1. **`GET /books` - index()**

   Fetches a paginated list of books with optional search and filtering.

   - **Request Parameters**:
     - `search`: A search string to filter books by title or author.
     - `category_id`: Filter books by a specific category.

   - **Response**:
     - JSON response with the list of books.

2. **`POST /books` - store()**

   Stores a new book record in the database along with the uploaded file and cover image.

   - **Request Body**:
     - Fields for book details (`title`, `author`, `file`, etc.).
     - Files (`cover_image`, `file`).

   - **Response**:
     - JSON response with the newly created book or error.

3. **`PUT /books/{id}` - update()**

   Updates an existing book record and handles any new file uploads.

   - **Request Body**:
     - Fields for book details.
     - Optional files for updating the `cover_image` and `file`.

   - **Response**:
     - JSON response with the updated book or error.

4. **`DELETE /books/{id}` - destroy()**

   Deletes a book record along with the associated files (e.g., cover image, PDF file).

   - **Response**:
     - JSON response with success or error.

### File Handling

#### PDF Parsing

When uploading a PDF file, the `handleFile()` method calculates the file size in MB and extracts the number of pages using the `Smalot\PdfParser\Parser` package. This information is stored alongside the book's details.

- **Example of File Handling**:

   ```php
   $fileData = $this->handleFile($request->file('file'), $request->title);
   $input['file'] = $fileData['uploaded_file'];
   $input['size'] = $fileData['size'];
   $input['number_pages'] = $fileData['number_pages'];
   ```

## Permissions

The `HasPermissions` trait allows the controller to enforce role-based access control. By default, the following permissions are required for each action:

- `index`: Create, Edit, Delete permissions for the `book` entity.
- `create`: Create permission for `book`.
- `update`: Edit permission for `book`.
- `destroy`: Delete permission for `book`.

### Example Permission Definition:

```php
$this->authorizePermissions('book', function($permissions) {
    $permissions['index'] = 'view-book';
    return $permissions;
});
```

## Error Handling

The controller uses `try-catch` blocks to handle exceptions during file uploads, book creation, or updates. Errors are returned in JSON format with appropriate status codes.

- **Example**:

   ```php
   try {
       $book = $this->createRecord(new Book, $input);
       return new BookResource($book);
   } catch (\Exception $e) {
       return response()->json(['error' => 'Unable

 to create book.'], 500);
   }
