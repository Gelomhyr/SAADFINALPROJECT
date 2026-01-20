P&J Tenarte Dental Clinic Website (PHP + Tailwind)

How to run locally (Windows / Mac / Linux):
1) Install PHP 8+.
2) Open a terminal in this folder: PROJECT SAAD
3) Run:
   php -S localhost:8000
4) Open your browser:
   http://localhost:8000/index.php

Pages:
- index.php (Home)
- about.php
- services.php
- appointment.php (stores requests to /data/appointment_requests.json)
- contact.php (stores messages to /data/contact_messages.json)

Notes:
- This is a simple working demo website. It stores appointment requests and contact messages as JSON files.
- To deploy online, upload the whole folder to a PHP-enabled hosting (cPanel, Hostinger, etc.).

---
STAFF PORTAL (PHP SESSION LOGIN)

Open: /staff/index.php

Demo Credentials:
- Receptionist: receptionist / reception123
- Owner: owner / owner123

Receptionist can confirm/reschedule/cancel requests. Actions generate:
- notifications.json (in-app)
- message_logs.json (simulated SMS/Email)
- reminder_logs.json (scheduled reminders for confirmed appointments)

Owner dashboard is read-only and can view requests and logs.

========================
STAFF PORTAL (NEW)
========================
Open: /staff/index.php

Demo credentials:
- Receptionist
  Username: receptionist
  Password: receptionist123

- Owner
  Username: owner
  Password: owner123

Receptionist can confirm/reschedule/cancel requests.
Owner can view requests and logs (simulated SMS/Email + reminders).
