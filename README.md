# MVC Cars JJPaya

Built with PHP 8, HTML/CSS/AngularJS 1.5 & SQL

## Features
* Search bar with autocomplete
* Shop filters (state stored in URL), order by, Google Maps locations, Shop details with back button
* Carousel with most visited listings on the main page
* Scroll to load more car brands
* Related books from Google Books API for each listing
* Starred items (favorites)
* Shop cart (client sided, localStorage, modal)
* Server sided checkout confirmation (invoices and invoice lines with a copy of the car price)
* Logging in, registering, avatars, mail verification & password reset mail
* Verified social login using firebase (checks idToken)
* Admin controls (with Auth Guard, Creation, Updates and Deletes with validation and modals)

### Other Improvements
* Favorites, Add to Cart and Cart popup as components
* Header and Footer as components
* A lot of server and client side validation
* Custom framework using PHP8.0 features such as types and attributes
* Checkout email receipt (confirmation)
* Email service using MailJet
* User sessions with JWT, current user data in localStorage
* "Remember me" checkbox on local logins
* Toastr popups

## Installation

You will need nginx, PHP 8.0, and the curl & mysql php plugins.
Sample config file for nginx can be found on `backend/private/setup/nginx-config` along with a sample database.

You also need to create a credentials.json file on `backend/private/` with the following format:
```json
{
	"db": {
		"name": "database_name",
		"user": "database_username",
		"pass": "database_password"
	},
	"api": {
		"google": "google_api_key_for_google_books_and_google_maps_js_api",
		"firebase": {
			"apiKey": "Your firebase configuration goes here"
		},
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

The frontend also needs a credentials file in `frontend/config/credentials.js`:
```js
export default {
	api: {
		google: 'Google api key goes here',
		firebase: {
			apiKey: "Firebase configuration goes here"
		}
	}
};
```
