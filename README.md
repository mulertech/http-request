
# Http Request

___

This package is used to manage the request data.

___

## Installation

###### _Two methods to install Http Request package with composer :_

1.
Add to your "**composer.json**" file into require section :

```
"mulertech/http-request": "^1.0"
```

and run the command :

```
php composer.phar update
```

2.
Run the command :

```
php composer.phar require mulertech/http-request "^1.0"
```

___

## Usage

<br>

###### _Get key in session :_

```
$session = new Session();
$session->get('key');
```

<br>

###### _Has key in session :_

```
$session = new Session();
$session->has('key');
```

<br>

###### _Set key in session :_

```
$session = new Session();
$session->set('key', 'value');
```

<br>

###### _Remove key in session :_

```
$session = new Session();
$session->remove('key');
```

<br>

###### _Add key in session :_
This method is used to add a key in session if it does not exist.<br>
If the key exists, it will be replaced by the new value only if the key is not an array.<br>
If the key is an array, the new value will be added to the array.

```
$session = new Session();
$session->add('key', 'value');
```

<br>

###### _Add sub key in session :_

```
$session = new Session();
$session->add('key', 'value', 'path', 'to', 'subkey');
```

<br>

###### _Remove sub key in session :_

```
$session = new Session();
$session->delete('key');
```

<br>

###### _Remove sub key in session :_

```
$session = new Session();
$session->delete('key', 'path', 'to', 'subkey');
```

<br>

###### _Get cookie data :_

```
HttpRequest::getCookie('key');
```

<br>

###### _Has cookie :_

```

HttpRequest::hasCookie('key');
```

<br>

###### _Get $\_GET data :_

```
HttpRequest::get('key');
```

<br>

###### _Has Get :_

```
HttpRequest::has('key');
```

<br>

###### _Method :_

```
HttpRequest::method();
```

<br>

###### _Get $\_POST data :_

```
HttpRequest::getPost('key');
```

<br>

###### _Has Post :_

```
HttpRequest::hasPost('key');
```

<br>

###### _Get request uri :_

```
HttpRequest::getUri();
```

<br>

###### _Get request url :_

```
HttpRequest::getUrl();
```

<br>

###### _Get post list string :_

```
HttpRequest::getPostListString();
```

<br>

###### _Get post list :_

```
HttpRequest::getPostList();
```

<br>

###### _Get $\_GET list :_

```
HttpRequest::getList();
```