# Chapter 04 Examples

The examples at this package are for the Chapter 4 of this book. In order to run it you just need to do the following steps:

- Step 1: make sure you have composer available in your environment (you can go to these [instructions](https://getcomposer.org/doc/00-intro.md) if not).

- Step 2: run the command at the root directory of this example:

```shell
composer install
```

- Step 3: prepare the `.env` file with sensitive information, example:
`.env` file content:

```
SERVER_ADDRESS=127.0.0.1
SERVER_PORT=9503
```

- Step 4: run each server and verify the results according to the chapter, as an example, to run the first HTTP server you would run:

```shell
php http_server.php
```