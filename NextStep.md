The system has been upgraded to 3.8. Quasi-core files like those in bin, 
index.php and the like have been fixed. 

Deprecations have been handled.

CakeDC/Users has been upgraded. The user system 
function was restored after the upgrade.

A Command (tester) has been written to run test suites because the phpunit 
xml runner was not handling fixtures properly but the tests would otherwise 
run properly.

The main goal is to prune useless legacy code. First, I'm working on 
getting SystemState out of the app. 
It's been removed from AppModel and all models work. 
It's been removed from all Controllers and they all work (minimal testing). At 
least I can say there are no more references to SystemState in Controllers.

Currenly, SysState is still mentioned in AppController.

This keeps Components and views that have not been handled yet from breaking.

EditionStackComponent::stackQuery(), an original nested stack concept has been 
eliminated and ArtStack can now emit the equivalent data structures. These 
structures should be rethought, but all functionality was preserved.
