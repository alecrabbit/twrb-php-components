```bash
docker-compose -f docker-compose-xdebug.yml up -d
```

```bash
docker-compose exec app phpunit --coverage-html tests/coverage/html
```

```bash
docker-compose exec app phpunit --coverage-clover tests/coverage/clover.xml
```
### phpcs
```bash
docker run -it --rm -v $PWD:/app dralec/php-code-sniffer app/src
```
