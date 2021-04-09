# services-configurator

### Instalation

```bash
# Clone project
git clone https://github.com/creatortsv/services-configurator.git

# Create local env & configure it
composer dump-env dev

# Install dependencies
composer install
composer update
```
---

### Create your services
You need to create services classes with implementation of
```php
Creatortsv\ServicesConfiguratorBundle\Services\ServiceInterface
```

### Add them into the config file
For example: **config/services.yaml**
```yaml
services:

  # ... default configuration
  
  # name alias of your service
  app.api.service:
    # Class name
    class: App\Services\ApiConnectionService
    # Required
    public: true
    # Any arguments if you need it
    arguments:
      - 'https://%env(resolve:SERVICE_API_HOST)%:%env(resolve:SERVICE_API_PORT)%/'
```
