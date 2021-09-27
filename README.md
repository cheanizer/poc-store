## POC case study BE Enginier 
# Explanation :

From the fact, we can get a conclusion there is some process that use same resource in the same time. in high traffic transaction, there is some problem called race condition.
race condition can happen in database level or even in memory level. the fact misreported inventory quantities explain the existing order management system cannot handle that problem. 
what happened is when a checkout request comming from shoping chart (by user action) system will reserve the stock and deduct the current stock of inventory based from amount of stuff user requested. The problem's comming when it is in high traffict that reduce the performance of deduction process -- and for prerformance reason the system enable the multi processing in one time --, so the system is able to run another reduce process **before** the first one are finished. Thats means the second request can run the process with exacly same state with the first request. In multi processing process It can be two, or three process in a same time depend the thread counts from multiprocessing is self. 


Solution :
From explanation above, system keed to know the process are finish or not, usualy we are using flag to lock and unlock. it can prevent same process from another request run simultaneously that fire race condition. as long we are using PHP. there is not much option to lock the process. usualy we using flock to create exclusive lock and blocking wait until obtained.

POC
stack : 
PHP - Ngix - Mysql

Requirement : 
docker 

Run :
$ docker-compose up -d

Setup Project : 
$ docker exec poc ./setup.sh 

Url : 
http://localhost:8080
