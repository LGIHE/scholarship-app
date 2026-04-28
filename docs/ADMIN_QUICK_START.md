# Admin System - Quick Start Guide

## 🚀 Getting Started

### 1. Access the Admin Panel

**URL**: `http://your-domain.com/admin`

**Login Credentials** (from seeder):
- **System Admin**: `admin@example.com`
- **Committee Member**: `committee@example.com` or `c.nkunze@lgfug.org`

### 2. Dashboard Overview

Upon login, you'll see the enhanced dashboard with:

#### Analytics Widgets:
1. **Application Stats** (Top Row)
   - Total Applications
   - Pending Review
   - Approved Scholars
   - Average Score

2. **Scholar Stats** (Top Row)
   - Active Scholars (with trend)
   - Total Applicants
   - New Scholars (30 days)

3. **Applications by Status Chart** (Middle)
   - Visual breakdown of all application statuses

4. **Recent Applications Table** (Bottom)
   - Last 5 applications with quick details

## 📋 Main Features

### Scholarship Management Section

#### 1. Applications
**Path**: Admin → Applications

**What you can do**:
- View all scholarship applications
- Filter by status (Draft, Submitted, Under Review, Approved, Rejected)
- View detailed application information
- Change application status
- Approve applications (automatically creates Scholar record)
- Reject applications
- View scoring breakdown

**Quick Actions**:
- **Under Review**: Mark submitted applications for review
- **Approve**: Approve application and convert to Scholar
- **Reject**: Reject application

#### 2. Scholars
**Path**: Admin → Scholars

**What you can do**:
- View all active scholars
- Add new scholars manually
- Edit scholar information (University, Course, Student ID)
- View scholar details
- Track academic progress (via relation manager)

### User Management Section

#### 1. System Users
**Path**: Admin → System Users

**Purpose**: Manage admin-level accounts

**What you can do**:
- Create new System Admin or Committee Member accounts
- Edit existing admin users
- Assign roles (System Admin or Committee Member)
- Delete admin accounts
- Filter by role

**User Types**:
- **System Admin**: Full system access
- **Committee Member**: Application review and scholarship management

#### 2. Applicant Users
**Path**: Admin → Applicant Users

**Purpose**: Manage applicant-level accounts

**What you can do**:
- Create new Applicant or Scholar accounts
- Edit existing applicant users
- Assign roles (Applicant or Scholar)
- View application count per user
- Delete applicant accounts
- Filter by role

**User Types**:
- **Applicant**: Can submit scholarship applications
- **Scholar**: Approved applicants with scholarship awards

#### 3. Roles
**Path**: Admin → Roles

**Purpose**: Manage system roles

**What you can do**:
- Create new roles
- Edit existing roles
- View user count per role
- Delete roles (with confirmation)

**Default Roles**:
- System Admin (Red badge)
- Committee Member (Yellow badge)
- Scholar (Green badge)
- Applicant (Blue badge)

## 🔐 Role-Based Access

### Login Behavior:

| Role | Login Redirect | Access Level |
|------|---------------|--------------|
| System Admin | `/admin` (Filament) | Full system access |
| Committee Member | `/admin` (Filament) | Application review & management |
| Applicant | `/portal` (Application) | Submit applications |
| Scholar | `/portal` (Application) | View application status |

### Permissions:

- **System Admin**: Can access all features
- **Committee Member**: Can review applications, manage scholars
- **Applicant**: Can only access application portal
- **Scholar**: Can only access application portal

## 📊 Common Workflows

### Workflow 1: Review and Approve Application

1. Go to **Applications**
2. Filter by status: "Submitted"
3. Click **View** on an application
4. Review all details and scoring
5. Go back to list
6. Click **Under Review** action
7. After review, click **Approve** or **Reject**
8. If approved, Scholar record is automatically created

### Workflow 2: Create New Committee Member

1. Go to **System Users**
2. Click **Create**
3. Fill in:
   - Name
   - Email
   - Password
4. Select Role: **Committee Member**
5. Click **Create**
6. User can now login at `/admin`

### Workflow 3: Create New Applicant Account

1. Go to **Applicant Users**
2. Click **Create**
3. Fill in:
   - Name
   - Email
   - Password
4. Select Role: **Applicant**
5. Click **Create**
6. User can now login and will be redirected to `/portal`

### Workflow 4: Manage Scholar Information

1. Go to **Scholars**
2. Click **Edit** on a scholar
3. Update:
   - University
   - Course
   - Student ID
4. Click **Save**

## 🎨 Navigation Structure

```
Dashboard (Home)
│
├── Scholarship Management
│   ├── Applications (View & manage applications)
│   └── Scholars (Manage active scholars)
│
└── User Management
    ├── System Users (Admin accounts)
    ├── Applicant Users (Applicant accounts)
    └── Roles (System roles)
```

## 🔍 Search & Filter Features

### Applications:
- Search by: User name, First name, Last name
- Filter by: Status
- Sort by: Score, Date

### System Users:
- Search by: Name, Email
- Filter by: Role (System Admin, Committee Member)

### Applicant Users:
- Search by: Name, Email
- Filter by: Role (Applicant, Scholar)

### Scholars:
- Search by: Name, Email, University, Course, Student ID

## 💡 Tips & Best Practices

1. **Regular Monitoring**: Check the dashboard daily for pending applications
2. **Status Updates**: Keep application statuses up-to-date
3. **Scholar Records**: Ensure scholar information is complete after approval
4. **User Management**: Regularly review user accounts and roles
5. **Security**: Use strong passwords for admin accounts
6. **Backup**: Regular database backups recommended

## 🆘 Troubleshooting

### Can't access admin panel?
- Verify you have System Admin or Committee Member role
- Check email and password
- Clear browser cache

### Application not showing?
- Check filter settings
- Verify application status
- Use search function

### Widget not loading?
- Refresh the page
- Check database connection
- Verify data exists

## 📞 Support

For technical issues or questions, contact the system administrator.

---

**Last Updated**: April 28, 2026
**Version**: 2.0
