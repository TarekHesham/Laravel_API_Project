<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API Document</title>
    <style>
        body {
            background-color: rgb(19 25 36);
            color: #ffffff;
            font-family: system-ui;
        }

        .section {
            background-color: #232c3d;
            border-radius: .875rem;
            margin-inline: 2rem;
            margin-block: 0;
            position: relative;
        }
        .section:hover {
            background-color: rgb(41 51 71 / 1);
            cursor: pointer;
            span {
                color: #21c8f6;
            }
        }

        .section::after {
            content: "";
            position: absolute;
            right: -20px;
            top: -25px;
            width: 4rem;
            height: 4rem;
            background-image: url(https://ik.imagekit.io/laracasts/series/thumbnails/png//automatic-laravel-upgrades-explained.png?tr=w-890);
            background-repeat: no-repeat;
            background-size: contain;
            transition: all 0.7s 0s ease;
            scale: 0.9;
            opacity: 0;
        }
        .section:hover::after {
            rotate: 360deg;
            scale: 1;
            opacity: 1;
        }

        span {
            font-size: 1rem;
            font-weight: bold;
            color: #bad9fcb3;
        }
        color {
            color: #00b7ff;
        }

        h1 {
            padding: 15px;
            width: max-content;
            border-radius: 14px;
            margin-top: 1.5rem;
        }
        h1::before {
            content: "";
            background-image: repeating-linear-gradient(-45deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .1) 2px, transparent 2px, transparent 9px);
            width: 100%;
            height: 23px;
            margin: 0;
            padding: 0;
            display: block;
            position: absolute;
            top: 3rem;
            left: 0;
            opacity: 25%;
        }

        ::-webkit-scrollbar {
            width: 7px;
        }
        ::-webkit-scrollbar-thumb {
            border-radius: 50px;
        }
        ::-webkit-scrollbar-thumb {
            --tw-bg-opacity: 1;
            background-color: rgb(69 88 120/ var(--tw-bg-opacity));
        }
        ::-webkit-scrollbar-track {
            --tw-bg-opacity: 1;
            background-color: rgb(20 25 36/ var(--tw-bg-opacity));
        }
    </style>
</head>
<body>
    <h1>API <color>D</color>ocument</h1>
    <pre>
    <p class="section">
    <span>Global:</span>
        GET|HEAD        api/search .............................. Jobs\SearchController@search
        GET|HEAD        api/search/autocomplete ................. Jobs\SearchController@autocomplete
        
        GET|HEAD        api/job/{slug} .......................... jobs.showBySlug › Jobs\JobController@showBySlug
        GET|HEAD        api/jobs ................................ jobs.index › Jobs\JobController@index
        GET|HEAD        api/jobs/{job} .......................... jobs.show › Jobs\JobController@show
        
        GET|HEAD        api/comments ............................ comments.index › Jobs\CommentController@index
        POST            api/comments ............................ comments.store › Jobs\CommentController@store
        DELETE          api/comments/{comment} .................. comments.destroy › Jobs\CommentController@destroy
    </p>
    <p class="section">
        <span>Candidate:</span>
        GET|HEAD        api/application ......................... application.index › ApplicationController@index
        POST            api/application ......................... application.store › ApplicationController@store
        GET|HEAD        api/application/{application} ........... application.show › ApplicationController@show
        DELETE          api/application/{application} ........... application.destroy › ApplicationController@destroy
    </p>
    <p class="section">
        <span>Employer:</span>
        POST            api/jobs ................................ jobs.store › Jobs\JobController@store
        PUT|PATCH       api/jobs/{job} .......................... jobs.update › Jobs\JobController@update
        GET|HEAD        api/employer/jobs ....................... EmployerJobController@index
        POST            api/employer/{job}/cancel ............... EmployerJobController@cancelJob
    </p>
    <p class="section">
    <span>Admin:</span>
        DELETE          api/jobs/{job} .......................... jobs.destroy › Jobs\JobController@destroy
        PUT             api/jobs/{job}/status ................... Jobs\JobController@acceptReject
    </p>
    <p class="section">
    <span>Auth:</span>
        POST            api/login ............................... Auth\AuthController@login
        GET|HEAD        api/logout .............................. Auth\AuthController@logout
        POST            api/register ............................ Auth\AuthController@register
        GET|HEAD        api/user ................................ Auth\AuthController@me
        GET|HEAD        sanctum/csrf-cookie ..................... sanctum.csrf-cookie › Laravel\Sanctum › CsrfCookieController@show
    </p>
    <p class="section">
    <span>Server:</span>
        GET|HEAD        up ...................................... Mintor for testing
    </p>
    </pre>
</body>
</html>