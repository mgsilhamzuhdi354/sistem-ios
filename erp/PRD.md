# PT Indo OceanCrew Services - ERP System
## Product Requirements Document (PRD)

## Overview
A comprehensive ERP (Enterprise Resource Planning) web application for PT Indo OceanCrew Services, a professional crewing agency and ship chandler company. The system manages crew operations, contracts, payroll, finance, recruitment, vessels, and documents.

## Technology Stack
- **Backend**: PHP with CodeIgniter 4 framework
- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Database**: MySQL
- **Server**: Apache (Laragon)

## Core Modules & Features

### 1. Authentication (Auth)
- User login with email/username and password
- Password reset functionality
- OTP verification
- Session management
- Role-based access control

### 2. Dashboard
- Overview statistics (crew count, active contracts, vessels)
- Recent activities
- Quick action buttons
- Charts and analytics widgets
- Financial summary

### 3. Crew Management
- List all crew members with search and filter
- Add/Edit crew profiles (personal info, documents, certifications)
- Crew status tracking (active, standby, on leave)
- Document management per crew

### 4. Contract Management
- Create new contracts for crew assignments
- Contract approval workflow
- Contract renewal and termination
- Expiring contracts alerts
- Contract timeline view
- Contract history

### 5. Vessel Management
- Register and manage vessels
- Vessel details (type, flag, tonnage, IMO number)
- Assign crew to vessels
- Vessel status tracking

### 6. Payroll
- Salary calculation and processing
- Payslip generation (PDF)
- Payroll history
- Deductions and allowances management

### 7. Finance Module
- Chart of accounts management
- Journal entries
- Invoice creation and management
- Bill management
- Cost center tracking
- Financial dashboard with spending categories
- Budget tracking with percentage display

### 8. Recruitment Hub
- Recruitment pipeline management
- Referral code system for recruiters
- Recruiter performance tracking with points system
- Candidate evaluation

### 9. Document Management
- Upload and categorize documents
- Document expiry tracking
- Document parser for automated data extraction

### 10. Analytics & Reporting
- Crew analytics
- Financial reports
- Operational reports
- Export to Excel/PDF

### 11. Monitoring
- Integration monitoring
- Activity tracking
- System health dashboard

### 12. Settings & User Management
- System configuration
- User accounts management
- Role and permission settings
- Notification preferences
- WhatsApp notification integration

### 13. Smart Import
- Excel/CSV file import
- Automated header detection
- Data validation and mapping
- Bulk data import with progress tracking

### 14. Client Management
- Client company profiles
- Client contact information
- Service agreements

### 15. Admin Checklist
- Operational checklists
- Task tracking for administrative items

## User Roles
- **Super Admin**: Full system access
- **Admin**: Manage most modules
- **HR/Recruiter**: Crew, contracts, recruitment
- **Finance**: Financial modules, payroll
- **Viewer**: Read-only access

## Key URLs & Navigation
- Login: `/auth/login`
- Dashboard: `/dashboard`
- Crews: `/crews`
- Contracts: `/contracts`
- Vessels: `/vessels`
- Payroll: `/payroll`
- Finance: `/finance`
- Recruitment: `/recruitment`
- Settings: `/settings`
