# Deployment Guide: Laravel on Render (with Docker & PostgreSQL)

This guide outlines the steps to deploy your Laravel application to Render using Docker for the application and Render's managed PostgreSQL for the database.

## Architecture on Render
```
[User Browser] <-> [Render Load Balancer] <-> [Render Web Service (Docker)] <-> [Render PostgreSQL]
                                                                 |
                                                                 |-> (Static assets served by Nginx in Docker)
                                                                 |-> (Optional: Render CDN for assets)
```

## 1. Prerequisites
*   A [Render](https://render.com/) account.
*   Your project pushed to a GitHub (or GitLab/Bitbucket) repository.
*   The `Dockerfile` (as configured in previous steps) committed to your repository root.
*   [Git](https://git-scm.com/downloads) installed locally.
*   [PHP](https://www.php.net/downloads) and [Composer](https://getcomposer.org/download/) installed locally (for generating `APP_KEY`).

## 2. Create PostgreSQL Database on Render
1.  Go to your Render Dashboard.
2.  Click **"New +"** and select **"PostgreSQL"**.
3.  Choose a **Name** for your database (e.g., `my-laravel-db`).
4.  Select a **Region** (choose one close to you or your users).
5.  Select a **PostgreSQL Version** (e.g., latest available).
6.  Click **"Create Database"**.
7.  Once created, go to the database's page. Under **"Connections"**, find and **copy the "Internal Connection String"**. You'll need its parts (host, port, database name, user, password) for your Web Service environment variables.
    *   Example Internal Connection String: `postgresql://user:password@host:port/database_name`

## 3. Create Web Service on Render (Laravel Application)
1.  Go to your Render Dashboard.
2.  Click **"New +"** and select **"Web Service"**.
3.  **Build & Deploy:**
    *   Choose **"Build and deploy from a Git repository"**.
    *   Connect your GitHub (or other Git provider) account and select your project repository.
4.  **Settings:**
    *   **Name:** Give your service a name (e.g., `my-laravel-app`). This will be part of your default `.onrender.com` URL.
    *   **Region:** Choose the same region as your database for optimal performance.
    *   **Branch:** Select the branch you want to deploy (e.g., `main` or `latest-commit-05-21`).
    *   **Root Directory:** Leave blank if your `Dockerfile` is in the root of the repository.
    *   **Environment:** Select **`Docker`**.
        *   Render should automatically detect your `Dockerfile`. If not, you can specify the path (e.g., `./Dockerfile`).
    *   **Instance Type:** Choose a plan (e.g., "Free" or a paid plan for more resources).
5.  **Environment Variables:**
    *   Click **"Advanced"** or look for the "Environment" section.
    *   Add the following environment variables. **It's crucial to get these right.**
        *   `APP_KEY`: Generate locally (`php artisan key:generate --show`) and paste the `base64:...` key.
        *   `APP_ENV`: `production`
        *   `APP_DEBUG`: `false`
        *   `APP_URL`: `https://your-app-name.onrender.com` (Replace `your-app-name` with the name you gave your Render Web Service).
        *   `LOG_CHANNEL`: `stderr`

        *   `DB_CONNECTION`: `pgsql`
        *   `DB_HOST`: (From your Render PostgreSQL "Internal Connection String")
        *   `DB_PORT`: (From your Render PostgreSQL "Internal Connection String")
        *   `DB_DATABASE`: (From your Render PostgreSQL "Internal Connection String" - database name)
        *   `DB_USERNAME`: (From your Render PostgreSQL "Internal Connection String" - user)
        *   `DB_PASSWORD`: (From your Render PostgreSQL "Internal ConnectionString" - password)

        *   `SESSION_DRIVER`: `database` (Recommended for production if you have multiple instances or use cron jobs that need session data. Ensure sessions table exists via migration).
        *   `CACHE_DRIVER`: `database` (Or `redis` if you set up a Redis instance on Render).
        *   `QUEUE_CONNECTION`: `database` (Or `redis` if you set up a Redis instance and use queues).

        *   *(Add any other custom environment variables your application needs, e.g., API keys for external services)*
6.  **Build & Start Commands:**
    *   **Build Command:** Render typically infers this from the Docker build process. You can usually leave this blank.
    *   **Start Command:** Render infers this from the `CMD` in your `Dockerfile` (`/start.sh`). You can usually leave this blank.
7.  **Health Check Path:**
    *   Set to `/` or a dedicated health check route if you have one (e.g., `/api/health`). Render uses this to determine if your application has started successfully.
8.  **Auto-Deploy:**
    *   Enable this if you want Render to automatically redeploy your application whenever you push changes to the configured branch.
9.  Click **"Create Web Service"**. Render will start building your Docker image and deploying your application. This might take a few minutes.

## 4. Database Migrations & Seeding
Once your Web Service has successfully deployed for the first time:
1.  Go to your Web Service page on Render.
2.  Open the **"Shell"** tab.
3.  Run the following commands:
    ```bash
    php artisan migrate --force
    ```
    ```bash
    php artisan db:seed --class=RoleSeeder
    # Add other seeders as needed, e.g.:
    # php artisan db:seed --class=CompetencySeeder
    # php artisan db:seed --class=UserSeeder
    ```

## 5. Scheduled Tasks (Cron Jobs on Render)
If you have scheduled tasks (like the session cleanup in your original guide):
1.  Go to your Render Dashboard.
2.  Click **"New +"** and select **"Cron Job"**.
3.  **Settings:**
    *   **Name:** e.g., `laravel-scheduler`
    *   **Connect Repository:** Select the same repository as your Web Service.
    *   **Region:** Same as your Web Service and Database.
    *   **Branch:** Same as your Web Service.
    *   **Schedule (CRON expression):**
        *   For daily at midnight UTC: `0 0 * * *`
        *   For your session cleanup: `0 0 * * *` (or as per your `app/Console/Kernel.php` if you want it to run all scheduled tasks)
    *   **Command:**
        ```bash
        php /var/www/html/artisan schedule:run
        ```
        (This assumes your `Dockerfile` places the app in `/var/www/html`)
    *   **Instance Type:** Choose a plan.
4.  **Environment Variables:**
    *   **IMPORTANT:** Copy the **exact same environment variables** from your Web Service (especially `APP_KEY` and all `DB_*` variables) to your Cron Job service so it can connect to the database and run correctly.
5.  Click **"Create Cron Job"**.

## 6. Custom Domains (Optional)
1.  Go to your Web Service page on Render.
2.  Navigate to the **"Settings"** tab.
3.  Scroll down to **"Custom Domains"**.
4.  Click **"Add Custom Domain"** and follow the instructions to point your domain's DNS records to Render.

## Troubleshooting & Tips
*   **Logs:** Check the "Logs" tab for your Web Service or Cron Job on Render for any build or runtime errors.
*   **Environment Variables:** Double-check that all environment variables are correctly set, especially `APP_KEY` and database credentials. A common issue is a missing or incorrect `APP_KEY`.
*   **`Dockerfile` Base Image:** The `richarvey/nginx-php-fpm` image is convenient. Ensure the tag you use (`latest` or a specific version like `3.1.6`) provides a PHP version compatible with your Laravel version (PHP >= 8.1 for Laravel 10, PHP >= 8.0 for Laravel 9).
*   **Storage Permissions:** If you encounter issues with file uploads or writing to `storage/logs`, you might need to adjust permissions in your `Dockerfile` (see the commented-out section in the provided `Dockerfile`).
*   **Vite Assets:** Ensure `npm run build` in your `Dockerfile` is successfully creating assets in the `public/build` directory and that your `manifest.json` is correctly referenced.
*   **Clear Config Cache Locally:** Before pushing, if you made changes to config files, run `php artisan config:clear` locally. The Docker build process runs `config:cache`, so this is more for local consistency.

This guide should provide a solid foundation for deploying your Laravel application to Render. Good luck!
