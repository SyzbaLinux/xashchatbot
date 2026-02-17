# User Profile Management System

This document outlines the complete profile management system for the ErrandRunner application, including personal information management, password changes, and two-factor authentication (2FA) setup.

## Features

### 1. Personal Information Management
Users can update their profile information including:
- **Full Name** - Change their display name
- **Email Address** - Update email (with uniqueness validation)
- **Member Since** - View-only field showing account creation date

**Route**: `GET/PUT /user/profile`
**Controller**: `App\Http\Controllers\User\ProfileController@edit` / `@update`

### 2. Password Management
Secure password change with validation:
- **Current Password** - Must verify current password before change
- **New Password** - Must meet security requirements:
  - Minimum 8 characters
  - Contains uppercase letters
  - Contains lowercase letters
  - Contains numbers
  - Contains special characters
- **Confirm Password** - Password confirmation required

**Route**: `PUT /user/profile/password`
**Controller**: `App\Http\Controllers\User\ProfileController@updatePassword`

### 3. Two-Factor Authentication (2FA)

#### Enable 2FA
1. User initiates 2FA setup
2. System generates secret key using Google Authenticator
3. QR code is displayed for scanning
4. Recovery codes are generated (8 codes for emergency access)
5. User verifies with authenticator code
6. 2FA becomes active

**Routes**:
- `POST /user/two-factor-authentication` - Enable 2FA
- `POST /user/recovery-codes` - Regenerate recovery codes
- `DELETE /user/two-factor-authentication` - Disable 2FA

**Controller**: `App\Http\Controllers\User\ProfileController`

#### Recovery Codes
- 8 recovery codes generated when 2FA is enabled
- Can be regenerated anytime with password confirmation
- Should be stored securely
- Each code can be used once

#### Disable 2FA
- Requires password confirmation for security
- Removes all 2FA settings
- User can re-enable anytime

## User Interface

### Profile Settings Page
Located at: `/user/profile`

Three main tabs:
1. **Personal Info**
   - Name and email fields
   - Submit form to update

2. **Password**
   - Current password verification
   - New password with strength indicators
   - Password confirmation

3. **Security**
   - 2FA status display
   - Enable/Disable 2FA buttons
   - Recovery codes management

## Database Schema

### Users Table Fields
```php
$table->text('two_factor_secret')->nullable(); // Encrypted 2FA secret
$table->text('two_factor_recovery_codes')->nullable(); // JSON array of recovery codes
```

## API Endpoints

### Profile Management
| Method | Endpoint | Name | Auth | Description |
|--------|----------|------|------|-------------|
| GET | `/user/profile` | user.profile.edit | ✓ | Show profile edit page |
| PUT | `/user/profile` | user.profile.update | ✓ | Update profile info |
| PUT | `/user/profile/password` | user.profile.password | ✓ | Update password |

### Two-Factor Authentication
| Method | Endpoint | Name | Auth | Description |
|--------|----------|------|------|-------------|
| POST | `/user/two-factor-authentication` | user.two-factor.enable | ✓ | Enable 2FA |
| DELETE | `/user/two-factor-authentication` | user.two-factor.disable | ✓ | Disable 2FA |
| POST | `/user/recovery-codes` | user.recovery-codes | ✓ | Generate new recovery codes |

## Security Considerations

1. **Password Validation**
   - Uses Laravel's `current_password` validation
   - Requires strong passwords (8+ chars, mixed case, numbers, special chars)

2. **2FA Protection**
   - Google Authenticator compatible
   - Encrypted secret storage
   - Recovery codes for emergency access
   - Password required to disable

3. **Data Validation**
   - Email uniqueness check
   - Password confirmation matching
   - Current password verification

4. **Error Handling**
   - Clear error messages
   - Form validation feedback
   - User-friendly notifications

## Frontend Components

### Profile/Edit.vue
Main profile management page with:
- Tab navigation
- Form handling with loading states
- Modal confirmations
- Error display
- Success notifications

## Installation & Setup

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Install 2FA Dependency
```bash
composer require pragmarx/google2fa
```

### 3. Access Profile
Login as a user and navigate to Profile Settings from the sidebar or user menu.

## Usage Examples

### Update Profile Information
```javascript
const form = useForm({
  name: 'John Doe',
  email: 'john@example.com',
});

form.put(route('user.profile.update'));
```

### Update Password
```javascript
const form = useForm({
  current_password: 'current123!',
  password: 'NewPassword123!',
  password_confirmation: 'NewPassword123!',
});

form.put(route('user.profile.password'));
```

### Enable 2FA
```javascript
// Initiates 2FA setup, redirects to verification page
router.post(route('user.two-factor.enable'));
```

### Disable 2FA
```javascript
const form = useForm({
  password: 'user_password',
});

form.delete(route('user.two-factor.disable'));
```

## Future Enhancements

- [ ] Account deletion
- [ ] Login history tracking
- [ ] Active sessions management
- [ ] Email verification reminders
- [ ] Two-step verification options (SMS, Email)
- [ ] Backup codes download as PDF
- [ ] Account activity log
- [ ] Connected devices management

## Troubleshooting

### 2FA Not Working
- Ensure user's device time is synchronized
- Check that secret key is properly stored in database
- Verify authenticator app is compatible

### Password Update Fails
- Check password meets all requirements
- Verify current password is correct
- Check password confirmation matches

### Recovery Code Issues
- Generate new recovery codes if lost
- Ensure password is provided for confirmation
- Check recovery codes are stored correctly

## Support

For issues or questions about profile management, please refer to the main application documentation or contact support.
