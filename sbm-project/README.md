# 🇮🇳 Swachh Bharat Mission (Clean India) - Urban Sanitation Portal

A modern, full-stack web application designed for municipal transparency, citizen empowerment, and sanitation worker accountability under the **Swachh Bharat Mission (SBM)** guidelines.

Built using **HTML5, CSS3, JavaScript (Leaflet GIS & Chart.js), PHP 8, and MySQL**.

---

## 🌟 Core Features

### 1. Monitor Area Cleanliness Status & Cleanliness Score (0–100)
- **Dynamic Score Formula**:
  - **40%** Field inspection rating by municipal health inspectors
  - **20%** Ratio of operational, segregated (wet/dry) smart dustbins
  - **Deductions (-5 to -12 pts)** for active unresolved citizen complaints based on severity
  - **Bonus (+5 pts each)** for verified completed cleanings
- **Three-Tier Classification**:
  - 🟢 **Clean (80–100)**: Swachh Adarsh Benchmark
  - 🟡 **Moderate (60–79)**: Action Needed
  - 🔴 **Not Clean (<60)**: Critical Intervention Required

### 2. User Feedback System for Reporting Issues
- Citizen grievance submission with:
  - Categories: *Garbage Dump, Overflowing Dustbin, Plastic Litter, Sewage Leak, Dead Animal, Open Defecation*
  - Photo upload with live preview
  - Interactive Leaflet GPS map coordinate picker
  - Address / landmark detection
  - Instant reward incentive: **+50 Swachh Points** on submission

### 3. Before & After Image Verification System
- **Worker Workflow**: Sanitation staff upload authentic photos before starting and after finishing the cleanup.
- **Admin Audit**: Municipal supervisors inspect the split-image **Interactive Comparator Slider** (drag handle left/right to compare paver cleanliness).
- **Approval & Rewards**: When approved, the grievance is closed, the area cleanliness score increases, and the citizen receives a **+75 Points Bonus**.

### 4. Cleanliness Points & Citizen Leaderboard
- Top 3 citizen guardians displayed on a **Gold, Silver, and Bronze podium**.
- Ranking table displaying total verified reports and points.
- **Government Badge Tiers**:
  - `Swachhta Mitra` (0–99 pts)
  - `Cleanliness Champion` (100–299 pts)
  - `Green Warrior` (300–599 pts)
  - `National Swachh Icon` (600+ pts)

---

## 🏛️ Portal Structure

### Public Website (Citizen-Facing)
- `index.php`: Home page featuring mission highlights, live statistics strip, interactive Before/After comparison demo, and live activity feed.
- `areas.php`: Area Cleanliness Status board with real-time score badges, search, and status filters.
- `map.php`: Interactive Leaflet GIS Map with color-coded ward markers (green/yellow/red) and direct reporting pins.
- `report.php`: Grievance submission form with GPS picker and camera upload.
- `track.php`: 5-step visual tracking timeline (*Reported ➔ Assigned ➔ Cleaning ➔ Photo Audit ➔ Verified Clean*) with embedded Before/After comparison.
- `leaderboard.php`: Citizen podium, rankings, and points rewards breakdown.
- `auth.php`: Login & Citizen registration with 1-click persona switchers.

### 🛡️ Admin Panel (`/admin/`)
*Designed following the maritime deep teal and card-based dashboard aesthetic.*
- `admin/index.php`: Real-time KPI gauge meters, current hotspot and next inspection mini-maps, operational activity calendar, fleet status, and photo audit queue.
- `admin/areas.php`: Municipal ward management, inspection rating adjustments, and live score recalculation algorithm.
- `admin/complaints.php`: Grievance dispatch to workers, status filters, and **Before & After Photo Verification Modal**.
- `admin/workers.php`: Field cleaner roster, active workloads, and worker enrollment.
- `admin/users.php`: Citizen directory, points audit, and bonus points rewards.
- `admin/analytics.php`: Chart.js visualizations (donut status breakdown, score comparisons, waste category polar area), and exportable reports.

### 👷 Worker Panel (`/worker/`)
*Mobile-first interface optimized for field staff on tablets or phones.*
- `worker/tasks.php`: Assigned tasks with urgency badges, citizen photos, and directions.
- `worker/upload.php`: Before and After photo upload with live previews and field remarks.
- `worker/history.php`: History of completed cleanings with supervisor audit notes.

---

## 🚀 Getting Started

### Option A: Running with XAMPP (Apache & MySQL)
1. Copy or move this folder into your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\sbm-project
   ```
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import the provided [`database.sql`](database.sql) file (creates database `sbm_db` with all tables and sample records).
5. Open your browser and navigate to:
   ```
   http://localhost/sbm-project/
   ```

### Option B: Instant Built-in PHP Server
Run directly from this project folder:
```powershell
php -S localhost:8000
```
Then visit `http://localhost:8000` in your web browser.

---

## 🔑 Demo Login Accounts

Every page includes a **Quick 1-Click Role Switcher** at the very top banner for easy testing without typing credentials:

| Role | Email | Password | Pre-loaded Data |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@sbm.gov.in` | `admin123` | Municipal Commissioner (Dr. Rajesh Verma) |
| **Worker** | `worker@sbm.gov.in` | `worker123` | Ramesh Kumar (Lead Sanitation Worker) |
| **Citizen** | `citizen@sbm.gov.in` | `citizen123` | Pooja Sharma (Green Warrior - 420 Pts) |
| **Citizen (Icon)** | `arun@sbm.gov.in` | `citizen123` | Arun Patel (National Swachh Icon - 680 Pts) |

---

## 📁 Project Directory Layout

```
├── config/
│   └── db.php                  # PDO database connection (MySQL with fallback)
├── includes/
│   ├── header.php              # Topbar (clock, search, logo) and public navigation
│   ├── footer.php              # Public footer & script bundles
│   └── functions.php           # Cleanliness score algorithm & points engine
├── admin/
│   ├── index.php               # Admin Dashboard (gauges, mini-maps, activity feed)
│   ├── areas.php               # Area & score management
│   ├── complaints.php          # Complaint dispatch & Before/After photo audit
│   ├── workers.php             # Worker workforce roster
│   ├── users.php               # Citizen directory & points awards
│   ├── analytics.php           # Chart.js analytics & printable reports
│   └── verify_process.php      # Before/After verification approval handler
├── worker/
│   ├── tasks.php               # Field task queue
│   ├── upload.php              # Before & After photo upload form
│   ├── upload_process.php      # Upload submission handler
│   └── history.php             # Verified completion archive
├── assets/
│   ├── css/                    # Custom CSS styling (style.css, dashboard.css, public.css)
│   ├── js/                     # Comparator slider, Leaflet map, Chart.js graphs, clock
│   ├── images/                 # SBM logo & user avatars
│   └── uploads/                # Sample Before & After cleaning photos
├── database.sql                # Complete MySQL schema & initial data
└── README.md                   # Documentation & setup instructions
```
