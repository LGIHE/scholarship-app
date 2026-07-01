# Admin Panel Guide

Complete reference for committee members and system admins using the Filament admin panel at `/admin`.

---

## Accessing the Panel

**URL**: `http://your-domain.com/admin`

Only users with **System Admin** or **Committee Member** roles can log in here. All other users are redirected to `/portal`.

---

## Dashboard

The dashboard loads automatically on login and shows:

| Widget | Description |
|--------|-------------|
| **Application Stats** | Total applications, pending review, approved count, average score |
| **Scholar Stats** | Active scholars, pending applicants, new scholars (last 30 days) |
| **Applications by Status** | Doughnut chart of status distribution |
| **Recent Applications** | Last 5 submitted applications with key details |
| **Charts** | Applications by district, gender, nationality, university, science/arts |

---

## Navigation Structure

```
📊 Dashboard

📝 APPLICATION MANAGEMENT
   ├── Applications    — process all scholarship applications
   └── Applicants      — manage users who have NOT been approved yet

🎓 SCHOLAR MANAGEMENT
   ├── Scholars        — manage academic records for approved scholars
   └── Scholar Users   — manage user accounts for approved scholars

⚙️ SYSTEM ADMINISTRATION
   ├── System Users    — manage admin and committee accounts
   └── Roles           — manage roles and permissions
```

**Key distinction**: Each user appears in only one place.
- Not yet approved → **Application Management → Applicants**
- Approved → **Scholar Management → Scholar Users**
- Admin staff → **System Administration → System Users**

---

## Application Management

### Applications

View, filter, and process all scholarship applications.

**Columns**: Applicant name, status, score, submission date
**Filters**: Status, date range, score
**Search**: Applicant name

**Available actions on a submitted application**:

| Action | What it does |
|--------|-------------|
| **Under Review** | Marks the application as being reviewed |
| **Approve** | Approves the application; automatically creates a Scholar record, assigns the Scholar role, and sends an approval email |
| **Reject** | Rejects the application and notifies the applicant |

#### Workflow: Review an Application

```
1. Applications → filter by "Submitted"
2. Click "View" to read full details and scoring breakdown
3. Click "Under Review" to flag it
4. After deliberation, click "Approve" or "Reject"
```

### Applicants

Manage user accounts for applicants who have not yet been approved.

- Only shows users with the **Applicant** role (Scholars are excluded)
- Create new applicant accounts manually
- "View Applications" button links directly to their submitted applications

---

## Scholar Management

### Scholars

Manage academic records for approved scholars.

**Editable fields**: University, Course/Program, Student ID, expected graduation date, scholarship start date

**Scholar View page tabs**:

| Tab | Contents |
|-----|----------|
| **Bio** | Personal info, academic info, scholarship details |
| **Applications** | Original application with all submitted data |
| **Academic Progress** | Semester-by-semester CGPA (colour-coded: green ≥3.5, blue ≥3.0, yellow ≥2.5) |
| **Documents** | Uploaded transcripts with download links |
| **Activity** | Timeline of key dates, total progress records, average CGPA |

#### Workflow: Update Scholar Academic Info

```
1. Scholar Management → Scholars
2. Find the scholar → click "Edit"
3. Update university, course, or student ID
4. Click "Save"
```

### Scholar Users

Manage the user accounts (login credentials) for approved scholars.

- "View Scholar Details" links to the academic record
- Edit name, email, or password here

---

## System Administration

### System Users

Create and manage admin-level accounts (System Admin and Committee Member roles only).

#### Workflow: Add a Committee Member

```
1. System Administration → System Users → "Create"
2. Enter name, email, password
3. Select role: Committee Member
4. Click "Create"
5. User receives a welcome email with login credentials
```

### Roles

View and manage system roles. See the **Permissions Guide** for full details on creating and assigning permissions to roles.

---

## Cross-Navigation Quick Links

| From | Button | Goes to |
|------|--------|---------|
| Applicants list | "View Applications" | That applicant's applications |
| Scholar Users list | "View Scholar Details" | Scholar academic record |
| Scholars list | "View User Account" | Scholar's user account |

---

## Search Tips

| Looking for... | Go to... |
|----------------|----------|
| Applicant (not approved) | Application Management → Applicants → search by name/email |
| Approved scholar | Scholar Management → Scholar Users → search by name/email |
| Admin/committee staff | System Administration → System Users |
| Application by status | Application Management → Applications → status filter |
| Application by score | Application Management → Applications → sort by Total Score |

---

## Common Troubleshooting

**Can't find a user?**
Check their role — applicants and scholars are in different sections, and admins are in System Users.

**Approved an application but can't find the scholar?**
Check Scholar Management → Scholars (academic record) and Scholar Management → Scholar Users (account).

**Widget not loading?**
Refresh the page. If it persists, check the database connection and ensure data exists.

**Keyboard shortcuts (Filament defaults)**:
- `Ctrl/Cmd + K` — Global search
- `Ctrl/Cmd + /` — Toggle sidebar
- `Esc` — Close modals
