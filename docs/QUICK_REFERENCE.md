# Quick Reference Guide - Reorganized Structure

## 🎯 Where to Find What

### Managing Pending Applications

**Go to**: Application Management → Applications
- View all submitted applications
- Review and score applications
- Approve or reject applications
- View application details

**Go to**: Application Management → Applicants
- View users who haven't been approved yet
- Create new applicant accounts
- Edit applicant information
- View their submitted applications (quick link)

---

### Managing Approved Scholars

**Go to**: Scholar Management → Scholars
- View all scholar academic records
- Edit university, course, student ID
- Track academic progress
- View user account (quick link)

**Go to**: Scholar Management → Scholar Users
- View user accounts for approved scholars
- Edit user information (name, email, password)
- See academic details preview
- View full scholar record (quick link)

---

### Managing System Access

**Go to**: System Administration → System Users
- Manage admin accounts
- Create System Admin or Committee Member accounts
- Edit admin user information

**Go to**: System Administration → Roles
- View all system roles
- Create new roles
- See user count per role

---

## 🔄 Common Workflows

### Workflow 1: Process New Application

```
1. Application Management → Applications
2. Filter by status: "Submitted"
3. Click "View" to review details
4. Click "Under Review" action
5. After review, click "Approve" or "Reject"
6. If approved:
   - User automatically gets Scholar role
   - Scholar record created
   - User moves from "Applicants" to "Scholar Users"
```

### Workflow 2: Update Scholar Academic Info

```
1. Scholar Management → Scholars
2. Find the scholar
3. Click "Edit"
4. Update university, course, student ID
5. Click "Save"
```

### Workflow 3: Manage Scholar User Account

```
1. Scholar Management → Scholar Users
2. Find the scholar
3. Click "Edit"
4. Update name, email, or password
5. Click "Save"

OR

1. Scholar Management → Scholars
2. Find the scholar
3. Click "View User Account" action
4. Edit user information
```

### Workflow 4: Create New Applicant

```
1. Application Management → Applicants
2. Click "Create"
3. Fill in name, email, password
4. Role is automatically set to "Applicant"
5. Click "Create"
6. User can now login and submit applications
```

### Workflow 5: Create New Committee Member

```
1. System Administration → System Users
2. Click "Create"
3. Fill in name, email, password
4. Select role: "Committee Member"
5. Click "Create"
6. User can now login to admin panel
```

---

## 📊 Dashboard Widgets

### Application Stats Widget
- **Total Applications**: All applications ever submitted
- **Pending Review**: Applications needing action (Submitted + Under Review)
- **Approved Scholars**: Total approved applications
- **Average Score**: Average score across all applications

### Scholar Stats Widget
- **Active Scholars**: Total number of scholar records
- **Pending Applicants**: Users with Applicant role (not yet approved)
- **Complete Profiles**: Scholars with full academic information

### Applications by Status Chart
- Visual breakdown of applications by status
- Doughnut chart showing distribution

### Recent Applications Widget
- Last 5 applications submitted
- Quick overview with key details

---

## 🔍 Quick Search Tips

### Finding a Specific User

**If they're an applicant (not approved yet)**:
- Go to: Application Management → Applicants
- Search by name or email

**If they're a scholar (approved)**:
- Go to: Scholar Management → Scholar Users
- Search by name or email

**If they're an admin**:
- Go to: System Administration → System Users
- Search by name or email

### Finding Applications

**By applicant name**:
- Go to: Application Management → Applications
- Search in the user name column

**By status**:
- Go to: Application Management → Applications
- Use status filter dropdown

**By score**:
- Go to: Application Management → Applications
- Sort by "Total Score" column

---

## 🔗 Quick Links Between Resources

### From Applicants → Applications
**Button**: "View Applications"
- Shows all applications submitted by this applicant
- Only visible if they have submitted applications

### From Scholar Users → Scholars
**Button**: "View Scholar Details"
- Jumps to the academic record for this scholar
- Only visible if scholar record exists

### From Scholars → Scholar Users
**Button**: "View User Account"
- Jumps to the user account for this scholar
- Always visible

---

## 📋 Resource Cheat Sheet

| Resource | Location | Shows | Purpose |
|----------|----------|-------|---------|
| Applications | Application Management | All applications | Process applications |
| Applicants | Application Management | Users with Applicant role only | Manage pending applicants |
| Scholars | Scholar Management | Scholar academic records | Manage academic info |
| Scholar Users | Scholar Management | Users with Scholar role only | Manage scholar accounts |
| System Users | System Administration | Admin accounts | Manage admin access |
| Roles | System Administration | All roles | Manage permissions |

---

## 🎨 Navigation Icons

| Icon | Section | Items |
|------|---------|-------|
| 📝 | Application Management | Applications, Applicants |
| 🎓 | Scholar Management | Scholars, Scholar Users |
| ⚙️ | System Administration | System Users, Roles |

---

## ⚡ Keyboard Shortcuts (Filament Default)

- `Ctrl/Cmd + K`: Global search
- `Ctrl/Cmd + /`: Toggle sidebar
- `Esc`: Close modals

---

## 🆘 Troubleshooting

### "I can't find a user"

**Check their status**:
- If they haven't been approved → Application Management → Applicants
- If they've been approved → Scholar Management → Scholar Users
- If they're an admin → System Administration → System Users

### "A scholar appears in the wrong place"

**This shouldn't happen anymore!**
- Applicants: Only users with Applicant role (not Scholar)
- Scholar Users: Only users with Scholar role
- Each user appears in only ONE place

### "I approved an application but can't find the scholar"

**Check**:
1. Scholar Management → Scholars (academic record)
2. Scholar Management → Scholar Users (user account)
3. The user should have been automatically moved from "Applicants"

---

## 📞 Quick Actions Summary

| I want to... | Go to... | Action |
|--------------|----------|--------|
| Review applications | Application Management → Applications | View, Approve, Reject |
| Create applicant account | Application Management → Applicants | Create |
| Update scholar's university | Scholar Management → Scholars | Edit |
| Reset scholar's password | Scholar Management → Scholar Users | Edit |
| Add committee member | System Administration → System Users | Create |
| View all roles | System Administration → Roles | View list |
| See pending applicants | Application Management → Applicants | View list |
| See active scholars | Scholar Management → Scholar Users | View list |

---

**Version**: 3.0
**Last Updated**: April 28, 2026
