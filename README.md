# Custom-checkout
Скрипт предоставляет возможность отправлять содержимое корзины пользователю в удобном Вам формате<br>
<br>
Используйте в нужном месте Вашей воронки запрос на этот скипт (на Вашем хостинге)<br>
Пример запроса https://image.mufiksoft.com/chrome_chgnV47AZa.jpg<br>
<br>
```userId``` - идентификатор пользователя<br>
```token``` - токен проекта Smart Sender<br>
```message``` - шаблон текста сообщения<br>
```string``` - шаблон текста каждой строки в корзине<br>
<br>
<br>
Заменяемые параметры шаблона сообщения:<br>
```%checkout%``` - содержимое корзины,<br>
```%stringCount%``` - количество строк в корзине (наименований продуктов),<br>
```%productCount%``` - общее количество единиц продуктов в корзине,<br>
```%sum%``` - общая сума корзины,<br>
```%currency%``` - валюта корзины (валюта последнего товара в корзине)<br>
<br>
<br>
Заменяемые параметры шаблона строки корзины<br>
```%number%``` - порядковый номер строки,<br>
```%product%``` - название продукта,<br>
```%essence%``` - название сущности продукта,<br>
```%price%``` - цена продукта в формате ```1.00```,<br>
```%amount%``` - цена продукта в формате ```1.00 $```,<br>
```%cash%``` - цена продукта в формате ```1```,<br>
```%currency%``` - валюта цены продукта в формате ```USD```,<br>
```%quantity%``` - количество,<br>
```%sum%``` - сума (цена*количество) в формате ```1```<br>
