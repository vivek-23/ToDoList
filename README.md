# ToDoList Assignment

## Installation

* git clone this repo
* composer install
* Create a DB in MySQL called **to_do_list**
* Run **php artisan migrate** command.
* Run **php artisan db:seed** command.
* Attached to this repo is a postman collection with filename **ToDoList App.postman_collection.json** for testing.

# List of APIs made


## Create a task [POST request]

```
/api/task/create
```
Parameters in the POST request are:

* **title** (compulsory)
* **due_date** (compulsory) - in the format of **YYYY/mm/dd**.
* **parent_task** - Optional, should be the ID of the parent task if given, else leave it empty!)


## Complete a task [GET request]

```
/api/task/complete/{id}
```

* **{id}** - will be the id of the task to be marked as complete.

## Delete a task [GET request]

```
/api/task/delete/{id}
```

* **{id}** - will be the id of the task to be deleted(soft-delete).

## Search for tasks by title [POST request]

```
/api/task/search
```

* **title** - will be the key in the payload and give whatever value you wish to search for in the value section.


## Task List [GET request]

```
/api/task/list?task_range=today&pending=-1
```

* **task_range** - Optional key and will be one of the values from **today, this week,next week, overdue**. Leave it empty to fetch all tasks.

* **pending** - Compulsory, possible values are `-1`, `1` or `0`. `-1` being all tasks and `1` being only pending tasks and `0` being only completed tasks.


## Delete soft deletes older than a month [GET request]

```
/api/task/deleteSoftDeletes
```

Permanently deletes all soft deleted tasks older than a month.
