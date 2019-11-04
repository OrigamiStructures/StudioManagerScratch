The system has been upgraded to 3.8. Quasi-core files like those in bin, 
index.php and the like have been fixed. 

Deprecations have been handled.

CakeDC/Users has been upgraded.

A Command (tester) has been written to run test suites because the phpunit 
xml runner was not handling fixtures properly but the tests would otherwise 
run properly.

The main goal is to prune useless legacy code. First, I'm working on 
getting SystemState out of the app. It's been rem'd out of AppModel and 
this is the current focus. 
