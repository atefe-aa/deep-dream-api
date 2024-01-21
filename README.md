# Deep Dream

## Description
Deep Dream is a comprehensive slide scanner manager application designed to manage tests, test types, patients, and collaborations between laboratories and counselors. The application facilitates manual and automated scanning, image annotation using Cytomine, result templating, and advanced statistical visualization. It leverages Python for camera and scanner control and utilizes Laravel Sanctum for authentication.

## Repository Links:

- [Deep Dream Laravel API](https://github.com/atefe-aa/deep-dream-api)
- [Deep Dream Front-end](https://github.com/atefe-aa/deep-dream-front-end)


## Features

### Test Management: 
Create, edit, and manage tests and test types.

### Collaborations:
Facilitate annotations and collaborations between laboratories and counselors.

### Scanner Control: 
Manual and auto mode for scanning, allowing configuration of scanning regions and magnifications.

### Cytomine Integration:
Display scanned images with filters, zoom, and drawing features using [Cytomine](https://cytomine.com/).


### Result Templating:
Create and print result templates.

### Statistical Visualization:
Charts to visualize test statistics by price, quantity, and test types.

### Notifications: 
Automated notifications for test status changes and SMS alerts to counselors for image reviews.


### Role-Based Access Control (RBAC):
- Admin: Full access to all features, charts, tests, laboratories, and counselors. CRUD access for all components.
- Operator: Create tests, configure machine settings, and utilize manual and scanning features.
- Laboratories: CRUD access to their tests, counselors, and view completed scanned images of their tests.
- Counselors: View assigned images in Cytomine and create annotations.

## Technologies Used

- ### Backend:

  - Laravel (with Sanctum for authentication)
  - Python (for camera and scanner control)
  - Spatie/Permissions for role-based access control

- ### Frontend:

  - React
  - Bootstrap (styling)

---
  ## Installation
1. Clone the repository.
2. Install dependencies:
```composer install```

3. Install Spatie/Permissions:
```composer require spatie/laravel-permission```

4. Publish and migrate permissions:

 ``` php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations" and php artisan migrate```

5. Configure .env based on the database
6. Run the app:
```php artisan serve```
 
---

## Security Measures
- Laravel's built-in security features, including _CSRF_ protection.
- Regular updates and patches for Laravel and dependencies.
- Role-based access control using Spatie/Permissions.
- Sanitization and validation of user input.
- Secure storage of sensitive information.

## Contact Information
For questions or inquiries, feel free to contact **Atefe** **Ali** **Asgariyan** at aa.asgariyan12@gmail.com

