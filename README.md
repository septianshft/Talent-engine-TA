# Talent-engine-TA

## Project Overview: TalentConnect

TalentConnect (internally referred to as Talent-engine-TA) is a web application designed to streamline the talent scouting and recruitment process. It facilitates matching skilled talent with specific project or role requirements through a competency-based outsourcing model. The platform supports various user roles, including Administrators, Requesters (e.g., HR professionals), and Talents, each with dedicated functionalities to manage and interact with talent requests.

## Key Features

*   **Role-Based Access Control:** Distinct interfaces and permissions for Administrators, Requesters, and Talents.
*   **Talent Request Workflow:**
    *   Requesters can submit detailed talent requests specifying project needs.
    *   Administrators review, manage, and can assign talent to requests.
    *   Talents can view and respond (approve/reject) to requests assigned to them.
    *   Comprehensive status tracking for requests (e.g., `pending_user`, `pending_admin`, `pending_talent`, `approved`, `rejected`, `completed`).
*   **Competency Management:**
    *   Define and manage a catalog of professional competencies.
    *   Talents can list their competencies and proficiency levels.
    *   Talent requests specify required competencies, proficiency levels, and relative weights for each competency.
*   **Decision Support System (DSS):**
    *   An intelligent system to rank available talents based on their match with the competencies and proficiency levels specified in a talent request.
    *   Considers the weight of each competency to provide a prioritized list of candidates.
*   **Admin Dashboard:** Centralized interface for administrators to oversee all talent requests, manage users, and monitor system activity.
*   **User Dashboard:** Personalized dashboards for requesters and talents to track their respective activities.

## Technology Stack

*   **Backend:**
    *   **Framework:** Laravel (PHP)
    *   **Architecture:** MVC (Model-View-Controller)
    *   **Database:** Utilizes Eloquent ORM (Specific RDBMS like MySQL/PostgreSQL is common, with ElephantSQL mentioned for potential cloud deployment).
    *   **Authentication:** Built-in Laravel authentication.
*   **Frontend:**
    *   **Templating:** Blade Engine
    *   **Styling:** Tailwind CSS (with dark mode support)
    *   **Interactivity:** Likely uses Livewire for dynamic components, and standard JavaScript (compiled with Vite).
    *   **UI Components:** Potentially uses a library named Flux (as seen in `split.blade.php`).
*   **Testing:**
    *   **Framework:** PHPUnit
    *   **Types:** Feature tests and Unit tests covering various application modules.
*   **Development & DevOps:**
    *   **Version Control:** Git
    *   **Containerization:** Docker (Dockerfile and Nginx configuration provided).
    *   **CI/CD:** GitHub Actions for linting and running tests.
    *   **Environment Management:** `.env` for configuration.

## Project Structure Highlights

*   `app/`: Contains core application logic (Models, Controllers, Services like `DecisionSupportService`, Livewire components).
*   `config/`: Application configuration files.
*   `database/`: Migrations, factories, and seeders.
*   `public/`: Web server's document root, entry point (`index.php`), and static assets (images, CSS, JS).
*   `resources/`: Frontend assets (Blade views, raw CSS/JS, language files).
*   `routes/`: Route definitions (`web.php`, `auth.php`).
*   `tests/`: Automated tests (Feature, Unit).
*   `aset-foto/`: Contains additional image assets.
*   `public/images/`: Primary directory for publicly accessible images, including logos.

## Getting Started

(Standard Laravel setup procedures would apply here. Typically involves cloning the repository, installing Composer dependencies, setting up the `.env` file, running database migrations, and serving the application.)

## Deployment

Refer to the <mcfile name="DEPLOYMENT_GUIDE.md" path="d:\Data kuiah\TUGAS AKHIR\1PEMBUATAN WEBSITE (DISINI)\prototype-fix\DEPLOYMENT_GUIDE.md"></mcfile> for information on deploying this application. The guide mentions potential targets like Vercel/GitHub Pages for static assets, Heroku for the PHP backend, and ElephantSQL for the database.
