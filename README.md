# KDM-Stratus

KDM-Stratus is a hybrid-cloud infrastructure I built to modernize and streamline operations across 28 Kadiliman Esports Cafe branches. It handles real-time terminal telemetry, automated cashless payments, and centralizes multi-branch management into a single system.

## 🚀 Features & Highlights
* **Hybrid Infrastructure:** Utilizes a physical LAN setup backed by Windows Server (IIS) combined with a hub-and-spoke WAN across the public internet to connect all 28 branches.
* **Custom Python Client:** Lightweight Python-based client application installed on local gaming terminals to handle session locking, timers, and real-time telemetry.
* **Automated GCP Backups:** Scheduled, automated MySQL database backups securely pushed to Google Cloud Storage.
* **Cashless Payments:** Integrated PayMongo API to allow seamless e-wallet top-ups directly from the terminals.

## 👥 System Roles

* **HQ / Superadmin**
  * Full multi-tiered dashboard for global oversight.
  * View revenue, active sessions, and telemetry across all 28 branches in real-time.
  * Global network and system-wide configurations.
* **Branch Manager**
  * Branch-level oversight and reporting.
  * Monitor local terminal status (active, idle, locked) from the counter.
  * Manage local cash transactions and shift logs.
* **Customer (Terminal Client)**
  * Real-time session timer and lock screen UI.
  * Self-service e-wallet top-ups via PayMongo.

## 🛠️ How to Run

### Prerequisites
* Windows Server with IIS configured
* PHP 8.x & Composer (Laravel framework)
* MySQL
* Python 3.8+ (for the terminal client)

### Step-by-Step Setup

1. **Clone the repository**
   git clone [https://github.com/yourusername/kdm-stratus.git](https://github.com/yourusername/kdm-stratus.git)
   cd kdm-stratus

Configure the Server (Laravel)

2. **Configure the Server (Laravel)**

  cd server
  composer install
  copy .env.example .env
  php artisan key:generate
  
Update your .env file with your MySQL credentials, GCP bucket details, and PayMongo API keys.

3. **Set up the Database**
php artisan migrate --seed

Note: Point your IIS site document root to the public directory of the Laravel app.

4. **Run the Terminal Client (Python)**
  cd ../client
  pip install -r requirements.txt
  python main.py

Make sure to configure the client's .env or config file to point to your central server's IP/Domain.
