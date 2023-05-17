# FITBOX APP
Abandoned old project for a Gym management tool written in PHP using CodeIgniter. 

The tool was designed to be both an ERP and a CRM for multiple gyms, crossfit boxes, yoga studios, etc... 

Users management, payments and subscriptions; as well as Athletes nutrition, booking is 90% functional (CRM functionality). Where as the part for company resources management (ERP) part is a pending/TO-DO.

## Users hierarchy
- The **SUDO** user is the only one able to create new gyms (boxes) and its first SADMIN user.
- SADMINS can create/delete other SADMIN and "staff" users of a given box (gym)
- For every box (gym) there may be multiple staff users: ADMINS, COACHES (basic and full), Human Resources(rrhh), finance, etc... Each user type has different permissions. For example: Full coaches can manage trainning sessions, calendar, and Athletes (clients) payments. Basic coaches on the other hand cannot manage payments.
- Each staff person may have multiple user types assigned.
- ATHLETE is the user type for the clients.

## Calendar functionality
- As what has become the standard in most gyms, every Sunday evening the next week calendar is opened for booking.
- Depending on the type of subscription an Athlete has paid for, it may book different number (and type) of activities per week.
- The calendar it self (as well as the whole app) is both desktop and mobile screens friendly.

## Payments
- Stripe (for card payments) and GoCardless (for bank transfer payments) have been integrated in the app.
- There is also the posibility to register cash payments, of course.

## Nutrition
- Every Athlete can register their meals and frequent recepies.
- The app will inform of micro and macronutrients based on the ingredients. (it uses USDA as data source).

## Trainning sessions
- There is a stack of exercises that can be edited. 
- Training sessions design for coaches is fully functional.
- Trainning sessions results is a pending/TO-DO.

## Social / Community
- The app was design with a social functionality in mind although it has not been coded yet (and wont be by my part).
