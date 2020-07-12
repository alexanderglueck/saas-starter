# SaaS Starter

> A template to get your next SaaS idea off the ground quicker

## Goal
Provide a useful template to get your idea off the ground quicker. Deliver basic web application functionality
out of the box. Stop reinventing the wheel. 

Spend your time developing your app instead of dealing with the taken-for-granted bits. 

## Offers

### Features
- 2FA
- Remember Browsers to skip 2FA
- 2FA Backup Tokens
- List active sessions
- Destroy individual sessions
  - Doesn't work with "Remember me" sessions
- Logout everywhere
- Single-DB (tenant_id) multi tenancy
- Login / Register

### DX
- barryvdh/laravel-ide-helper
- barryvdh/laravel-debugbar

## WIP
- 2FA Safe device management
  - view saved devices
  - remove save devices

## Planed
- Profile image management
- API Tokens
- User settings
  - Name
  - Email
    - With reminder email to old address
  - Password
    - With update notification
- Account activation in app
  - Not forced on account creation (reduced friction)
- Subscription management
  - Invoices
  - Create subscription
  - Swap plan
  - Cancel subscription
  - Update credit card
- Deactivate account
  - Cancels active subscription
  - Keeps data if user decides to return
- Delete account
  - Cancels active subscription
  - Anonymizes associated user data
    - To keep relations with user crated content
- Trial functionality
  - No card up front
- Authentication log
- Team management
  - Rename
  - Add members to team
  - Block members
    - Prevents login / reduces seat count
- Role management
  - Create roles
  - Assign permissions
  - Assign users
- First login welcoming email

## Gotchas
Uses almost no styling. Basic bootstrap look. Some parts may even be completely unstyled. 
