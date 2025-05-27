# Talent-engine-TA

## Project Overview: TalentConnect

TalentConnect (internally referred to as Talent-engine-TA) is a web application designed to streamline the talent scouting and recruitment process. It facilitates matching skilled talent with specific project or role requirements through a competency-based outsourcing model. The platform supports various user roles, including Administrators, Requesters (e.g., HR professionals), and Talents, each with dedicated functionalities to manage and interact with talent requests.

## 🚀 Current Status (v1.2.0 - May 2025)

### ✅ System Health
- **Test Suite**: 80/80 tests passing (280 assertions)
- **Core Features**: All operational and stable
- **Critical Bugs**: Zero active issues
- **Authentication**: Fully functional with role-based access control
- **DSS System**: Enhanced with weighted competency scoring

### 🔥 Recent Major Fixes
- **Direct Talent Requests**: Fixed critical controller bug affecting request routing
- **Test Infrastructure**: Achieved 100% test pass rate with comprehensive coverage
- **DSS Algorithm**: Enhanced Simple Additive Weighting (SAW) implementation
- **User Experience**: Improved validation and error handling throughout the system

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
*   **Enhanced Decision Support System (DSS):**
    *   **SAW Algorithm**: Mathematically compliant Simple Additive Weighting implementation
    *   **Weighted Scoring**: Users can assign importance weights (0-100%) to each competency
    *   **Location Intelligence**: Multi-factor geographic compatibility scoring
    *   **Confidence Metrics**: Reliability indicators for talent recommendations
    *   **Veto Thresholds**: Automatic elimination of under-qualified candidates
    *   **Transparent Ranking**: Detailed score breakdowns and methodology explanations
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

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL/PostgreSQL database

### Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd prototype-fix
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Run the application**
   ```bash
   php artisan serve
   ```

### Testing

Run the comprehensive test suite:
```bash
# All tests
php artisan test

# Specific test groups
php artisan test --filter="AuthenticationTest"
php artisan test --filter="DashboardTest" 
php artisan test --filter="UserTalentRequestTest"
```

### Documentation

- **📊 [Enhanced DSS Documentation](docs/Enhanced_DSS_Documentation.md)**: Comprehensive guide to the Decision Support System
- **🏗️ [UML Diagrams](uml-diagrams/)**: Complete system architecture visualization with PlantUML diagrams
- **📝 [Integration Notes](integration_notes.md)**: Development history and feature implementation details
- **🔄 [Changelog](CHANGELOG.md)**: Detailed version history and improvements
- **🚀 [Deployment Guide](DEPLOYMENT_GUIDE.md)**: Production deployment instructions

### Architecture & Diagrams

The system includes comprehensive UML documentation in the `uml-diagrams/` folder:

- **🏗️ Class Diagram**: Models, services, controllers, and relationships
- **🔄 Sequence Diagram**: Workflow interactions and data flow
- **👤 Use Case Diagram**: Actor interactions and system functionality  
- **⚙️ Activity Diagram**: Enhanced DSS algorithm workflow
- **📊 State Diagram**: Talent request lifecycle management

View diagrams online: Open `uml-diagrams/index.html` in your browser or use the [PlantUML Online Editor](http://www.plantuml.com/plantuml/uml/)

## Deployment

Refer to the <mcfile name="DEPLOYMENT_GUIDE.md" path="d:\Data kuiah\TUGAS AKHIR\1PEMBUATAN WEBSITE (DISINI)\prototype-fix\DEPLOYMENT_GUIDE.md"></mcfile> for information on deploying this application. The guide mentions potential targets like Vercel/GitHub Pages for static assets, Heroku for the PHP backend, and ElephantSQL for the database.
