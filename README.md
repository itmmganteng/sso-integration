# SSO Intergration

Package ini digunakan untuk melakukan integrasi sistem Single Sign On M Mart.

## Installation

Setelah melakukan pembuatan project laravel, silahkan gunakan langkah-langkah berikut untuk melakukan instalasi:

### Via composer

1. `Cd` ke Project Laravel
2. Install package `itmm/sso` menggunakan perintah:
```
composer require itmm/sso
```
3. Daftarkan Provider Berikut pada `config/app.php`
```
'providers' => [
    ...,
    Itmm\Sso\SsoServiceProvider::class,
]
```
4. Jalankan perintah berikut pada command line 
```
composer dump-autoload
```
atau
```
composer du
```
5. Publish component package `itmm/sso` menggunakan perintah:
```
php artisan vendor:publish --tag=sso-components --force
```
6. Tambahkan script berikut untuk mendaftarkan middleware `VerifySso` dan `AuthenticatedSso` pada `app/Http/Kernel.php`
```
protected $routeMiddleware = [
    ...
    'verified.sso' => \App\Http\Middleware\VerifySso::class,
    'authenticated.sso' => \App\Http\Middleware\AuthenticatedSso::class,
];
```
7. `SSO APP` (Optional) Jalankan perintah berikut pada command line jika belum membuat keys passport pada SSO APP
```
php artisan passport:keys
```
8. `SSO APP` Jalankan perintah berikut pada command line untuk membuat client
```
php artisan passport:client
```
9. `SSO APP` Ikuti intruksi berikut saat menjalakan command line step ke 7
```
User Id: # Bisa di skip dengan tekan "Enter" langsung
Name: [nama-aplikasi] # Contoh: M Mart Application
Redirect Request: [http:// or https://][url-aplikasi]/callback # Contoh: https://application.m-mart.co.id/callback
```
10. `SSO APP` Setelah mengikuti intruksi tersebut akan muncul `client id` dan `client secret` seperti dibawah. Simpan `client id` dan `client secret` untuk digunakan pada `.env` file pada step ke 12
```
New client created successfully.
Client ID: 1
Client secret: ********************
```
11. `SSO APP` Perbarui data client yang baru saja di buat pada database. Perbarui pada kolom `app_id` menjadi id dari aplikasi anda pada SSO
12. `SSO APP` Pastikan tipe login dari aplikasi sudah diperbarui ke menggunakan passport
13. Pada file `.env` tambahkan beberapa environment berikut yang berisikan credential SSO
```
SSO_URL=[sso-url] # Contoh: https://sso.m-mart.co.id
SSO_API_URL=[sso-api-url] # Contoh: https://sso.m-mart.co.id
SSO_CLIENT_ID=[passport-client-id]
SSO_CLIENT_SECRET=[passport-client-secret]
```
14. Pada file `.env` tambahkan beberapa environment berikut yang berisikan credential Aplikasi
```
APP_ID=[app-id-dari-sso]
APP_CALLBACK_URL=[app-callback-url] # Contoh: https://application.m-mart.co.id/callback
APP_LOGIN_URL=[app-auth-url] # Contoh: https://application.m-mart.co.id/sso/auth
APP_ROUTE_HOME_NAME=[home-route-name] # Contoh: home
```
15. Jalankan script berikut untuk menyegarkan cache pada aplikasi
```
php artisan optimize:clear
```