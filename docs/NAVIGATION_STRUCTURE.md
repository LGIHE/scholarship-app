# Navigation Structure - Reorganized

## Overview
The admin panel navigation has been reorganized to eliminate redundancy and provide a more logical structure that separates applicants from scholars.

## New Navigation Structure

```
📊 Dashboard
│
├── 📝 APPLICATION MANAGEMENT
│   ├── 📄 Applications (Sort: 1)
│   │   └── View, manage, and process scholarship applications
│   │
│   └── 👥 Applicants (Sort: 2)
│       └── Manage users who have NOT been approved yet
│           - Only shows users with "Applicant" role
│           - Excludes users who have been promoted to "Scholar"
│           - Quick link to view their applications
│
├── 🎓 SCHOLAR MANAGEMENT
│   ├── 🎓 Scholars (Sort: 1)
│   │   └── Manage scholar academic records
│   │       - University, Course, Student ID
│   │       - Academic progress tracking
│   │       - Quick link to user account
│   │
│   └── 👤 Scholar Users (Sort: 2)
│       └── Manage user accounts for approved scholars
│           - Only shows users with "Scholar" role
│           - View academic details
│           - Quick link to scholar record
│
└── ⚙️ SYSTEM ADMINISTRATION
    ├── 👤 System Users (Sort: 1)
    │   └── Manage admin accounts
    │       - System Admin
    │       - Committee Member
    │
    └── 🛡️ Roles (Sort: 2)
        └── Manage system roles and permissions
```

## Key Differences from Previous Structure

### Before (Redundant):
```
❌ Scholarship Management
   - Applications
   - Scholars

❌ User Management
   - System Users
   - Applicant Users (showed BOTH Applicants AND Scholars)
   - Roles
```

### After (Logical Separation):
```
✅ Application Management
   - Applications
   - Applicants (ONLY non-approved users)

✅ Scholar Management
   - Scholars (academic records)
   - Scholar Users (ONLY approved users)

✅ System Administration
   - System Users
   - Roles
```

## Resource Details

### 1. Application Management Section

#### Applications Resource
**Purpose**: Manage scholarship applications

**Features**:
- View all applications
- Filter by status
- Approve/Reject applications
- View scoring breakdown
- When approved → User automatically gets "Scholar" role

**Navigation**: Application Management → Applications

---

#### Applicants Resource
**Purpose**: Manage users who are still in the application phase

**Query Filter**:
```php
whereHas('roles', function ($query) {
    $query->where('name', 'Applicant');
})
->whereDoesntHave('roles', function ($query) {
    $query->where('name', 'Scholar');
})
```

**Features**:
- Create new applicant accounts
- Edit applicant information
- View application count
- Quick link to view their applications
- Only shows "Applicant" role (no Scholar option)

**Navigation**: Application Management → Applicants

---

### 2. Scholar Management Section

#### Scholars Resource
**Purpose**: Manage academic records for approved scholars

**Features**:
- View all scholar records
- Edit university, course, student ID
- Track academic progress
- Quick link to view user account
- Automatically created when application is approved

**Navigation**: Scholar Management → Scholars

---

#### Scholar Users Resource
**Purpose**: Manage user accounts for approved scholars

**Query Filter**:
```php
whereHas('roles', function ($query) {
    $query->where('name', 'Scholar');
})
```

**Features**:
- View all scholar user accounts
- Edit user information (name, email, password)
- View academic details (university, course, student ID)
- Quick link to scholar academic record
- Create new scholar accounts manually

**Navigation**: Scholar Management → Scholar Users

---

### 3. System Administration Section

#### System Users Resource
**Purpose**: Manage admin-level accounts

**Query Filter**:
```php
whereHas('roles', function ($query) {
    $query->whereIn('name', ['System Admin', 'Committee Member']);
})
```

**Features**:
- Create/edit/delete admin accounts
- Assign System Admin or Committee Member roles
- Secure password management

**Navigation**: System Administration → System Users

---

#### Roles Resource
**Purpose**: Manage system roles

**Features**:
- Create/edit/delete roles
- View user count per role
- Color-coded badges

**Navigation**: System Administration → Roles

---

## User Journey Flow

### Applicant → Scholar Journey

```
1. User Registers
   └─> Assigned "Applicant" role
   └─> Appears in: Application Management → Applicants

2. User Submits Application
   └─> Application appears in: Application Management → Applications

3. Committee Reviews Application
   └─> Status: Submitted → Under Review

4. Committee Approves Application
   └─> User gets "Scholar" role (in addition to Applicant)
   └─> Scholar record created automatically
   └─> User now appears in: Scholar Management → Scholar Users
   └─> User removed from: Application Management → Applicants
   └─> Scholar record appears in: Scholar Management → Scholars

5. Admin Updates Scholar Details
   └─> Edit university, course, student ID
   └─> Track academic progress
```

## Quick Actions & Cross-Links

### From Applicants → Applications
**Action**: "View Applications" button
**Purpose**: See all applications submitted by this applicant
**Condition**: Only visible if user has submitted applications

### From Scholar Users → Scholars
**Action**: "View Scholar Details" button
**Purpose**: Jump to the academic record for this scholar
**Condition**: Only visible if scholar record exists

### From Scholars → Scholar Users
**Action**: "View User Account" button
**Purpose**: Jump to the user account for this scholar
**Condition**: Always visible

## Benefits of New Structure

### 1. **Clear Separation**
- Applicants and Scholars are now in separate sections
- No confusion about which resource to use
- Logical progression from application to scholarship

### 2. **No Redundancy**
- Each user appears in only ONE user management resource
- Applicants: Application Management → Applicants
- Scholars: Scholar Management → Scholar Users

### 3. **Better Context**
- Application Management focuses on the application process
- Scholar Management focuses on active scholars
- System Administration focuses on system-level concerns

### 4. **Improved Workflow**
- Natural flow from application to scholarship
- Easy to find pending applicants
- Easy to manage active scholars
- Clear distinction between phases

### 5. **Cross-Linking**
- Quick navigation between related records
- View applications from applicant record
- View user account from scholar record
- View scholar details from user account

## Widget Updates

### Scholar Stats Widget
Updated to reflect new structure:

1. **Active Scholars**: Total count of scholar records
2. **Pending Applicants**: Users with Applicant role (not yet approved)
3. **Complete Profiles**: Scholars with full academic information

## Technical Implementation

### Files Created:
- `app/Filament/Resources/ScholarUserResource.php`
- `app/Filament/Resources/ScholarUserResource/Pages/ListScholarUsers.php`
- `app/Filament/Resources/ScholarUserResource/Pages/CreateScholarUser.php`
- `app/Filament/Resources/ScholarUserResource/Pages/EditScholarUser.php`

### Files Modified:
- `app/Filament/Resources/ApplicantUserResource.php` (filtered to exclude scholars)
- `app/Filament/Resources/ApplicationResource.php` (navigation group updated)
- `app/Filament/Resources/ScholarResource.php` (navigation group updated, added cross-link)
- `app/Filament/Resources/SystemUserResource.php` (navigation group updated)
- `app/Filament/Resources/RoleResource.php` (navigation group updated)
- `app/Filament/Widgets/ScholarStatsWidget.php` (metrics updated)

## Summary

The new structure provides:
- ✅ Logical separation between applicants and scholars
- ✅ No redundancy in user management
- ✅ Clear workflow progression
- ✅ Better organization with meaningful sections
- ✅ Quick cross-navigation between related records
- ✅ Improved user experience for administrators

---

**Version**: 3.0
**Last Updated**: April 28, 2026
