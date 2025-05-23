Plugin Functionaltiy:
=====================

Form Short Code : [testimonial_form]

Listing Short Code :[testimonial_list] 

Create a basic testimonial system in WordPress using custom post types, custom meta fields, and shortcodes—without using any third-party plugins. 
Users can submit testimonials via a frontend form, and approved ones are displayed in a Bootstrap carousel.

================

Requirements:
==============


Backend:
=========


Custom Post Type:

    testimonial for storing testimonials. 
    SS : https://prnt.sc/O5_eKU5px6rK

Custom Fields:

    Name: Stored as post title.

    Email: Stored as private meta field.

    Testimonial Content: Stored in a textarea meta field.

    Status: Custom field with values: Pending, Approved, Rejected.

    SS: https://prnt.sc/O5_eKU5px6rK

Admin Interface Enhancements:

    Custom columns: Name, Email, Status.

    Quick Edit dropdown to update status directly from the list view.

    SS: https://prnt.sc/o2e9YcShL-WT

Notification Email:

    Admin receives an email when a new testimonial is submitted.
    Not Tested in My local host.




Frontend :
===========


    Frontend Testimonial Submission Form

        Accessible to non-logged-in users : SS : https://prnt.sc/itr1Enn97rml

        Responsive and visually appealing design SS: https://prnt.sc/HIQGGvMDauEM

        Includes input validation and sanitization : https://prnt.sc/yuG_XxYPhb_D

        Displays a success message upon successful submission : https://prnt.sc/bMsTe5T0H1sJ

        Submissions are stored as pending testimonial posts  : https://prnt.sc/tAffHNK3gbJ_

    Testimonials Display Page

        Shows the latest 5 approved testimonials 
            backend : https://prnt.sc/A0F6Yw9gSLnp
            Front : https://prnt.sc/4mUxO72zliDo

        Displays only the name and content: https://prnt.sc/4mUxO72zliDo

        Presented in a responsive, visually appealing slider : https://prnt.sc/7FGGrUxppe3D

      
Challenge Areas
=================

AJAX Form Submission : https://prnt.sc/aq3_w0EWWSj3

Shortcode / Block Support :
For display Form Short Code :  [testimonial_form]   https://prnt.sc/714VI04SWZdq
For display Listing Short code :[testimonial_list]  https://prnt.sc/1O7vtpkSSRUB

REST API Endpoint
Created a secure REST API endpoint 

Listing:
 End point :http://localhost:8888/test/wp-json/wp/v2/testimonial/1
 Method : GET
 SS: https://prnt.sc/bYvpQDvi46WY


Submit: 
 End Point: http://localhost:8888/test/wp-json/custom/v1/testimonials
 Method : POST
 PARAM : name, content
 SS:https://prnt.sc/BYqWi8m5MALY


Star Ratings: Added: https://prnt.sc/vblEtoV1lEwo

