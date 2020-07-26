# SaaS Starter

> A template to get your next SaaS idea off the ground quicker

## Goal
Provide a useful template to get your idea off the ground quicker. Deliver basic web application functionality
out of the box. Stop reinventing the wheel. 

Spend your time developing your app instead of dealing with the taken-for-granted bits. 

## Offers

### Features
- Single-DB (tenant_id) multi tenancy
- Login / Register
- 2FA
  - Setup 2FA
  - Disable 2FA
  - Check 2FA on login
  - Remember Browsers to skip 2FA
  - 2FA Backup Tokens
  - 2FA Safe device management
    - View saved devices
    - Remove safe devices
- Session management
  - List active sessions
  - Destroy individual sessions
    - Doesn't work with "Remember me" sessions
  - Logout everywhere
- Docker support
- GitHub Actions support
- First time sign-in setup
- API Tokens
- User settings
  - Name
  - Email
    - With update notification to old email address
  - Password
    - With update notification
- Subscription management (Stripe)
  - Invoices
  - Create subscription
  - Swap plan
  - Cancel subscription
  - Update credit card
  - SCA (Europe)
  - Thank you for subscribing email
- Deactivate account
  - Cancels active subscription
  - Keeps data if user decides to return
- Trial functionality
  - No card up front
- Authentication log
  - Login
  - Two Factor enabled / disabled
- Welcome email
- Account activation in app
  - Not forced on account creation (reduced friction)
- Delete account
  - Cancels active subscription
- Account activation in app
  - Show "Resend verification email" on profile if email is not verified

### DX
- barryvdh/laravel-ide-helper
- barryvdh/laravel-debugbar

## WIP


## Planned
- Profile image management
- Delete account
  - Anonymizes associated user data
    - To keep relations with user crated content
- Team management
  - Rename
  - Invite members to team
  - Block members
    - Prevents login / reduces seat count
  - User impersonation
- Role management
  - Create roles
  - Assign permissions to roles
  - Assign users to roles

## Gotchas
Uses almost no styling. Basic bootstrap look. Some parts may even be completely unstyled. 
