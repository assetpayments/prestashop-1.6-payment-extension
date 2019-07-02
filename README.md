## Prestashop 1.6 payment module

### Installation

* Backup your webstore and database
* Upload the module file assetpayments.zip via Modules -> Module & Services -> Add a new module -> Upload Plugin
* Upload assetpayments.zip 
* Activate the module in Module manager -> Payment & Gateways -> AssetPayments -> Install
* Proceed with the installation 
* Configure the module settings:
  * Merchant Id
  * Secret key
  * Template ID (default = 19)
  * Update settings
  
### Notes
Tested and developed with Prestashop 1.6.14

### Troubleshooting
If you hosting service doesn't provide a FTP access, most probably you will have to install the extension before to install the payment module.

Alternatively you can just upload the upload [assetpayments] directory content to modules/ directory.

## Модуль оплаты Prestashop 1.6

### Установка
* Создайте резервную копию вашего магазина и базы данных
* Загрузите файл модуля assetpayments.zip через Modules -> Module & Services -> Add a new module -> Upload Plugin
* Укажите путь к файлу assetpayments.zip 
* Задайте в настройках модуля:
  * Id магазина
  * Секретный ключ
  * Id шаблона (по-умолчанию = 19)
  * Нажмите кнопку Сохранить изменения

### Примечания
Разработано и протестировано с Prestashop 1.6.14

### Проблемы при установке
Если ваша хостинговая компания не предоставляет FTP доступ, то вам будет необходимо установить этот модуль прежде чем устанавливать данный модуль оплаты.

Другой вариант - это закачать на сервер содержимое папки upload [assetpayments] в директорию modules/.
