# WP_Twitter
This will be all in one solution for wordpress theme or plugin creators. Currently, it can be just return tweets in easy way. This library is based on [twitter-api-php](https://github.com/J7mbo/twitter-api-php).

## Installation
You should just copy the wp_twitter folder anywhere in your theme or plugin. Then you should include the WP Twitter class in your file. Like that;

```php
require_once('wp_twitter/WP_Twitter.php');
```

Now, you are ready to go!

## How to Use
After you include the library you can use the main class. For example;

```php
$WP_Twitter = new WP_Twitter($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret);
print_r($WP_Twitter->tweets('twitter_username',(int how many tweets), (int how many minutes for cache)));
```

Thats it! It will return an array of latest tweets.