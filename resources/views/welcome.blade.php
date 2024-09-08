<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API Document</title>
</head>
<body>
    <h1>API Document</h1>
    <pre>
        GET|HEAD        /
        GET|HEAD        api/comments ............................ comments.index › Jobs\CommentController@index
        POST            api/comments ............................ comments.store › Jobs\CommentController@store
        DELETE          api/comments/{comment} .................. comments.destroy › Jobs\CommentController@destroy
        GET|HEAD        api/employer/jobs ....................... EmployerJobController@index
        POST            api/employer/{job}/cancel ............... EmployerJobController@cancelJob
        GET|HEAD        api/jobs ................................ jobs.index › Jobs\JobController@index
        POST            api/jobs ................................ jobs.store › Jobs\JobController@store
        GET|HEAD        api/jobs/{job} .......................... jobs.show › Jobs\JobController@show
        PUT|PATCH       api/jobs/{job} .......................... jobs.update › Jobs\JobController@update
        DELETE          api/jobs/{job} .......................... jobs.destroy › Jobs\JobController@destroy
        PUT             api/jobs/{job}/status ................... Jobs\JobController@acceptReject
        POST            api/login ............................... Auth\AuthController@login
        GET|HEAD        api/logout .............................. Auth\AuthController@logout
        POST            api/register ............................ Auth\AuthController@register
        GET|HEAD        api/user ................................ Auth\AuthController@me
        GET|HEAD        sanctum/csrf-cookie ..................... sanctum.csrf-cookie › Laravel\Sanctum › CsrfCookieController@show
        GET|HEAD        up ...................................... Mintor for testing
    </pre>
    <pre>
[
    "api/user": "Get data about the authenticated user",
    "api/login": "Login with email and password",
    "api/logout": "Logout",
    "api/register": "Register a new user {name, email, password}",
    "api/comments": [
        "GET": "Get all comments Only for admin",
        "POST": "Create new comment {content, job_id}",
        "DELETE": "Delete comment by id"
    ],
    "api/jobs": [
        "GET": "Get all jobs Only status open",
        "POST": "Create new job {title, description, experience_level, ...}",
        "PUT|PATCH": "Update job by id and new data",
        "DELETE": "Delete job by id"
    ],
    "api/employer": [
        "GET": "Get all jobs of the current employer",
        "POST": "Cancel job by id '/{job_id}/cancel'"
    ],
    "api/sanctum/csrf-cookie": "Generate a new csrf token",
    "api/jobs/{job}/status": "Accept or reject a job with admin permission, { status = 'accepted' | 'rejected' }"
]
    </pre>
</body>
</html>