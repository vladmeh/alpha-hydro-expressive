```php
<?php

namespace Api;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class ConfigProvider
{
  
    public function __invoke()
    {
        return [
			/*....*/			
            'doctrine' => $this->getDoctrine(),
        ];
    }

    public function getDependencies()
    {
        /*....*/			

    }

    public function getTemplates()
    {
        /*....*/			

    }

    public function getDoctrine()
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Api\Entity' => 'api_entity',
                    ],
                ],
                'api_entity' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [
                        dirname(__DIR__) . '/src/Api/Entity' => './src/Api/Entity',
                    ],
                ],
            ],
        ];
    }
}

```

##### Создание сущностей из существующих таблиц базы данных

> В таблицах базы данных: 
>   * не должны быть поля "enum".
>   * присутствовать первичные ключи

**Генерируем объекты и экспортируем информацию об «аннотации» в ./src/Api/Entity** 

```bash
vendor\bin\doctrine orm:convert-mapping --namespace="Api\Entity\\" --filter="\\Categories$" --force --from-database annotation src
```

> --filter - Regexp выражение, которое фильтрует создаваемые сущности (не имя таблицы)

**Генерируем объекты сущностей и добавляем setter/getter**

> К сожалению, Doctrine не поддерживает поддерживает стандарт PSR-4. Чтобы обойти эту проблему , мы должны переместить вручную из ./src/Api/Entity в ./src/Api/src/Entity.

```bash
vendor\bin\doctrine orm:generate-entities src --generate-annotations=true --filter="\\Manufacture"
```

**Проверяем**

```bash
vendor\bin\doctrine orm:validate-schema --skip-sync
```

> Если `[Database] FAIL - The database schema is not in sync with the current mapping file.`
>
> Смотрим SQL который нужно внести для синхронизации, без измения самой базы данных
> ```bash
> $ vendor\bin\doctrine orm:schema:update --dump-sql
> ```
> обычно это внешние и вторичные ключи, поэтому я ставлю `--skip-sync`

**Repository**

```cmd
vendor\bin\doctrine orm:generate-repositories --filter="\\Categories$" src
```

**Error**

```bash
...'could not find driver'...
```
> Не могу найти указанный в настройках pdo driver .....

1. Проверяем модули подключенные в php `php -m` - должны быть PDO и pdo_mysql
2. Проверяем откуда подгружается сам php `php -r "var_dump(getenv('PHPBIN'));"`
