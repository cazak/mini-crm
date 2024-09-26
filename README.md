## Запуск

```shell
git clone git@github.com:cazak/mini-crm.git your-folder-name

cd ./your-folder-name/

make dc_build
make dc_up
```

Предварительно настроить `./docker/.env`, `./docker/mysql/.env`

## Запустить миграции

```shell
php yii migrate --migrationPath=@yii/rbac/migrations
php yii migrate
```

## Создать админа

```shell
php yii create-admin/index admin example@mail.com password
```
