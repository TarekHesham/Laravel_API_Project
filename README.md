<p align="center">
  <a href="https://iti.gov.eg/" target="_blank" rel="noopener noreferrer">
    <img width="150" src="https://shamra-academia.com/uploads/publishers/logoc1ee0a1961b28b92869f371af51313da.png" alt="ITI Logo">
  </a>
</p>

# Laravel API

This project was generated with [Laravel](https://laravel.com/docs) version 11.

- **[MindMap - ERD for DB - Mapping Tables](./Resources)**
- **[FrontEnd](https://github.com/TarekHesham/Laravel_Project)**

## API Document

<div align="center">

![document](./Resources/document.png)
![documentHover](./Resources/documentHover.png)

</div>

## Development server

Run `php artisan serve` for a dev server. Navigate to `http://127.0.0.1:8000/`. The api will automatically reload if you change any of the source files.

## Build
```bash
composer install # Composer will install all dependency resources

php artisan migrate:fresh --seed # Laravel will generate all tables and add data for locations, benefits, skills, categories
php artisan serve # Run service on port 8000
```
- **Don't Forget to add your DB informations in `.env`**
