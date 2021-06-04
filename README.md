# MVC Cars JJPaya

Built with PHP 8, HTML/CSS/AngularJS 1.5 & SQL

## Features
* Search bar with autocomplete
* Shop filters, order by, Google Maps locations
* Carousel with most visited listings on the main page
* Scroll to load more car brands
* Related books from Google Books API for each listing
* Starred items
* Shop cart
* Logging in, registering, avatars, mail verification & password reset mail
* Admin controls

## Installation

You will need nginx, PHP 8.0, and the curl & mysql php plugins.
Sample config file for nginx can be found on `private/setup/nginx-config` along with a sample database.

You also need to create a credentials.json file on `private/` with the following format:
```json
{
	"db": {
		"name": "database_name",
		"user": "database_username",
		"pass": "database_password"
	},
	"api": {
		"google": "google_api_key_for_google_books_and_google_maps_js_api",
		"mailjet": {
			"email": "sender_email_for_web_mails",
			"user": "user_mailjet_token",
			"pass": "pass_mailjet_token"
		}
	},
	"jwt": {
		"secret": "jwt_secret_key"
	}
}
```
