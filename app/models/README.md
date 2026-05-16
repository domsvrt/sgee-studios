# Models Guide

All application data access lives in:

- `app/models/shared/*`

There are no role-specific model folders. Public, user, and admin flows all reuse shared models.

## Core structure

- `baseModel.php`
  - Shared DB connection access and common model foundation.
- Domain models:
  - `userModel.php`
  - `serviceCategoryModel.php`
  - `serviceSectionModel.php`
  - `serviceModel.php`
  - `bookingModel.php`
  - `bookingItemModel.php`
  - `bookingStatusLogModel.php`
  - `notificationModel.php`
  - `passwordResetRequestModel.php`
  - `activityLogModel.php`

## How models are used

1. Route is matched in `public/index.php`.
2. A role controller forwards to shared handler logic in `app/controllers/shared/*`.
3. Shared handler methods call one or more models in `app/models/shared/*`.
4. Returned data is passed to views in `app/views/*`.

## Where to edit

- Change query/filter/order behavior: edit the relevant method in the domain model.
- Change write behavior/validation side effects: trace controller payload handling in `app/controllers/shared/*` and adjust model method calls there.
