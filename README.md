# Kindergarten Facial Recognition

A Laravel web app for kindergartens. Teachers upload class photos, and the app uses face recognition to work out which child is in each photo and files it under that child. Parents then see only their own child's photos.

## Features

**Teachers**
- Upload one or more photos. If no child is selected, the face is matched automatically; otherwise the photo goes to the chosen child.
- Upload training photos for each child and retrain the face model.
- View their classroom and children, edit their profile, and chat with parents.

**Parents**
- View and download photos of their own child, and mark favourites.
- Chat with teachers, send feedback, and read the school guidelines and consent form.

**Admins**
- Manage users, classrooms and parent-child links.
- Edit guidelines and read feedback.

## Tech stack

| Part | Technology |
|---|---|
| Backend | PHP 8.2+ (Laravel 12), Laravel Breeze/UI authentication |
| Database | MySQL (also used for sessions, cache and queue) |
| Frontend | Blade templates, Bootstrap 5, Tailwind, Vite |
| Face recognition | Python 3 with `face_recognition` (dlib), run by `train.py` and `recognize.py` |

Laravel calls the Python scripts through `shell_exec`. The Python interpreter is set by `PYTHON_BIN` in `.env`.

## Requirements

- PHP 8.2 or newer with `pdo_mysql`, `mbstring`, `gd`, `openssl`, `tokenizer`, `curl` and `fileinfo`
- Composer
- Node.js and npm
- MySQL 8 or newer
- Python 3.11 or 3.12 (3.13+ may fail to build `dlib`) and CMake

## Setup on macOS

```bash
brew install php composer node mysql cmake python@3.12
brew services start mysql
```

1. **Install PHP and JS packages**
   ```bash
   composer install
   npm install
   ```

2. **Create the Python environment**
   ```bash
   python3.12 -m venv .venv
   .venv/bin/pip install -r requirements.txt
   ```
   Building `dlib` takes a few minutes.

3. **Configure the environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Then edit `.env`:
   ```
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_app
   DB_USERNAME=admin_user
   DB_PASSWORD=choose-a-password
   PYTHON_BIN=.venv/bin/python
   ```

4. **Create the database**
   ```bash
   mysql -uroot -e "CREATE DATABASE laravel_app;
     CREATE USER 'admin_user'@'127.0.0.1' IDENTIFIED BY 'choose-a-password';
     GRANT ALL ON laravel_app.* TO 'admin_user'@'127.0.0.1';"
   ```
   Use the same password as in `.env`.

5. **Create tables and demo data, link storage, start the server**
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
   Open http://127.0.0.1:8000.

### Demo logins

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | adminpasscode |
| Teacher | teacher@example.com | teacherpass |
| Parent | parent@example.com | parentpass |

Change these before using the app for anything real.

## Setup on Windows

The steps are the same, with these differences:
- Create the venv with `py -3.12 -m venv .venv` and install with `.venv\Scripts\pip install -r requirements.txt`.
- Set `PYTHON_BIN=.venv\Scripts\python.exe` in `.env`.
- Install CMake and the Visual Studio C++ Build Tools first, because `dlib` needs them.

## Face recognition

- Training photos live in `storage/app/training_faces/<child_id>/`, one folder per child.
- `python train.py` reads them and writes the model to `storage/app/model.pkl`. Teachers can also trigger this from the training page.
- `python recognize.py <photo>` prints the matching child ID, or `No match found`.
- To register a new child for recognition:
  1. Add the child to the `children` table (there is no admin form for this yet), for example:
     ```sql
     INSERT INTO children (name, age, gender, parent_id, created_at, updated_at)
     VALUES ('Lewandowski', 4, 'Male', 7, NOW(), NOW());
     ```
     `parent_id` must be the ID of an existing parent user.
  2. Create `storage/app/training_faces/<child_id>/`. The folder name must match the child's ID exactly.
  3. Put several clear photos of the child's face in it, then run `python train.py`.
- Retrain whenever you add or change training photos. The face data belongs to children, so keep the repository private.

## Known issues

- **`npm run build` fails.** `resources/css/app.css` uses Tailwind v4 syntax, but `package.json` installs Tailwind v3. The pre-built files in `public/build` are used instead, so the app works, but frontend styles cannot be rebuilt until this is resolved.
- **PHP 8.5 prints deprecation notices** (`PDO::MYSQL_ATTR_SSL_CA`) from the framework's database config. They are harmless. Hide them with `error_reporting = E_ALL & ~E_DEPRECATED` in `php.ini`.

## Tests

```bash
php artisan test
```

## Project layout

| Path | Contents |
|---|---|
| `app/Http/Controllers` | Admin, teacher, parent, photo, chat, feedback and consent controllers |
| `app/Models` | Eloquent models (users, children, classrooms, photos, chats, and so on) |
| `database/migrations`, `database/seeders` | Schema and demo data |
| `resources/views` | Blade templates |
| `routes/web.php` | Routes, grouped by role |
| `train.py`, `recognize.py` | Face recognition scripts |
| `storage/app` | Uploaded photos, training faces and the trained model |
